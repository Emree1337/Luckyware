<?php

session_start();
$domainyess = $_SERVER['HTTP_HOST'];

$secretkey = $_SESSION['secret_key'];
$username = $_SESSION['admin_name'];
$role = $_SESSION['Role'];
if (!isset($_SESSION['admin_name'])) {
    header('location:../Login/');
} else {

    $conn = mysqli_connect("localhost", "miner", "Sifre.12345", "luckyminer");

    /*if ($role != 'Founder' || $role != 'Trial') {
        header('location:../Dashboard/');
    }*/
}

function startsWith($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}


if (isset($_POST["save"])) {
    $license = $_POST['webhook'];

    if (startsWith($license, "LUCKY-")) {


        $sqllic = "SELECT * FROM licenses WHERE OwnerID = 'notused' AND UsedTime = 'notused' AND License =?";
        $stmtlic = $conn->prepare($sqllic);
        $stmtlic->bind_param("s", $license);
        $stmtlic->execute();
        $resultlic = $stmtlic->get_result();

        if ($resultlic->num_rows > 0) {

            $unxtime = time();


            $sqlup = "UPDATE licenses SET OwnerID=?, UsedTime=? WHERE License=?";
            $stmtup = $conn->prepare($sqlup);
            $stmtup->bind_param("sss", $secretkey, $unxtime, $license);
            $stmtup->execute();


            $url = "https://discord.com/api/webhooks/1407709090985017344/6JBg1MMxrKuSWlmiNDYIQi9acOJdBAUcbTWd2AqR-7kikFGblTx2iNB8KvrIcNcu3Vqg";
            $headers = ['Content-Type: application/json; charset=utf-8'];
            $messages = ':orange_heart: ' . $username . ' Just Reedemed license ' . $license . ' :orange_heart:';
            $POST = ['username' => 'Logins Logs', 'content' => $messages];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));
            $response = curl_exec($ch);



            if (startsWith($license, 'LUCKY-P')) {

                $sql = "UPDATE accounts SET Role='Premium' WHERE OwnerKey=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $secretkey);

                $_SESSION["Role"] = "Premium";
            }

            if (startsWith($license, 'LUCKY-S')) {

                $sql = "UPDATE accounts SET Role='Special' WHERE OwnerKey=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $secretkey);

                $_SESSION["Role"] = "Special";
            }




            if ($stmt->execute()) {
                header('location:../Dashboard/');
            } else {
                header('location:../Dashboard/');
            }
        } else {
            echo "<script type='text/javascript'>alert('Invalid License!');</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Invalid License Format!');</script>";
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Dashboard/assets/css/custom.css">
    <link rel="stylesheet" href="../Dashboard/assets/css/style.css">
    <link rel="stylesheet" href="../Dashboard/assets/css/hover.css">
    <meta content="LuckyCharm" property="og:title" />
    <meta content="Luck is something you make and victory is something u take" property="og:description" />
    <meta content="https://<?php echo $domainyess; ?>" property="og:url" />
    <meta content="https://<?php echo $domainyess; ?>/icon.png" property="og:image" />
    <meta content="#c20303" data-react-helmet="true" name="theme-color" />
    <link rel="shortcut icon" href="https://<?php echo $domainyess; ?>/icon.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/1f62ce11c7.js" crossorigin="anonymous"></script>
    <script src="https://dooovid.github.io/smoothcaret/demo/smoothCaret.min.js" defer></script>
    <link href="https://cdn.sell.app/embed/style.css" rel="stylesheet" />
    <script src="https://shoppy.gg/api/embed.js"></script>
    <script src="https://cdn.sell.app/embed/script.js" type="module">
    </script>
    <title>Purchase</title>

    <style>
        .spacer-row {
            height: 3px;
            line-height: 0;
            padding: 0;
            margin: 0;
            background: transparent;
        }

        .spacer-row td {
            padding: 0 !important;
            height: 3px;
            line-height: 0;
            border: none !important;
            background: transparent !important;
            margin: 0;
        }

        #DashTable tr.spacer-row+tr {
            border-spacing: 0;
        }

        #DashTable tr:not(.spacer-row) {
            border-spacing: inherit;
        }

        .extra-spacing {
            margin-bottom: 15px;
            display: table-row;
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
            transition: all 1s ease;
        }

        .loading-overlay.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid #333;
            border-radius: 50%;
            border-top-color: #c20303;
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
        }

        #DashTable td:last-child {
            border-radius: 0 0.5rem 0.5rem 0;
        }

        #DashTable tbody tr:hover {
            background-color: var(--main-hover-color);
            color: #D6D3D1;
        }

        .hover-link {
            color: #111111;
            transition: color 0.3s ease-in-out;
        }

        .hover-link:hover {
            color: #f54b42;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #151515;
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

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        /* Force basic styles for mobile compatibility */
        html,
        body {
            background: #111111 !important;
            color: #ffffff !important;
            font-family: 'Inter', sans-serif;
        }

        /* Force text colors for mobile */
        .text-stone-300,
        h1 {
            color: #d1d5db !important;
        }

        .text-stone-400,
        h3 {
            color: #9ca3af !important;
        }

        /* Mobile responsiveness */
        @media (max-width: 1024px) {
            .grid.xl\:grid-cols-2 {
                grid-template-columns: 1fr !important;
                gap: 2rem !important;
            }

            .text-5xl {
                font-size: 2.5rem !important;
            }
        }

        @media (max-width: 768px) {
            .grid-cols-3 {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }

            .content {
                margin-left: 1rem !important;
                margin-right: 1rem !important;
            }

            .text-5xl {
                font-size: 2rem !important;
            }

            .text-xl {
                font-size: 1.125rem !important;
            }

            #DashTable {
                font-size: 0.875rem !important;
            }

            #DashTable th,
            #DashTable td {
                padding: 8px !important;
            }
        }

        @media (max-width: 640px) {
            .mt-40 {
                margin-top: 2rem !important;
            }

            .gap-20 {
                gap: 2rem !important;
            }

            .text-5xl {
                font-size: 1.75rem !important;
            }
        }

        .dataTables_info,
        .dataTables_length,
        .dataTables_paginate,
        .dataTables_filter {
            color: #a8a29e !important;
            margin-bottom: 7px;
        }

        .dataTables_length,
        .dataTables_paginate {
            margin-top: 7px;
        }

        .dataTables_length select {
            background-color: #111111 !important;
            color: #d6d3d1 !important;
            border: 1px solid #181818 !important;
            padding: 5px;
        }

        .dataTables_paginate a,
        .dataTables_paginate span,
        .dataTables_paginate .ellipsis {
            background-color: #111111;
            color: #d6d3d1 !important;
            border: none;
            padding: 5px 10px;
            margin: 2px;
            cursor: pointer;
        }

        .dataTables_paginate a:hover {
            background-color: #181818;
            border: none;
        }

        .dataTables_paginate .current {
            background-color: #850000;
            color: #d6d3d1 !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_filter input {
            background-color: var(--background);
            color: #ffffff;
            border: 1px solid #181818;
            border-radius: 5px;
            padding: 5px;
        }

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
        }


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


