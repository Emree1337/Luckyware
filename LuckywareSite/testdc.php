<?php
// API Configuration
$API_KEY = 'rBCnF9Q5fa3zCzqj73nmL1AKV5pJOfKp';
$BASE_URL = 'https://osintdog.com';

// Session management for async searches
session_start();

// API Endpoint Mapping with detailed configuration
$API_ENDPOINTS = [
    'email' => [
        ['name' => 'Universal Search', 'endpoint' => '/api/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'KeyScore', 'endpoint' => '/api/keyscore/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'Dump.cat', 'endpoint' => '/api/dumpcat/search/init', 'method' => 'POST', 'async' => true, 'priority' => 1],
        ['name' => 'Snusbase', 'endpoint' => '/api/snusbase/search', 'method' => 'POST', 'priority' => 2],
        ['name' => 'LeakCheck v2', 'endpoint' => '/api/leakcheck/v2', 'method' => 'POST', 'priority' => 1],
        ['name' => 'HackCheck', 'endpoint' => '/api/hackcheck', 'method' => 'POST', 'priority' => 2],
        ['name' => 'BreachBase', 'endpoint' => '/api/breachbase', 'method' => 'POST', 'priority' => 2],
        ['name' => 'IntelVault', 'endpoint' => '/api/intelvault', 'method' => 'POST', 'priority' => 2],
        ['name' => 'IdLeakCheck', 'endpoint' => '/api/idleakcheck', 'method' => 'POST', 'priority' => 3],
        ['name' => 'Akula', 'endpoint' => '/api/akula', 'method' => 'POST', 'priority' => 2],
        ['name' => 'LeakSight', 'endpoint' => '/api/leaksight', 'method' => 'POST', 'priority' => 2],
        ['name' => 'SEON Email', 'endpoint' => '/api/seon/email', 'method' => 'GET', 'priority' => 3],
        ['name' => 'OathNet Holehe', 'endpoint' => '/api/oathnet/holehe', 'method' => 'POST', 'priority' => 3],
        ['name' => 'OathNet GHunt', 'endpoint' => '/api/oathnet/ghunt', 'method' => 'POST', 'priority' => 3],
        ['name' => 'INF0SEC Leaks', 'endpoint' => '/api/inf0sec/leaks', 'method' => 'GET', 'priority' => 2],
        ['name' => 'INF0SEC Discord', 'endpoint' => '/api/inf0sec/discord', 'method' => 'GET', 'priority' => 3]
    ],
    'username' => [
        ['name' => 'Universal Search', 'endpoint' => '/api/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'KeyScore', 'endpoint' => '/api/keyscore/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'Dump.cat', 'endpoint' => '/api/dumpcat/search/init', 'method' => 'POST', 'async' => true, 'priority' => 1],
        ['name' => 'Snusbase', 'endpoint' => '/api/snusbase/search', 'method' => 'POST', 'priority' => 2],
        ['name' => 'LeakCheck v2', 'endpoint' => '/api/leakcheck/v2', 'method' => 'POST', 'priority' => 1],
        ['name' => 'HackCheck', 'endpoint' => '/api/hackcheck', 'method' => 'POST', 'priority' => 2],
        ['name' => 'BreachBase', 'endpoint' => '/api/breachbase', 'method' => 'POST', 'priority' => 2],
        ['name' => 'Akula', 'endpoint' => '/api/akula', 'method' => 'POST', 'priority' => 2],
        ['name' => 'LeakSight', 'endpoint' => '/api/leaksight', 'method' => 'POST', 'priority' => 2],
        ['name' => 'INF0SEC Username', 'endpoint' => '/api/inf0sec/username', 'method' => 'GET', 'priority' => 3],
        ['name' => 'Room 101 Reddit', 'endpoint' => '/api/room101/user/{value}', 'method' => 'GET', 'priority' => 3],
        ['name' => 'OathNet Minecraft', 'endpoint' => '/api/oathnet/minecraft-history', 'method' => 'POST', 'priority' => 3]
    ],
    'phone' => [
        ['name' => 'Universal Search', 'endpoint' => '/api/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'KeyScore', 'endpoint' => '/api/keyscore/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'Snusbase', 'endpoint' => '/api/snusbase/search', 'method' => 'POST', 'priority' => 2],
        ['name' => 'LeakCheck v2', 'endpoint' => '/api/leakcheck/v2', 'method' => 'POST', 'priority' => 1],
        ['name' => 'LeakSight', 'endpoint' => '/api/leaksight', 'method' => 'POST', 'priority' => 2],
        ['name' => 'SEON Phone', 'endpoint' => '/api/seon/phone', 'method' => 'GET', 'priority' => 3],
        ['name' => 'INF0SEC HLR', 'endpoint' => '/api/inf0sec/hlr', 'method' => 'GET', 'priority' => 3],
        ['name' => 'IdLeakCheck', 'endpoint' => '/api/idleakcheck', 'method' => 'POST', 'priority' => 3]
    ],
    'domain' => [
        ['name' => 'Universal Search', 'endpoint' => '/api/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'KeyScore', 'endpoint' => '/api/keyscore/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'Dump.cat Reverse', 'endpoint' => '/api/dumpcat/reverse/init', 'method' => 'POST', 'async' => true, 'priority' => 1],
        ['name' => 'Snusbase', 'endpoint' => '/api/snusbase/search', 'method' => 'POST', 'priority' => 2],
        ['name' => 'LeakCheck v2', 'endpoint' => '/api/leakcheck/v2', 'method' => 'POST', 'priority' => 1],
        ['name' => 'Akula', 'endpoint' => '/api/akula', 'method' => 'POST', 'priority' => 2],
        ['name' => 'LeakSight', 'endpoint' => '/api/leaksight', 'method' => 'POST', 'priority' => 2],
        ['name' => 'INF0SEC Domain', 'endpoint' => '/api/inf0sec/domain', 'method' => 'GET', 'priority' => 3]
    ],
    'ip' => [
        ['name' => 'Universal Search', 'endpoint' => '/api/search', 'method' => 'POST', 'priority' => 1],
        ['name' => 'KeyScore IP', 'endpoint' => '/api/keyscore/iplookup', 'method' => 'POST', 'priority' => 1],
        ['name' => 'Snusbase IP WHOIS', 'endpoint' => '/api/snusbase/ip-whois', 'method' => 'POST', 'priority' => 2],
        ['name' => 'LeakCheck v2', 'endpoint' => '/api/leakcheck/v2', 'method' => 'POST', 'priority' => 1],
        ['name' => 'LeakSight', 'endpoint' => '/api/leaksight', 'method' => 'POST', 'priority' => 2]
    ],
    'hash' => [
        ['name' => 'KeyScore Hash', 'endpoint' => '/api/keyscore/hashlookup', 'method' => 'POST', 'priority' => 1],
        ['name' => 'Snusbase Hash', 'endpoint' => '/api/snusbase/hash-lookup', 'method' => 'POST', 'priority' => 1],
        ['name' => 'LeakCheck v2', 'endpoint' => '/api/leakcheck/v2', 'method' => 'POST', 'priority' => 2]
    ],
    'name' => [
        ['name' => 'IdLeakCheck', 'endpoint' => '/api/idleakcheck', 'method' => 'POST', 'priority' => 1],
        ['name' => 'INF0SEC NPD', 'endpoint' => '/api/inf0sec/npd', 'method' => 'GET', 'priority' => 1],
        ['name' => 'Rutify Name', 'endpoint' => '/api/rutify/name', 'method' => 'POST', 'priority' => 2]
    ],
    'discord_id' => [
        ['name' => 'OathNet Discord->Roblox', 'endpoint' => '/api/oathnet/discord-to-roblox', 'method' => 'POST', 'priority' => 1],
        ['name' => 'INF0SEC Discord', 'endpoint' => '/api/inf0sec/discord', 'method' => 'GET', 'priority' => 1]
    ],
    'roblox' => [
        ['name' => 'OathNet Roblox', 'endpoint' => '/api/oathnet/roblox-userinfo', 'method' => 'POST', 'priority' => 1]
    ],
    'steam' => [
        ['name' => 'OathNet Steam', 'endpoint' => '/api/oathnet/steam-userinfo', 'method' => 'POST', 'priority' => 1]
    ],
    'xbox' => [
        ['name' => 'OathNet Xbox', 'endpoint' => '/api/oathnet/xbox-userinfo', 'method' => 'POST', 'priority' => 1]
    ],
    'rut' => [
        ['name' => 'Rutify RUT', 'endpoint' => '/api/rutify/rut', 'method' => 'POST', 'priority' => 1],
        ['name' => 'Rutify SII', 'endpoint' => '/api/rutify/sii', 'method' => 'POST', 'priority' => 1]
    ],
    'plate' => [
        ['name' => 'Rutify Car', 'endpoint' => '/api/rutify/car', 'method' => 'POST', 'priority' => 1]
    ],
    'reddit' => [
        ['name' => 'Room 101 Analysis', 'endpoint' => '/api/room101/analyze/{value}', 'method' => 'GET', 'params' => 'model=gpt-4&latest=true&sources=true', 'priority' => 1],
        ['name' => 'Room 101 User', 'endpoint' => '/api/room101/user/{value}', 'method' => 'GET', 'params' => 'latest=true', 'priority' => 2],
        ['name' => 'Room 101 Search', 'endpoint' => '/api/room101/search', 'method' => 'GET', 'priority' => 3]
    ],
    'subreddit' => [
        ['name' => 'Room 101 Subreddit', 'endpoint' => '/api/room101/subreddit/{value}', 'method' => 'GET', 'priority' => 1]
    ]
];

