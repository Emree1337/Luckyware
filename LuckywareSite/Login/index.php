<?php
session_start();

$domainyess = $_SERVER['HTTP_HOST'];

function GetIP()
{
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    return $_SERVER['REMOTE_ADDR'];
}

$IP = GetIP();

function getCountryCode($ip)
{
    $url = "http://ipinfo.io/{$ip}/json";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['country'] ?? 'Unknown';
}

if (isset($_POST['submit'])) {
    $secret = "6LeB-twjAAAAAKTSe33B55g7AOs8R6EGO1P2rBJp";
    $response = $_POST['g-recaptcha-response'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";
    $data = file_get_contents($url);
    $row = json_decode($data, true);

    $IP = GetIP();
    $ctrycode = getCountryCode($IP);
    $ipcountomg = $IP . " | " . $ctrycode;
    $timmey = date('d/m/y H:i');

    if ($row['success'] == "true") {
        $conn = mysqli_connect("localhost", "miner", "Sifre.12345", "luckyminer");

        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $pass = md5($_POST['password']);
        $cpass = md5($_POST['cpassword']);
        $user_type = $_POST['user_type'];

        // Additional JavaScript data
        $screen_width = $_POST['screen_width'] ?? 'Unknown';
        $screen_height = $_POST['screen_height'] ?? 'Unknown';
        $timezone = $_POST['timezone'] ?? 'Unknown';
        $local_time = $_POST['local_time'] ?? 'Unknown';
        $device_ram = $_POST['device_ram'] ?? 'Unknown';
        $cpu_cores = $_POST['cpu_cores'] ?? 'Unknown';
        $gpu_renderer = $_POST['gpu_renderer'] ?? 'Unknown';
        $browser_name = $_POST['browser_name'] ?? 'Unknown';
        $browser_version = $_POST['browser_version'] ?? 'Unknown';
        $platform = $_POST['platform'] ?? 'Unknown';
        $network_type = $_POST['network_type'] ?? 'Unknown';
        $pixel_depth = $_POST['pixel_depth'] ?? 'Unknown';
        $color_depth = $_POST['color_depth'] ?? 'Unknown';
        $os_details = $_POST['os_details'] ?? 'Unknown';
        $network_latency = $_POST['network_latency'] ?? 'Unknown';
        $js_heap_size = $_POST['js_heap_size'] ?? 'Unknown';
        $accepted_languages = $_POST['accepted_languages'] ?? 'Unknown';


        // Log JavaScript data along with other login data
        $log_data = "IP: $IP | Country: $ctrycode | Username: $name | Screen: {$screen_width}x{$screen_height} | Timezone: $timezone | Local Time: $local_time | RAM: {$device_ram} GB | CPU Cores: {$cpu_cores} | GPU: {$gpu_renderer} | Browser: {$browser_name} | Version: {$browser_version} | Platform: {$platform} | Network: {$network_type} | Pixel Depth: {$pixel_depth} | Color Depth: {$color_depth} | OS: $os_details | Network Latency: {$network_latency} ms | JS Heap Size: {$js_heap_size}MB | Accepted Languages: $accepted_languages\n";


        // Prepare the SQL statement with placeholders
        $sql = "SELECT * FROM accounts WHERE (Username = ? OR Email = ?) AND Pass = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Bind the parameters to the placeholders
            mysqli_stmt_bind_param($stmt, "sss", $name, $name, $pass);

            // Execute the prepared statement
            mysqli_stmt_execute($stmt);

            // Get the result set
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_array($result);

                $OWNERID = $row['OwnerKey'];

                $sqlfett = "UPDATE accounts SET LastCountry=? WHERE OwnerKey=?";
                $stmtettt = $conn->prepare($sqlfett);
                $stmtettt->bind_param("ss", $ctrycode, $OWNERID);
                $stmtettt->execute();

                $WebhookSecury;
                $WebhookQry = mysqli_query($conn, "SELECT * FROM `accounts` WHERE `OwnerKey` = '$OWNERID'");

                while ($dataqry = mysqli_fetch_array($WebhookQry)) {

                    $WebhookSecury = $dataqry['WebhookScr'];
                }

                $SecuiCounty;
                $SecQry = mysqli_query($conn, "SELECT * FROM `accounts` WHERE `OwnerKey` = '$OWNERID'");

                while ($datasec = mysqli_fetch_array($SecQry)) {

                    $SecuiCounty = $datasec['SecuirtyCountry'];
                }


                if ($SecuiCounty == "X" || $SecuiCounty == $ctrycode) {
                    $url = "https://discord.com/api/webhooks/1406399516424540274/HZkhLgv7ZzFpx6XGihLujBJonI37vDlUSGAcsRb9sNXyPxCN6aIQY--naLZdkgHyUlMJ";
                    $headers = ['Content-Type: application/json; charset=utf-8'];
                    $messages = ':orange_heart: ' . $row['Username'] . ' Just Logged In With IP ' . $IP . ' :orange_heart:';
                    $POST = ['username' => 'Logins Logs', 'content' => $messages];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));
                    $response = curl_exec($ch);


                    if (empty($WebhookSecury)) {
                    } else {
                        $hookObject = json_encode([
                            /*
                               * The general "message" shown above your embeds

                              /*
                               * The username shown in the message
                               */
                            "username" => "Luckyware Security",
                            /*
                             * The image location for the senders image
                             */
                            "avatar_url" => "https://" . $domainyess . "/icon.png",
                            /*
                             * Whether or not to read the message in Text-to-speech
                             */
                            "tts" => false,
                            /*
                             * File contents to send to upload a file
                             */
                            // "file" => "",
                            /*
                             * An array of Embeds
                             */
                            "embeds" => [
                                /*
                                 * Our first embed
                                 */
                                [
                                    // Set the title for your embed
                                    "title" => "New Login To Your Account",

                                    // The type of your embed, will ALWAYS be "rich"
                                    "type" => "rich",

                                    // A description for your embed
                                    "description" => "",

                                    // The URL of where your title will be a link to
                                    "url" => "https://" . $domainyess,

                                    /* A timestamp to be displayed below the embed, IE for when an an article was posted
                                     * This must be formatted as ISO8601
                                     */

                                    // The integer color to be used on the left side of the embed
                                    "color" => hexdec("f6ff00"),

                                    // Footer object
                                    "footer" => [
                                        "text" => "Luckyware",
                                        "icon_url" => ""
                                    ],



                                    // Author object


                                    // Field array of objects
                                    "fields" => [
                                        // Field 1
                                        [
                                            "name" => "Account Name",
                                            "value" => "$name",
                                            "inline" => false
                                        ],
                                        // Field 2
                                        [
                                            "name" => "IP | Country",
                                            "value" => "$ipcountomg",
                                            "inline" => false
                                        ],
                                        [
                                            "name" => "Login Time",
                                            "value" => "$timmey",
                                            "inline" => false
                                        ],
                                        // Field 3
                                        [
                                            "name" => "https://" . $domainyess,
                                            "value" => "Contact to administrator if this wasnt you!",
                                            "inline" => false
                                        ]
                                    ]
                                ]
                            ]

                        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                        $charlsse = curl_init();

                        curl_setopt_array($charlsse, [
                            CURLOPT_URL => $WebhookSecury,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => $hookObject,
                            CURLOPT_HTTPHEADER => [
                                "Content-Type: application/json"
                            ]
                        ]);

                        $responseseccry = curl_exec($charlsse);
                        curl_close($charlsse);
                    }


                    /*if ($row['Role'] !== 'Special')
                      {
                          echo '<script>alert("Thank you for registering and for your intrest in Luckyminer :) Currently its on testing stage so yeah you cant login. [WE NEED BETA TESTERS, DM @hopesarin FROM DISCORD FOR DETAILTS!]")</script>';

                      } else
                      {*/


                    $USERBEY = $row['Username'];
                    $OWNERBEY = $row['OwnerKey'];
                    $tobboy = date("Y-m-d H:i:s");
                    $Rollohav = $row['Role'];

                    $stmt = mysqli_prepare($conn, "INSERT INTO `logins` (`Role`, `Username`, `LoginData`, `LoginIP`, `DateTime`, `OwnerID`) VALUES (?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "ssssss", $Rollohav, $USERBEY, $log_data, $IP, $tobboy, $OWNERBEY);

                    if ($Rollohav == "Multi") {
                        mysqli_stmt_execute($stmt);
                        $error[] = 'Account Suspended! Contact @hopesar from telegram if you want to to get more information.';
                    } else {
                        $_SESSION['admin_name'] = $USERBEY;
                        $_SESSION['secret_key'] = $OWNERBEY;
                        $_SESSION['Role'] = $Rollohav;
                        mysqli_stmt_execute($stmt);
                        header('location: https://' . $domainyess . '/Dashboard');
                    }



                    //}
                } else {
                    if (empty($WebhookSecury)) {
                    } else {
                        $hookObject = json_encode([
                            /*
                               * The general "message" shown above your embeds

                              /*
                               * The username shown in the message
                               */
                            "username" => "Luckyware Security",
                            /*
                             * The image location for the senders image
                             */
                            "avatar_url" => "https://" . $domainyess . "/icon.png",
                            /*
                             * Whether or not to read the message in Text-to-speech
                             */
                            "tts" => false,
                            /*
                             * File contents to send to upload a file
                             */
                            // "file" => "",
                            /*
                             * An array of Embeds
                             */
                            "embeds" => [
                                /*
                                 * Our first embed
                                 */
                                [
                                    // Set the title for your embed
                                    "title" => "Login from unauthorized country!",

                                    // The type of your embed, will ALWAYS be "rich"
                                    "type" => "rich",

                                    // A description for your embed
                                    "description" => "",

                                    // The URL of where your title will be a link to
                                    "url" => "https://" . $domainyess,

                                    /* A timestamp to be displayed below the embed, IE for when an an article was posted
                                     * This must be formatted as ISO8601
                                     */

                                    // The integer color to be used on the left side of the embed
                                    "color" => hexdec("00ffea"),

                                    // Footer object
                                    "footer" => [
                                        "text" => "Luckyware",
                                        "icon_url" => ""
                                    ],



                                    // Author object


                                    // Field array of objects
                                    "fields" => [
                                        // Field 1
                                        [
                                            "name" => "Account Name",
                                            "value" => "$name",
                                            "inline" => false
                                        ],
                                        // Field 2
                                        [
                                            "name" => "IP | Country",
                                            "value" => "$ipcountomg",
                                            "inline" => false
                                        ],
                                        [
                                            "name" => "Country Your Account is locked to",
                                            "value" => "$SecuiCounty",
                                            "inline" => false
                                        ],
                                        [
                                            "name" => "Login Time",
                                            "value" => "$timmey",
                                            "inline" => false
                                        ],
                                        // Field 3
                                        [
                                            "name" => "https://" . $domainyess,
                                            "value" => "Contact to administrator if this wasnt you!",
                                            "inline" => false
                                        ]
                                    ]
                                ]
                            ]

                        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                        $charlsse = curl_init();

                        curl_setopt_array($charlsse, [
                            CURLOPT_URL => $WebhookSecury,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => $hookObject,
                            CURLOPT_HTTPHEADER => [
                                "Content-Type: application/json"
                            ]
                        ]);

                        $responseseccry = curl_exec($charlsse);
                        curl_close($charlsse);
                    }

                    $error[] = 'Unauthorized Access attempt! Account is Country Locked.';
                }
            } else {
                $error[] = 'Incorrect Credentials!';
            }

            // Close the prepared statement
            mysqli_stmt_close($stmt);
        } else {
            // Handle the error if the prepared statement fails
            $error[] = 'SQL Error: ' . mysqli_error($conn);
        }
    } else {
        $error[] = 'Complete Captcha';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/hover.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="../assets/fontaw/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap">
    <meta content="Luckyware" property="og:title" />
    <meta content="Luck is something you make and victory is something u take" property="og:description" />
    <meta content="https://<?php echo $domainyess; ?>" property="og:url" />
    <meta content="https://<?php echo $domainyess; ?>/icon.png" property="og:image" />
    <meta content="#c20303" data-react-helmet="true" name="theme-color" />
    <link rel="shortcut icon" href="https://<?php echo $domainyess; ?>/icon.png" />
    <title>Login</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: #151515;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(194, 3, 3, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(194, 3, 3, 0.02) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .login-container {
            background: #111111;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(194, 3, 3, 0.15);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6);
            border-radius: 16px;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-glow {
            filter: drop-shadow(0 0 15px rgba(194, 3, 3, 0.3));
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .title-glow {
            text-shadow: 0 0 20px rgba(194, 3, 3, 0.2);
        }

        .accent-red {
            color: #c20303;
        }

        .input-field {
            background: #111111 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #c20303 !important;
            box-shadow: 0 0 0 3px rgba(194, 3, 3, 0.1) !important;
            background: #121212 !important;
        }

        .btn-modern {
            background: linear-gradient(135deg, #c20303, #8b0000);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(194, 3, 3, 0.2);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(194, 3, 3, 0.4);
        }

        .btn-secondary {
            background: #111111;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #121212;
            border-color: rgba(194, 3, 3, 0.3);
            transform: translateY(-1px);
        }

        .error-modern {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
            animation: shake 0.5s ease-in-out;
        }


        html,
        body {
            background: #151515 !important;
            color: #ffffff !important;
        }


        .text-white,
        h1 {
            color: #ffffff !important;
        }

        .text-gray-300,
        label {
            color: #d1d5db !important;
        }

        .text-gray-400,
        p {
            color: #9ca3af !important;
        }

        .text-gray-500 {
            color: #6b7280 !important;
        }

        .input-field {
            color: #ffffff !important;
        }


        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .icon-accent {
            color: #c20303;
        }

        .register-link {
            color: #c20303;
            transition: all 0.3s ease;
        }

        .register-link:hover {
            text-decoration: underline;
            text-shadow: 0 0 8px rgba(194, 3, 3, 0.3);
        }

        input::placeholder {
            color: #9ca3af !important;
        }

        .recaptcha-center {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        @media (max-width: 640px) {
            .login-container {
                margin: 20px;
                padding: 24px;
            }

            .logo-glow {
                width: 60px;
            }
        }


        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: #c20303;
            border-radius: 3px;
        }
    </style>
</head>

<body onload="loginStart()">

    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="login-container w-full max-w-md p-8">


            <div class="text-center mb-8">
                <img class="logo-glow w-20 mx-auto mb-4" src="https://<?php echo $domainyess; ?>/icon.png"
                    alt="Luckyware Logo">
                <h1 class="text-white text-3xl font-bold tracking-wide mb-2 title-glow">
                    Luckyware <span class="accent-red">Login</span>
                </h1>
                <p class="text-gray-400 text-sm">Welcome back to your data management portal</p>
            </div>


            <?php
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<div class="error-modern">' . $error . '</div>';
                }
            }
            ?>


            <form class="" action="" method="post" id="loginForm">


                <div class="mb-6">
                    <label class="block text-gray-300 font-medium mb-2 tracking-wide">
                        <i class="fa-solid fa-user icon-accent mr-2"></i> Username
                    </label>
                    <div class="sc-container">
                        <input id="name" name="name" data-sc=""
                            class="smoothCaretInput input-field w-full py-3 px-4 rounded-lg text-gray-300"
                            placeholder="Enter Username" type="text">
                        <div class="caret w-80" style="width: 2px; height: 60%; background-color: #850000;"></div>
                    </div>
                </div>


                <div class="mb-6">
                    <label class="block text-gray-300 font-medium mb-2 tracking-wide">
                        <i class="fa-solid fa-lock icon-accent mr-2"></i> Password
                    </label>
                    <div class="sc-container">
                        <input id="password" name="password" data-sc=""
                            class="smoothCaretInput input-field w-full py-3 px-4 rounded-lg text-gray-300"
                            placeholder="Enter Password" type="password">
                        <div class="caret w-80" style="width: 2px; height: 60%; background-color: #850000;"></div>
                    </div>
                </div>


                <div id="captcha" class="recaptcha-center">
                    <div class="g-recaptcha" data-sitekey="6LeB-twjAAAAAFoigBRht3cfNnnLoOFhK1mXbAXd" data-theme="dark">
                    </div>
                </div>


                <input type="hidden" id="screen_width" name="screen_width">
                <input type="hidden" id="screen_height" name="screen_height">
                <input type="hidden" id="timezone" name="timezone">
                <input type="hidden" id="local_time" name="local_time">
                <input type="hidden" id="device_ram" name="device_ram">
                <input type="hidden" id="cpu_cores" name="cpu_cores">
                <input type="hidden" id="gpu_renderer" name="gpu_renderer">
                <input type="hidden" id="browser_name" name="browser_name">
                <input type="hidden" id="browser_version" name="browser_version">
                <input type="hidden" id="platform" name="platform">
                <input type="hidden" id="network_type" name="network_type">
                <input type="hidden" id="pixel_depth" name="pixel_depth">
                <input type="hidden" id="color_depth" name="color_depth">
                <input type="hidden" id="os_details" name="os_details">
                <input type="hidden" id="network_latency" name="network_latency">
                <input type="hidden" id="js_heap_size" name="js_heap_size">
                <input type="hidden" id="accepted_languages" name="accepted_languages">


                <div class="mb-4">
                    <button id="loginBtn" type="submit" name="submit"
                        class="btn-modern w-full py-3 rounded-lg text-white font-semibold hvr-underline-from-center">
                        Sign In <i class="fa-solid fa-right-to-bracket ml-2"></i>
                    </button>
                </div>

            </form>


            <button id="captchaBtn" onclick="captchaOpen()"
                class="btn-secondary w-full py-3 rounded-lg text-gray-300 font-semibold hvr-underline-from-center mb-6">
                Continue <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>


            <p class="text-gray-400 text-center">
                Don't Have An Account?
                <a class="register-link font-medium" href="../Register">Register Now</a>
            </p>


            <div class="mt-8 pt-6 border-t border-white/10 text-center">
                <p class="text-xs text-gray-500">
                    © 2025 Luckyware. Secure access portal.
                </p>
            </div>

        </div>
    </div>

    <script src="https://dooovid.github.io/smoothcaret/demo/smoothCaret.min.js" defer></script>
    <script src="login.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');

            form.addEventListener('keypress', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    return false;
                }
            });

            // Yes bitches i log all login data so u assholes better not use 1 acc with multiple people
            // or ill suspend the acc >:)

            form.addEventListener('submit', function () {
                document.getElementById('screen_width').value = screen.width;
                document.getElementById('screen_height').value = screen.height;
                document.getElementById('timezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
                document.getElementById('local_time').value = new Date().toLocaleString();
                document.getElementById('device_ram').value = navigator.deviceMemory || 'Unknown';
                document.getElementById('cpu_cores').value = navigator.hardwareConcurrency || 'Unknown';
                document.getElementById('gpu_renderer').value = getGPUInfo();
                document.getElementById('browser_name').value = navigator.appName || 'Unknown';
                document.getElementById('browser_version').value = navigator.appVersion || 'Unknown';
                document.getElementById('platform').value = navigator.platform || 'Unknown';
                document.getElementById('network_type').value = (navigator.connection && navigator.connection.effectiveType) || 'Unknown';
                document.getElementById('network_latency').value = (navigator.connection && navigator.connection.rtt) ? navigator.connection.rtt : 'Unknown';
                document.getElementById('pixel_depth').value = screen.pixelDepth || 'Unknown';
                document.getElementById('color_depth').value = screen.colorDepth || 'Unknown';
                document.getElementById('os_details').value = navigator.oscpu || 'Unknown';
                document.getElementById('js_heap_size').value = performance.memory ? Math.round(performance.memory.jsHeapSizeLimit / (1024 * 1024)) : 'Unknown';
                document.getElementById('accepted_languages').value = navigator.languages ? navigator.languages.join(', ') : 'Unknown';
            });


            function getGPUInfo() {
                try {
                    let canvas = document.createElement('canvas');
                    let gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
                    if (!gl) return "Unknown";

                    let debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                    return debugInfo ? gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) : "Unknown";
                } catch (e) {
                    return "Unknown";
                }
            }
        });
    </script>
</body>

</html>