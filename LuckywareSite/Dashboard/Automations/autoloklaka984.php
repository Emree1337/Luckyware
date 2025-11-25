<?php
session_start();


// Set content type to JSON
header('Content-Type: application/json');

// Validate session data
$role = $_SESSION['Role'] ?? '';
$secretkey = $_SESSION['secret_key'] ?? '';

/**
 * Validate string length
 */
function isStringLengthGreaterThan5($inputString) {
    return is_string($inputString) && strlen($inputString) > 5;
}

/**
 * Enhanced input sanitization with strict validation
 */
function sanitizeInput($input) {
    if (!is_string($input) || empty($input)) {
        return '';
    }
    
    // Define forbidden characters and SQL keywords
    $forbiddenChars = ['(', ')', '=', '*', ';', '--', '/*', '*/', 'union', 'select', 'insert', 'update', 'delete', 'drop'];
    
    $input_lower = strtolower($input);
    
    // Check for forbidden characters and SQL keywords
    foreach ($forbiddenChars as $char) {
        if (strpos($input_lower, strtolower($char)) !== false) {
            return '';
        }
    }
    
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate user permissions
 */
function validateUserPermissions($role, $secretkey) {
    if (empty($role) || empty($secretkey)) {
        return false;
    }
    
    $validRoles = ['Founder', 'Admin', 'Special', 'Premium', 'Trial', 'User'];
    return in_array($role, $validRoles);
}

/**
 * Create secure database connection using PDO
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

/**
 * Truncate text for display
 */
function truncateText($text, $maxLength = 45) {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
    }
    return $text;
}

// Main execution
try {
    // Validate HTTP method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Validate session and permissions
    if (!validateUserPermissions($role, $secretkey) || !isStringLengthGreaterThan5($secretkey)) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied - Invalid session or insufficient permissions']);
        exit;
    }
    
    // Create database connection
    $pdo = createDatabaseConnection();
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
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
        if (!empty($sanitizedSearch) && strlen($sanitizedSearch) > 0) {
            $searchValue = substr($sanitizedSearch, 0, 100); // Limit to 100 chars for security
        }
    }
    
    // Define sortable columns (whitelist approach)
    $sortableColumns = [
        0 => 'ActionID',
        1 => 'ActionInfo',
        2 => 'ActionSentTime',
        3 => 'ActionID' // For buttons column, sort by ActionID
    ];
    
    // Validate sorting parameters
    $orderColumnIndex = filter_input(INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $orderColumn = 'ActionID'; // Default sort column
    $orderDirection = 'DESC'; // Default sort direction
    
    if (is_array($orderColumnIndex) && isset($orderColumnIndex[0]['column']) && isset($orderColumnIndex[0]['dir'])) {
        $columnIndex = intval($orderColumnIndex[0]['column']);
        $direction = strtoupper($orderColumnIndex[0]['dir']);
        
        if (isset($sortableColumns[$columnIndex]) && in_array($direction, ['ASC', 'DESC'])) {
            $orderColumn = $sortableColumns[$columnIndex];
            $orderDirection = $direction;
        }
    }
    
    // Build base query with role-based filtering
    $baseWhere = "WHERE ActionSentTime LIKE :download_filter";
    $params = ['download_filter' => '%Download%'];
    
    // Role-based filtering
    if ($role !== "Founder") {
        $baseWhere .= " AND ActionOwner = :owner_id";
        $params['owner_id'] = $secretkey;
    }
    
    // Add search conditions if search value exists
    $searchWhere = "";
    if (!empty($searchValue)) {
        $searchWhere = " AND (
            ActionInfo LIKE :search1 OR
            ActionStatus LIKE :search2 OR
            ActionType LIKE :search3
        )";
        $searchPattern = '%' . $searchValue . '%';
        $params['search1'] = $searchPattern;
        $params['search2'] = $searchPattern;
        $params['search3'] = $searchPattern;
    }
    
    // Build the main data query
    $dataQuery = "SELECT ActionID, ActionInfo, ActionSentTime, ActionStatus, ActionType
                  FROM actions 
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
    foreach ($results as $row) {
        // Sanitize and truncate action info
        $actionInfo = htmlspecialchars($row['ActionInfo'] ?? '', ENT_QUOTES, 'UTF-8');
        $truncatedInfo = truncateText($actionInfo, 45);
        
        // Sanitize other fields
        $actionId = intval($row['ActionID']);
        $actionSentTime = htmlspecialchars($row['ActionSentTime'] ?? '', ENT_QUOTES, 'UTF-8');
        
        // Generate secure delete button
        $buttonsHtml = '
            <div class="pt-2">
                <button class="bgCol px-4 py-1 rounded text-stone-300" onclick="deleteRecord(' . $actionId . ')">
                    <i class="fa-solid fa-trash miAuto text-sm"></i>
                </button>
            </div>
        ';
        
        $data[] = [
            $actionId,
            $truncatedInfo,
            $actionSentTime,
            $buttonsHtml
        ];
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total 
                   FROM actions 
                   $baseWhere $searchWhere";
    
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
    error_log("Actions DataTables Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'draw' => $draw ?? 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'An error occurred while processing your request'
    ]);
}
?>