// Function to build request body based on endpoint
function buildRequestBody($endpoint, $searchType, $searchValue) {
    $body = [];
    
    // Universal Search
    if (strpos($endpoint, '/api/search') !== false) {
        $body = ['field' => [[$searchType => $searchValue]]];
    }
    // KeyScore
    elseif (strpos($endpoint, '/api/keyscore/search') !== false) {
        $types = $searchType;
        if ($searchType === 'name') $types = 'email'; // Fallback
        $body = [
            'terms' => [$searchValue],
            'types' => [$types],
            'source' => 'xkeyscore',
            'wildcard' => false,
            'regex' => false,
            'operator' => 'OR',
            'page' => 1,
            'pagesize' => 10000
        ];
    }
    elseif (strpos($endpoint, '/api/keyscore/iplookup') !== false) {
        $body = ['ip' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/keyscore/hashlookup') !== false) {
        $body = ['hash' => $searchValue];
    }
    // Dump.cat
    elseif (strpos($endpoint, '/api/dumpcat/search/init') !== false) {
        $body = [
            'term' => $searchValue,
            'sort' => 2
        ];
    }
    elseif (strpos($endpoint, '/api/dumpcat/reverse/init') !== false) {
        $url = $searchValue;
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        $body = ['term' => $url];
    }
    // Snusbase
    elseif (strpos($endpoint, '/api/snusbase/search') !== false) {
        $body = [
            'terms' => [$searchValue],
            'types' => [$searchType],
            'wildcard' => false,
            'group_by' => 'db',
            'tables' => null
        ];
    }
    elseif (strpos($endpoint, '/api/snusbase/ip-whois') !== false) {
        $body = ['ip' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/snusbase/hash-lookup') !== false) {
        $body = ['hash' => $searchValue];
    }
    // LeakCheck
    elseif (strpos($endpoint, '/api/leakcheck') !== false) {
        $body = [
            'term' => $searchValue,
            'search_type' => $searchType,
            'limit' => 1000,
            'offset' => 0
        ];
    }
    // HackCheck & BreachBase
    elseif (strpos($endpoint, '/api/hackcheck') !== false || strpos($endpoint, '/api/breachbase') !== false) {
        $body = [
            'term' => $searchValue,
            'search_type' => $searchType
        ];
    }
    // IntelVault
    elseif (strpos($endpoint, '/api/intelvault') !== false) {
        $body = ['field' => [[$searchType => $searchValue]]];
    }
    // IdLeakCheck
    elseif (strpos($endpoint, '/api/idleakcheck') !== false) {
        if ($searchType === 'name') {
            $nameParts = explode(' ', $searchValue, 2);
            $body = [
                'firstName' => $nameParts[0] ?? '',
                'lastName' => $nameParts[1] ?? '',
                'sources' => ['npd']
            ];
        } elseif ($searchType === 'phone') {
            $body = [
                'phone1' => $searchValue,
                'sources' => ['npd']
            ];
        } elseif ($searchType === 'email') {
            $body = [
                'email' => $searchValue,
                'sources' => ['npd']
            ];
        }
    }
    // Akula
    elseif (strpos($endpoint, '/api/akula') !== false) {
        $body = [
            'searchTerm' => $searchValue,
            'search_type' => $searchType
        ];
    }
    // LeakSight
    elseif (strpos($endpoint, '/api/leaksight') !== false) {
        $st = $searchType;
        if ($searchType === 'ip') $st = 'ipgeo';
        $body = [
            'term' => $searchValue,
            'search_type' => $st
        ];
    }
    // Rutify
    elseif (strpos($endpoint, '/api/rutify/name') !== false) {
        $body = ['name' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/rutify/car') !== false) {
        $body = ['plate' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/rutify') !== false) {
        $body = ['rut' => $searchValue];
    }
    // OathNet
    elseif (strpos($endpoint, '/api/oathnet/holehe') !== false) {
        $body = ['email' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/oathnet/ghunt') !== false) {
        $body = ['email' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/oathnet/roblox-userinfo') !== false) {
        $body = ['username' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/oathnet/discord-to-roblox') !== false) {
        $body = ['discord_id' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/oathnet/steam-userinfo') !== false) {
        $body = ['steam_id' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/oathnet/xbox-userinfo') !== false) {
        $body = ['xbl_id' => $searchValue];
    }
    elseif (strpos($endpoint, '/api/oathnet/minecraft-history') !== false) {
        $body = ['username' => $searchValue];
    }
    
    return $body;
}

// Function to make API calls
function makeAPICall($api, $searchType, $searchValue) {
    global $API_KEY, $BASE_URL;
    
    $endpoint = $api['endpoint'];
    $method = $api['method'];
    
    // Replace placeholders
    $endpoint = str_replace('{value}', urlencode($searchValue), $endpoint);
    
    $url = $BASE_URL . $endpoint;
    
    // Add query parameters for GET requests
    if ($method === 'GET') {
        $params = [];
        
        if (isset($api['params'])) {
            $url .= '?' . $api['params'];
        } elseif (strpos($endpoint, '/api/seon/email') !== false) {
            $url .= '?email=' . urlencode($searchValue);
        } elseif (strpos($endpoint, '/api/seon/phone') !== false) {
            $url .= '?phone=' . urlencode($searchValue);
        } elseif (strpos($endpoint, '/api/inf0sec') !== false) {
            if (strpos($endpoint, '/npd') !== false && $searchType === 'name') {
                $nameParts = explode(' ', $searchValue, 2);
                $url .= '?firstname=' . urlencode($nameParts[0] ?? '');
                if (isset($nameParts[1])) {
                    $url .= '&lastname=' . urlencode($nameParts[1]);
                }
            } elseif (strpos($endpoint, '/api/room101/search') !== false) {
                $url .= '?terms=' . urlencode($searchValue);
            } else {
                $url .= '?q=' . urlencode($searchValue);
            }
        }
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-Key: ' . $API_KEY,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    if ($method === 'POST') {
        $body = buildRequestBody($endpoint, $searchType, $searchValue);
        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error, 'http_code' => $httpCode];
    }
    
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['raw_response' => $response, 'http_code' => $httpCode];
    }
    
    // Store async search IDs in session
    if (isset($api['async']) && $api['async'] && isset($decoded['search_id'])) {
        $_SESSION['async_searches'][] = [
            'api' => $api['name'],
            'search_id' => $decoded['search_id'],
            'type' => 'dumpcat',
            'timestamp' => time()
        ];
    } elseif (isset($api['async']) && $api['async'] && isset($decoded['lookup_id'])) {
        $_SESSION['async_searches'][] = [
            'api' => $api['name'],
            'lookup_id' => $decoded['lookup_id'],
            'type' => 'dumpcat_reverse',
            'timestamp' => time()
        ];
    }
    
    return ['data' => $decoded, 'http_code' => $httpCode];
}

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'search') {
        $searchType = $_POST['type'] ?? '';
        $searchValue = $_POST['value'] ?? '';
        
        if (empty($searchType) || empty($searchValue)) {
            echo json_encode(['error' => 'Missing search type or value']);
            exit;
        }
        
        $results = [];
        $endpoints = $API_ENDPOINTS[$searchType] ?? [];
        
        // Sort by priority
        usort($endpoints, function($a, $b) {
            return ($a['priority'] ?? 99) - ($b['priority'] ?? 99);
        });
        
        foreach ($endpoints as $api) {
            $result = makeAPICall($api, $searchType, $searchValue);
            $results[] = [
                'name' => $api['name'],
                'result' => $result,
                'async' => $api['async'] ?? false,
                'priority' => $api['priority'] ?? 99
            ];
        }
        
        echo json_encode(['results' => $results]);
        exit;
    }
    
    if ($_POST['action'] === 'check_async') {
        $asyncResults = [];
        
        if (isset($_SESSION['async_searches'])) {
            foreach ($_SESSION['async_searches'] as $key => $async) {
                // Check if search is older than 10 seconds
                if (time() - $async['timestamp'] > 10) {
                    if ($async['type'] === 'dumpcat') {
                        // Get Dump.cat results
                        $url = $BASE_URL . '/api/dumpcat/search/results';
                        $body = json_encode([
                            'search_id' => $async['search_id'],
                            'limit' => 100,
                            'offset' => 0
                        ]);
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'X-API-Key: ' . $API_KEY,
                            'Content-Type: application/json'
                        ]);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        
                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        
                        $data = json_decode($response, true);
                        if ($data && $httpCode === 200) {
                            $asyncResults[] = [
                                'api' => $async['api'],
                                'data' => $data
                            ];
                            unset($_SESSION['async_searches'][$key]);
                        }
                    } elseif ($async['type'] === 'dumpcat_reverse') {
                        // Get Dump.cat reverse results
                        $url = $BASE_URL . '/api/dumpcat/reverse/results/' . $async['lookup_id'] . '?limit=100';
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'X-API-Key: ' . $API_KEY
                        ]);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        
                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        
                        $data = json_decode($response, true);
                        if ($data && $httpCode === 200) {
                            $asyncResults[] = [
                                'api' => $async['api'],
                                'data' => $data
                            ];
                            unset($_SESSION['async_searches'][$key]);
                        }
                    }
                }
            }
            $_SESSION['async_searches'] = array_values($_SESSION['async_searches']);
        }
        
        echo json_encode(['results' => $asyncResults]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSINT Professional Intelligence Platform</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #141414;
            --bg-tertiary: #1a1a1a;
            --accent: #667eea;
            --accent-secondary: #764ba2;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --text-primary: #f0f0f0;
            --text-secondary: #a0a0a0;
            --border: rgba(255, 255, 255, 0.1);
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 20% 80%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(118, 75, 162, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(102, 126, 234, 0.05) 0%, transparent 50%);
            animation: bgPulse 20s ease-in-out infinite;
        }
        
        @keyframes bgPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeInDown 0.8s ease;
        }
        
        .header h1 {
            font-size: 3em;
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        
        .header .subtitle {
            color: var(--text-secondary);
            font-size: 1.2em;
            font-weight: 300;
        }
        
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-value {
            font-weight: 700;
            color: var(--accent);
        }
        
        /* Search Panel */
        .search-panel {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
            animation: fadeIn 0.8s ease;
        }
        
        .search-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 25px;
            margin-bottom: 25px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        .form-group select,
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            background: var(--bg-tertiary);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1em;
            transition: all 0.3s ease;
        }
        
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .form-group option {
            background: var(--bg-tertiary);
            padding: 10px;
        }
        
        .search-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-secondary) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            position: relative;
            overflow: hidden;
        }
        
        .search-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.5s ease;
        }
        
        .search-btn:hover::before {
            left: 100%;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }
        
        .search-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        
        .quick-btn {
            padding: 10px 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9em;
        }
        
        .quick-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
            transform: translateY(-2px);
        }
        
        /* Results Section */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .results-header h2 {
            font-size: 1.8em;
            font-weight: 700;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
        }
        
        .filter-btn {
            padding: 8px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85em;
        }
        
        .filter-btn.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        
        .results-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 25px;
            animation: fadeInUp 0.5s ease;
        }
        
        .api-result {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease;
        }
        
        .api-result:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            border-color: var(--accent);
        }
        
        .api-header {
            padding: 20px;
            background: var(--bg-tertiary);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .api-name {
            font-size: 1.2em;
            font-weight: 700;
            color: var(--accent);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }
        
        .status-error {
            background: rgba(239, 68, 68, 0.2);
            color: var(--error);
            border: 1px solid var(--error);
        }
        
        .status-loading {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        .api-meta {
            padding: 15px 20px;
            background: rgba(102, 126, 234, 0.05);
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 20px;
            font-size: 0.9em;
            color: var(--text-secondary);
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .result-content {
            max-height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.85em;
            line-height: 1.6;
        }
        
        .result-content::-webkit-scrollbar {
            width: 10px;
        }
        
        .result-content::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }
        
        .result-content::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 5px;
        }
        
        .result-stats {
            padding: 15px 20px;
            background: var(--bg-tertiary);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            font-size: 0.85em;
        }
        
        /* Loading Animation */
        .loader {
            text-align: center;
            padding: 60px;
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            position: relative;
        }
        
        .spinner::before,
        .spinner::after {
            content: '';
            position: absolute;
            border: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1.5s linear infinite;
        }
        
        .spinner::before {
            width: 100%;
            height: 100%;
            border-top-color: var(--accent);
            animation-duration: 1s;
        }
        
        .spinner::after {
            width: 75%;
            height: 75%;
            top: 12.5%;
            left: 12.5%;
            border-bottom-color: var(--accent-secondary);
            animation-duration: 0.75s;
            animation-direction: reverse;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 20px 25px;
            border-radius: 12px;
            animation: slideInRight 0.5s ease;
            z-index: 1000;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .notification.success {
            background: var(--success);
            color: white;
        }
        
        .notification.error {
            background: var(--error);
            color: white;
        }
        
        .notification.info {
            background: var(--accent);
            color: white;
        }
        
        /* JSON Syntax Highlighting */
        .json-key { color: #f59e0b; }
        .json-string { color: #10b981; }
        .json-number { color: #3b82f6; }
        .json-boolean { color: #8b5cf6; }
        .json-null { color: #ef4444; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .search-grid {
                grid-template-columns: 1fr;
            }
            
            .results-container {
                grid-template-columns: 1fr;
            }
            
            .stats-bar {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>
    
    <div class="container">
        <div class="header">
            <h1>OSINT Intelligence Platform</h1>
            <p class="subtitle">Professional Multi-Source Intelligence Aggregation</p>
            <div class="stats-bar">
                <div class="stat-item">
                    <span>APIs:</span>
                    <span class="stat-value">25+</span>
                </div>
                <div class="stat-item">
                    <span>Data Sources:</span>
                    <span class="stat-value">15+</span>
                </div>
                <div class="stat-item">
                    <span>Status:</span>
                    <span class="stat-value">Operational</span>
                </div>
            </div>
        </div>
        
        <div class="search-panel">
            <form id="searchForm">
                <div class="search-grid">
                    <div class="form-group">
                        <label for="searchType">Data Type</label>
                        <select id="searchType" name="type" required>
                            <option value="">Select...</option>
                            <optgroup label="Common Searches">
                                <option value="email">Email Address</option>
                                <option value="username">Username</option>
                                <option value="phone">Phone Number</option>
                                <option value="domain">Domain</option>
                                <option value="ip">IP Address</option>
                            </optgroup>
                            <optgroup label="Advanced">
                                <option value="hash">Hash (MD5/SHA)</option>
                                <option value="name">Full Name</option>
                            </optgroup>
                            <optgroup label="Social Media">
                                <option value="reddit">Reddit Username</option>
                                <option value="discord_id">Discord ID</option>
                                <option value="roblox">Roblox Username</option>
                                <option value="steam">Steam ID</option>
                                <option value="xbox">Xbox Gamertag</option>
                                <option value="subreddit">Subreddit</option>
                            </optgroup>
                            <optgroup label="Regional">
                                <option value="rut">Chilean RUT</option>
                                <option value="plate">Vehicle Plate (Chile)</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="searchValue">Search Value</label>
                        <input type="text" id="searchValue" name="value" placeholder="Enter search value..." required autocomplete="off">
                    </div>
                </div>
                
                <button type="submit" class="search-btn">
                    <span id="btnText">Execute Search</span>
                </button>
            </form>
        </div>
        
        <div class="quick-actions">
            <button class="quick-btn" onclick="setExample('email', 'example@gmail.com')">Example Email</button>
            <button class="quick-btn" onclick="setExample('username', 'johndoe')">Example Username</button>
            <button class="quick-btn" onclick="setExample('phone', '+1234567890')">Example Phone</button>
            <button class="quick-btn" onclick="setExample('domain', 'example.com')">Example Domain</button>
            <button class="quick-btn" onclick="checkAsyncResults()">Check Async Results</button>
        </div>
        
        <div id="resultsSection" style="display: none;">
            <div class="results-header">
                <h2>Search Results</h2>
                <div class="filter-buttons">
                    <button class="filter-btn active" onclick="filterResults('all')">All</button>
                    <button class="filter-btn" onclick="filterResults('success')">Success</button>
                    <button class="filter-btn" onclick="filterResults('error')">Errors</button>
                    <button class="filter-btn" onclick="filterResults('pending')">Pending</button>
                </div>
            </div>
            
            <div id="resultsContainer" class="results-container"></div>
        </div>
    </div>
    
    <script>
        let asyncCheckInterval;
        let currentResults = [];
        
        function formatJSON(obj) {
            if (typeof obj !== 'object') return obj;
            return JSON.stringify(obj, null, 2)
                .replace(/(".*?")/g, '<span class="json-key">$1</span>')
                .replace(/: "([^"]*)"/g, ': <span class="json-string">"$1"</span>')
                .replace(/: (\d+)/g, ': <span class="json-number">$1</span>')
                .replace(/: (true|false)/g, ': <span class="json-boolean">$1</span>')
                .replace(/: null/g, ': <span class="json-null">null</span>');
        }
        
        function setExample(type, value) {
            document.getElementById('searchType').value = type;
            document.getElementById('searchValue').value = value;
        }
        
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideInRight 0.5s ease reverse';
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }
        
        function filterResults(filter) {
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            document.querySelectorAll('.api-result').forEach(card => {
                if (filter === 'all') {
                    card.style.display = '';
                } else if (filter === 'success') {
                    card.style.display = card.dataset.status === 'success' ? '' : 'none';
                } else if (filter === 'error') {
                    card.style.display = card.dataset.status === 'error' ? '' : 'none';
                } else if (filter === 'pending') {
                    card.style.display = card.dataset.status === 'pending' ? '' : 'none';
                }
            });
        }
        
        async function checkAsyncResults() {
            const formData = new FormData();
            formData.append('action', 'check_async');
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.results && data.results.length > 0) {
                    data.results.forEach(result => {
                        updateAsyncResult(result.api, result.data);
                    });
                    showNotification(`Updated ${data.results.length} async results`, 'success');
                }
            } catch (error) {
                console.error('Error checking async results:', error);
            }
        }
        
        function updateAsyncResult(apiName, data) {
            const cards = document.querySelectorAll('.api-result');
            cards.forEach(card => {
                const nameEl = card.querySelector('.api-name');
                if (nameEl && nameEl.textContent === apiName) {
                    card.dataset.status = 'success';
                    card.querySelector('.status-badge').className = 'status-badge status-success';
                    card.querySelector('.status-badge').textContent = 'Success';
                    card.querySelector('.result-content').innerHTML = `<pre>${formatJSON(data)}</pre>`;
                }
            });
        }
        
        document.getElementById('searchForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const searchType = document.getElementById('searchType').value;
            const searchValue = document.getElementById('searchValue').value;
            
            if (!searchType || !searchValue) {
                showNotification('Please select a data type and enter a search value', 'error');
                return;
            }
            
            document.getElementById('resultsSection').style.display = 'block';
            document.getElementById('resultsContainer').innerHTML = 
                '<div class="loader"><div class="spinner"></div><p>Searching across multiple intelligence sources...</p></div>';
            
            const submitBtn = document.querySelector('.search-btn');
            submitBtn.disabled = true;
            document.getElementById('btnText').textContent = 'Searching...';
            
            clearInterval(asyncCheckInterval);
            
            try {
                const formData = new FormData();
                formData.append('action', 'search');
                formData.append('type', searchType);
                formData.append('value', searchValue);
                
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.error) {
                    showNotification(`Error: ${data.error}`, 'error');
                    document.getElementById('resultsContainer').innerHTML = '';
                } else if (data.results) {
                    displayResults(data.results);
                    showNotification(`Search completed! Found ${data.results.length} API results.`, 'success');
                    
                    // Check for async searches
                    const hasAsync = data.results.some(r => r.async);
                    if (hasAsync) {
                        showNotification('Async searches initiated. Checking for results...', 'info');
                        asyncCheckInterval = setInterval(checkAsyncResults, 10000);
                    }
                }
            } catch (error) {
                showNotification(`Error: ${error.message}`, 'error');
                document.getElementById('resultsContainer').innerHTML = '';
            } finally {
                submitBtn.disabled = false;
                document.getElementById('btnText').textContent = 'Execute Search';
            }
        });
        
        function displayResults(results) {
            const container = document.getElementById('resultsContainer');
            container.innerHTML = '';
            currentResults = results;
            
            results.forEach((api, index) => {
                const resultCard = document.createElement('div');
                resultCard.className = 'api-result';
                resultCard.style.animationDelay = `${index * 0.1}s`;
                
                let statusBadge = '';
                let statusClass = '';
                let content = '';
                let resultCount = 0;
                
                if (api.result.error) {
                    statusClass = 'error';
                    statusBadge = '<span class="status-badge status-error">Error</span>';
                    content = `<div style="color: var(--error);">Error: ${api.result.error}</div>`;
                } else if (api.result.http_code === 200) {
                    if (api.async) {
                        statusClass = 'pending';
                        statusBadge = '<span class="status-badge status-loading">Pending</span>';
                        content = `<div style="color: var(--warning);">Async search initiated. Will check for results automatically...</div>`;
                        if (api.result.data) {
                            content += `<pre>${formatJSON(api.result.data)}</pre>`;
                        }
                    } else {
                        statusClass = 'success';
                        statusBadge = '<span class="status-badge status-success">Success</span>';
                        const data = api.result.data || api.result.raw_response || {};
                        
                        // Count results
                        if (data.results && Array.isArray(data.results)) {
                            resultCount = data.results.length;
                        } else if (data.data && Array.isArray(data.data)) {
                            resultCount = data.data.length;
                        } else if (data.found !== undefined) {
                            resultCount = data.found;
                        }
                        
                        content = `<pre>${formatJSON(data)}</pre>`;
                    }
                } else {
                    statusClass = 'error';
                    statusBadge = `<span class="status-badge status-error">HTTP ${api.result.http_code}</span>`;
                    content = `<pre>${formatJSON(api.result.data || api.result.raw_response || {})}</pre>`;
                }
                
                resultCard.dataset.status = statusClass;
                
                resultCard.innerHTML = `
                    <div class="api-header">
                        <span class="api-name">${api.name}</span>
                        ${statusBadge}
                    </div>
                    <div class="api-meta">
                        <div class="meta-item">
                            <span>Priority:</span>
                            <span style="color: var(--accent);">${api.priority || 99}</span>
                        </div>
                        <div class="meta-item">
                            <span>Type:</span>
                            <span>${api.async ? 'Async' : 'Sync'}</span>
                        </div>
                        ${resultCount > 0 ? `
                        <div class="meta-item">
                            <span>Results:</span>
                            <span style="color: var(--success);">${resultCount}</span>
                        </div>` : ''}
                    </div>
                    <div class="result-content">${content}</div>
                    <div class="result-stats">
                        <span>Response Code: ${api.result.http_code || 'N/A'}</span>
                        <span>${new Date().toLocaleTimeString()}</span>
                    </div>
                `;
                
                container.appendChild(resultCard);
            });
        }
        
        // Update placeholder based on selected type
        document.getElementById('searchType').addEventListener('change', function() {
            const input = document.getElementById('searchValue');
            const placeholders = {
                'email': 'example@domain.com',
                'username': 'johndoe123',
                'phone': '+1234567890 or 1234567890',
                'domain': 'example.com',
                'ip': '192.168.1.1 or 8.8.8.8',
                'hash': '5f4dcc3b5aa765d61d8327deb882cf99',
                'name': 'John Doe',
                'reddit': 'spez',
                'discord_id': '123456789012345678',
                'roblox': 'builderman',
                'steam': '76561197960435530',
                'xbox': 'MajorNelson',
                'subreddit': 'AskReddit',
                'rut': '12345678-9',
                'plate': 'ABCD12'
            };
            
            input.placeholder = placeholders[this.value] || 'Enter search value...';
        });
        
        // Stop async checking when page unloads
        window.addEventListener('beforeunload', () => {
            clearInterval(asyncCheckInterval);
        });
    </script>
</body>
</html>