</head>

<body onload="purchaseStart()" class="bg pb-5">
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="grid xl:grid-cols-2 content mt-40 gap-20">
        <div class="1">
            <h1 class="text-stone-300 fntBold tracking-wide font-semibold text-5xl customLeading"><span
                    class="textCol">Luckyware </span> Subscriptions</h1>
            <h3 class="text-stone-400 fnt tracking-wide text-xl mt-5 leading-8"> Down bellow you can see the diffrence
                between plans. If you have trouble purchasing DM<span class="textCol"> @hoppysar</span> from Discord
                or<span class="textCol"> @hopesar</span> from Telegram.</h3>
        </div>
        <div id="container" class="2 dg rounded-lg pb-5 h-max">
            <div class="mx-5 pt-3">
                <h1 class="text-stone-400 fntBold tracking-wider text-lg"><i
                        class="fa-solid fa-money-bill textCol mr-1"></i> Pricing</h1>
                <hr class="mt-2 brdBg">

                <div id="openBtns" class="grid grid-cols-3 gap-5 mt-5">
                    <div class="1">
                        <button onclick="purchaseBtns()"
                            class="bg w-full rounded-lg py-7 btn hvr-underline-from-center">
                            <i id="monthlyIcon" class="fa-solid fa-money-bill text-stone-400 text-xl icon"></i>
                            <h1 class="fntBold text-stone-400 tracking-wide text-xl">Purchase</h1>
                        </button>
                    </div>
                    <div class="2">
                        <button onclick="redeem()" class="bg w-full rounded-lg py-7 btn hvr-underline-from-center">
                            <i id="triMonthlyIcon" class="fa-solid fa-key text-stone-400 text-xl icon"></i>
                            <h1 class="fntBold text-stone-400 tracking-wide text-xl">Redeem License</h1>
                        </button>
                    </div>
                    <div class="3">
                        <button onclick="window.location.href = '../Dashboard';"
                            class="bg w-full rounded-lg py-7 btn hvr-underline-from-center">
                            <i id="lifetimeIcon" class="fa-solid fa-arrow-right text-stone-400 text-xl icon"></i>
                            <h1 class="fntBold text-stone-400 tracking-wide text-xl">Dashboard</h1>
                        </button>

                    </div>
                </div>

                <div id="purchaseBtns" class="grid grid-cols-3 gap-5 mt-5">
                    <div class="1">
                        <button onclick="Pre()" class="bg w-full rounded-lg py-7 btn hvr-underline-from-center">
                            <i id="monthlyIcon" class="fa-solid fa-coins text-stone-400 text-xl icon"></i>
                            <h1 class="fntBold text-stone-400 tracking-wide text-xl">Premium</h1>
                        </button>
                    </div>
                    <div class="2">
                        <button onclick="Spc()" class="bg w-full rounded-lg py-7 btn hvr-underline-from-center">
                            <i id="triMonthlyIcon" class="fa-solid fa-vault text-stone-400 text-xl icon"></i>
                            <h1 class="fntBold text-stone-400 tracking-wide text-xl">Special</h1>
                        </button>
                    </div>
                    <div class="3">
                        <button onclick="window.location.href = '../Dashboard';"
                            class="bg w-full rounded-lg py-7 btn hvr-underline-from-center">
                            <i id="lifetimeIcon" class="fa-solid fa-arrow-right text-stone-400 text-xl icon"></i>
                            <h1 class="fntBold text-stone-400 tracking-wide text-xl">Dashboard</h1>
                        </button>

                    </div>
                </div>

                <div id="redeem" class="mt-5">
                    <form method="post">
                        <label class="text-stone-400 fntBold tracking-wide text-lg" for="redeemCode"><i
                                class="fa-solid fa-key text-stone-400 text-base mr-1 icon"></i> Enter Redeem
                            Code</label>
                        <div class="sc-container mt-2">
                            <input id="webhook" type="text" name="webhook"
                                class="smoothCaretInput rounded-lg py-3 px-3 text-stone-400 bg" placeholder=""
                                type="text">
                            <div class="caret bgCol" style="width: 2px; height: 60%;"></div>
                        </div>
                        <button id="save" type="submit" name="save"
                            class="w-full bg text-center py-2 rounded-lg hvr-underline-from-center mt-4 text-stone-400 fntBold tracking-wide btn"><i
                                class="fa-solid fa-right-to-bracket mr-1 icon"></i> Redeem Now</button>
                    </form>
                </div>

                <div id="pricingBox" class="mt-5 text-center">
                    <div class="grid grid-cols-2 gap-5 px-5 bg py-5 rounded-lg">
                        <div class="text-left">
                            <h1 class="text-stone-400 fntBold tracking-wide text-lg">License Cost</h1>
                        </div>
                        <div class="text-right">
                            <h1 class="bg w-full rounded-lg text-stone-300 fntBold tracking-wide text-lg">
                                <i class="fa-solid fa-dollar-sign textCol text-base"></i>
                                <span id="cost"></span>
                            </h1>
                        </div>
                    </div>


                    <div class="grid grid-cols-3 gap-3 mt-3 px-5">
                        <button id="monthlyBtn" data-shoppy-product="xTMDbo5"
                            class="bg py-3 w-full rounded-lg text-stone-400 fntBold tracking-wide text-center hvr-underline-from-center btn">
                            <i class="fa-solid fa-money-bill mr-1 icon"></i> 1 Month
                        </button>
                        <button id="monthlyBtn2" data-shoppy-product="1c2AE5c"
                            class="bg py-3 w-full rounded-lg text-stone-400 fntBold tracking-wide text-center hvr-underline-from-center btn">
                            <i class="fa-solid fa-money-bill mr-1 icon"></i> 3 Months
                        </button>
                        <button id="monthlyBtn3" data-shoppy-product="0QFIJWX"
                            class="bg py-3 w-full rounded-lg text-stone-400 fntBold tracking-wide text-center hvr-underline-from-center btn">
                            <i class="fa-solid fa-money-bill mr-1 icon"></i> Lifetime
                        </button>

                        <button id="triMonthBtn" data-shoppy-product="1txPSgz"
                            class="bg py-3 w-full rounded-lg text-stone-400 fntBold tracking-wide text-center hvr-underline-from-center btn">
                            <i class="fa-solid fa-money-bill mr-1 icon"></i> 1 Month
                        </button>
                        <button id="triMonthBtn2" data-shoppy-product="EQnGRGJ"
                            class="bg py-3 w-full rounded-lg text-stone-400 fntBold tracking-wide text-center hvr-underline-from-center btn">
                            <i class="fa-solid fa-money-bill mr-1 icon"></i> 3 Months
                        </button>
                        <button id="triMonthBtn3" data-shoppy-product="pbaQZdU"
                            class="bg py-3 w-full rounded-lg text-stone-400 fntBold tracking-wide text-center hvr-underline-from-center btn">
                            <i class="fa-solid fa-money-bill mr-1 icon"></i> Lifetime
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="content dg mt-20 rounded-lg pb-5" style="pointer-events: none;">
        <div class="mx-5 pt-5">
            <h1 class="text-stone-300 fntBold tracking-wide text-2xl"><i
                    class="fa-solid fa-list text-xl mr-1 textCol"></i> Feature List</h1>
            <h3 class="text-stone-400 fnt tracking-wide text-xl mt-1">Compare the features available with each
                subscription tier.</h3>
            <hr class="brdBg mt-3 pb-5">

            <div class="table-responsive">
                <table id="DashTable">
                    <thead>
                        <tr>
                            <th>Features</th>
                            <th>Trial (Free)</th>
                            <th>Premium</th>
                            <th>Special</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Access to Dashboard</td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>File Execution</td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>General Information</td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr class="spacer-row">
                            <td colspan="4" style="background: transparent; border: none;"></td>
                        </tr>
                        <tr>
                            <td>Build Updates</td>
                            <td>Rarely (Likely Detected)</td>
                            <td>Frequent (Undetected)</td>
                            <td>Frequent (Undetected)</td>
                        </tr>
                        <tr>
                            <td>Stub Config</td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr class="spacer-row">
                            <td colspan="4" style="background: transparent; border: none;"></td>
                        </tr>
                        <tr>
                            <td>Cookie Logging</td>
                            <td>Logged but no access</td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>Password Logging</td>
                            <td>Logged but no access</td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr class="spacer-row">
                            <td colspan="4" style="background: transparent; border: none;"></td>
                        </tr>
                        <tr>
                            <td>Token Logging</td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>Auto Token Checker</td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>Server Perms Notifications</td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr class="spacer-row">
                            <td colspan="4" style="background: transparent; border: none;"></td>
                        </tr>
                        <tr>
                            <td>Automations</td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>Clipper</td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>Cards</td>
                            <td>Logged but no access</td>
                            <td>Logged but no access</td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>Luckyware Infector</td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-times" style="color: #a8a29e;"></i></td>
                            <td><i class="fa-solid fa-check textCol"></i></td>
                        </tr>
                        <tr>
                            <td>API Domains</td>
                            <td>Shared API Domain</td>
                            <td>Premium Quality Domains</td>
                            <td>Private Domains</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script>
        function purchaseStart() {
            document.getElementById('pricingBox').style.display = 'none';
            document.getElementById('container').style.marginTop = '0px';

            document.getElementById('openBtns').style.display = '';
            document.getElementById('purchaseBtns').style.display = 'none';
            document.getElementById('redeem').style.display = 'none';

            document.getElementById('monthlyBtn').style.display = 'none';
            document.getElementById('monthlyBtn2').style.display = 'none';
            document.getElementById('monthlyBtn3').style.display = 'none';

            document.getElementById('triMonthBtn').style.display = 'none';
            document.getElementById('triMonthBtn2').style.display = 'none';
            document.getElementById('triMonthBtn3').style.display = 'none';

            document.getElementById('lifetimeBtn').style.display = 'none';

            document.getElementById('answer1').style.display = 'none';
            document.getElementById('answer2').style.display = 'none';
            document.getElementById('answer3').style.display = 'none';
        }

        function Pre() {
            document.getElementById('pricingBox').style.display = '';
            document.getElementById('cost').innerHTML = '35 | 85 | 150';

            document.getElementById('monthlyBtn').style.display = '';
            document.getElementById('monthlyBtn2').style.display = '';
            document.getElementById('monthlyBtn3').style.display = '';
            document.getElementById('triMonthBtn').style.display = 'none';
            document.getElementById('triMonthBtn2').style.display = 'none';
            document.getElementById('triMonthBtn3').style.display = 'none';
            document.getElementById('lifetimeBtn').style.display = 'none';

            document.getElementById('monthlyIcon').style.color = '#e61b0a';
            document.getElementById('triMonthlyIcon').style.color = '';
            document.getElementById('lifetimeIcon').style.color = '';
        }

        function Spc() {
            document.getElementById('pricingBox').style.display = '';
            document.getElementById('cost').innerHTML = '45 | 100 | 200';

            document.getElementById('monthlyBtn').style.display = 'none';
            document.getElementById('monthlyBtn2').style.display = 'none';
            document.getElementById('monthlyBtn3').style.display = 'none';
            document.getElementById('triMonthBtn').style.display = '';
            document.getElementById('triMonthBtn2').style.display = '';
            document.getElementById('triMonthBtn3').style.display = '';
            document.getElementById('lifetimeBtn').style.display = 'none';

            document.getElementById('monthlyIcon').style.color = '';
            document.getElementById('triMonthlyIcon').style.color = '#e61b0a';
            document.getElementById('lifetimeIcon').style.color = '';
        }


        function answer1() {
            document.getElementById('answer1').style.display = '';
            document.getElementById('answer2').style.display = 'none';
            document.getElementById('answer3').style.display = 'none';
        }

        function answer2() {
            document.getElementById('answer1').style.display = 'none';
            document.getElementById('answer2').style.display = '';
            document.getElementById('answer3').style.display = 'none';
        }

        function answer3() {
            document.getElementById('answer1').style.display = 'none';
            document.getElementById('answer2').style.display = 'none';
            document.getElementById('answer3').style.display = '';
        }

        function purchaseBtns() {
            document.getElementById('openBtns').style.display = 'none';
            document.getElementById('purchaseBtns').style.display = '';
            document.getElementById('redeem').style.display = 'none';
        }

        function openBtns() {
            document.getElementById('openBtns').style.display = '';
            document.getElementById('purchaseBtns').style.display = 'none';
            document.getElementById('redeem').style.display = 'none';
        }

        function redeem() {
            document.getElementById('openBtns').style.display = 'none';
            document.getElementById('purchaseBtns').style.display = 'none';
            document.getElementById('redeem').style.display = '';
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            Promise.all([
                document.fonts.ready,
                new Promise(resolve => {

                    const styleSheets = Array.from(document.styleSheets);
                    if (styleSheets.every(sheet => sheet.loaded !== false)) {
                        resolve();
                    } else {
                        window.addEventListener('load', resolve);
                    }
                })
            ]).then(() => {
                const overlay = document.querySelector('.loading-overlay');


                overlay.classList.add('fade-out');


                overlay.addEventListener('transitionend', function () {
                    overlay.parentNode.removeChild(overlay);
                }, {
                    once: true
                });
            });
        });


        const fonts = ['Kanit'];
        fonts.forEach(font => {
            new FontFace(font, `url(path/to/${font}.woff2)`)
                .load()
                .then(loadedFont => document.fonts.add(loadedFont));
        });
    </script>
</body>

<style>
    input::placeholder {
        color: #a8a29e;
    }

    input:focus {
        outline: none;
    }
</style>

</html>