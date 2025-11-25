<?php

$domainyess = $_SERVER['HTTP_HOST'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Dashboard/assets/css/custom.css">
    <link rel="stylesheet" href="../Dashboard/assets/css/style.css">
    <link rel="stylesheet" href="../Dashboard/assets/css/hover.css">
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
    <meta content="#c20303" data-react-helmet="true" name="theme-color" />
    <link rel="shortcut icon" href="https://<?php echo $domainyess; ?>/icon.png" />
    <title>ToS</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
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

        /* Custom hover link color */
        .hover-link {
            color: #111111;
            transition: color 0.3s ease-in-out;
        }

        .hover-link:hover {
            color: #c20303;
        }

        .current {
            background-color: #850000;
        }

        .btn3 {
            border: 1px solid #151515;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn3::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(194, 3, 3, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .btn3:hover {
            box-shadow: 0 0 8px rgba(194, 3, 3, 0.3);
            border-color: #c20303;
            transform: translateY(-1px);
        }

        .btn3:hover::before {
            left: 100%;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .col-span-10 {
                margin-left: 1rem;
                margin-right: 1rem;
            }
            
            .message {
                margin-left: 1rem !important;
                margin-right: 1rem !important;
            }
            
            .px-5 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .xl\:col-start-1 {
                grid-column-start: 1;
            }
            
            .xl\:col-end-7 {
                grid-column-end: -1;
            }
        }

        @media (max-width: 640px) {
            .message h1 {
                font-size: 0.9rem;
                line-height: 1.4;
            }
            
            .text-lg {
                font-size: 1rem;
            }
            
            .mt-10 {
                margin-top: 1.5rem;
            }
        }

        /* Subtle animations */
        .message {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .message:nth-child(3) { animation-delay: 0.1s; }
        .message:nth-child(4) { animation-delay: 0.2s; }
        .message:nth-child(5) { animation-delay: 0.3s; }
        .message:nth-child(6) { animation-delay: 0.4s; }
        .message:nth-child(7) { animation-delay: 0.5s; }
        .message:nth-child(8) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced section titles */
        .section-title {
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #c20303;
            transition: width 0.3s ease;
        }

        .btn3:hover .section-title::after {
            width: 30%;
        }

        /* Icon enhancements */
        .icon-enhanced {
            transition: all 0.3s ease;
        }

        .btn3:hover .icon-enhanced {
            color: #c20303;
            transform: scale(1.1);
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
                <h1 class="fntBold tracking-wide text-lg px-5 pt-4 text-stone-400">
                    <i class="fa-solid fa-globe mr-1 icon2 icon-enhanced"></i> Lucky Ware Terms of Service
                </h1>
                <hr class="mx-5 mt-2 border-stone-800">

                <!-- General Information -->
                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide section-title">
                                    <i class="fa-solid fa-info-circle mr-2 icon-enhanced" style="color: #c20303;"></i>
                                    General Information
                                </h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        Luckyware is a platform that allows users to build customized executable (.exe) files for data management. By accessing and using Luckyware, you agree to comply with all applicable local, national, and international laws and regulations. All services provided are intended strictly for lawful use.
                    </h1>
                </div>

                <!-- Acceptable Use -->
                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide section-title">
                                    <i class="fa-solid fa-check-circle mr-2 icon-enhanced" style="color: #c20303;"></i>
                                    Acceptable Use
                                </h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        Users are expected to use Luckyware-generated files for ethical and professional purposes only. It is strictly forbidden to use Luckyware to create tools for unauthorized data access, monitoring, manipulation, or harm. Any activity that violates our Acceptable Use policy may result in immediate suspension or termination of your access to our services.
                    </h1>
                </div>

                <!-- Misuse & Liability Disclaimer -->
                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide section-title">
                                    <i class="fa-solid fa-exclamation-triangle mr-2 icon-enhanced" style="color: #c20303;"></i>
                                    Misuse & Liability Disclaimer
                                </h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        Luckyware is not responsible for any misuse of the tools created through its platform. All responsibility for actions taken using the generated files lies with the end user. We do not monitor user activity or the final use of the executables provided. Using Luckyware for illegal, malicious, or harmful activities is strictly prohibited and may result in legal consequences. And finally Luckyware isnt made for direct useage like sending people the executeable directly, It is made for stealth useage, like adding the binary to a loader or a product.
                    </h1>
                </div>

                <!-- Refund Policy -->
                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide section-title">
                                    <i class="fa-solid fa-credit-card mr-2 icon-enhanced" style="color: #c20303;"></i>
                                    Refund Policy
                                </h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        Refunds are only issued if the provided stub is immediately flagged by Malwarebytes and Windows Defender via Virustotal upon delivery or if it fails to operate on a standard machine setup, note that the machine has to be a real machine with a real cpu, and gpu. Also Luckyware stubs do require C++ Redist to operate. Information regards the builds are also avaible in luckyware.co/Dashboard/Information please read this page too before purchasing. Refunds will not be granted due to misuse, user error, or environment-specific issues. All refund claims must be submitted within 12 hours of delivery with proof of error. If you contacted to support you have to wait till they respond, respond times can take up to 24 hours, if you do not wait for the support and still request a refund it will be denied. 
                    </h1>
                </div>

                <!-- License & Ownership -->
                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide section-title">
                                    <i class="fa-solid fa-certificate mr-2 icon-enhanced" style="color: #c20303;"></i>
                                    License & Ownership
                                </h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        Luckyware grants you a non-exclusive, non-transferable, limited license to use the generated executables for lawful purposes. All intellectual property rights related to the service, templates, and backend tools remain the sole property of Luckyware and its affiliates.
                    </h1>
                </div>

                <!-- Modifications & Termination -->
                <div class="message dg mx-5 py-1 mt-5 rounded-lg btn3">
                    <div class="flex px-5 mt-3">
                        <div class="grid grid-cols-2">
                            <div class="block">
                                <h1 class="mt-1 ml-0 fntBold text-[#DCDBD1] tracking-wide section-title">
                                    <i class="fa-solid fa-edit mr-2 icon-enhanced" style="color: #c20303;"></i>
                                    Modifications & Termination
                                </h1>
                            </div>
                        </div>
                    </div>
                    <h1 class="px-5 mt-1 mb-3 text-stone-400 fntBold">
                        Luckyware reserves the right to modify, suspend, or terminate the service at any time without prior notice or Add & Remove data at any time. We may also change the terms outlined here, and continued use of the service implies acceptance of any updated terms. Account sharing will cause the account to get suspended without any refunds, suspended accs can be unlocked for 30 USD. (This applys if you get your account credentials stolen too)
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