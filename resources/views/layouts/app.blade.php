<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelevo - Listen to Podcasts, Earn Coins</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AOS Animate On Scroll -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0d9488',
                        secondary: '#14b8a6',
                        accent: '#06b6d4',
                        teal: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                once: true,
                duration: 900,
                easing: 'ease-out-cubic',
            });
        });
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(13, 148, 136, 0.5); }
            to { box-shadow: 0 0 30px rgba(13, 148, 136, 0.8); }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 50%, #06b6d4 100%);
        }
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #14b8a6 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .dark .card-hover:hover {
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-effect {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-gradient {
            background: linear-gradient(135deg, #0d9488, #14b8a6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .dark .text-gradient {
            background: linear-gradient(135deg, #5eead4, #2dd4bf, #14b8a6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        html {
            scroll-behavior: smooth;
        }
        .nav-link.active, .nav-link.active:focus {
            color: #14b8a6 !important;
            font-weight: bold;
            border-bottom: 2px solid #14b8a6;
            background: none;
        }
        .dark .nav-link.active, .dark .nav-link.active:focus {
            color: #5eead4 !important;
            border-bottom: 2px solid #5eead4;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300" x-data="{ showDemo: false, mobileMenu: false }">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-effect border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <div class="h-20 w-20 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('public/storage/logo/logo.png') }}" alt="Pelevo Logo" class="max-h-full max-w-full object-contain" height="200" width="200"/>
                    </div>
                    
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a id="nav-features" href="{{ route('landing') }}#features" class="nav-link text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded" tabindex="0">Features</a>
                    <a id="nav-how" href="{{ route('landing') }}#how-it-works" class="nav-link text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded" tabindex="0">How It Works</a>
                    <a id="nav-about" href="{{ route('landing') }}#about" class="nav-link text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded" tabindex="0">About</a>
                    <a id="nav-download" href="{{ route('landing') }}#download" class="nav-link text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded" tabindex="0">Download</a>
                    <a id="nav-contact" href="{{ route('contact.show') }}" class="nav-link text-gray-600 dark:text-gray-300 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded" tabindex="0">Contact Us</a>
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary" aria-label="Toggle dark mode">
                        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-full hover:shadow-lg transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary">Get Started</button>
                </div>
                <!-- Mobile menu button -->
                <button class="md:hidden p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary" @click="mobileMenu = !mobileMenu" aria-label="Open mobile menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-transition class="md:hidden fixed inset-0 z-50 bg-black/80" style="display: none;">
            <div class="absolute top-0 right-0 w-3/4 max-w-xs min-h-full h-auto bg-white shadow-lg p-8 flex flex-col space-y-6">
                <button class="absolute top-4 right-4 text-gray-500 hover:text-primary dark:hover:text-teal-400 text-2xl focus:outline-none" @click="mobileMenu = false" aria-label="Close mobile menu">&times;</button>
                <a id="mnav-features" href="{{ route('landing') }}#features" @click="mobileMenu = false" class="nav-link block text-lg text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded">Features</a>
                <a id="mnav-how" href="{{ route('landing') }}#how-it-works" @click="mobileMenu = false" class="nav-link block text-lg text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded">How It Works</a>
                <a id="mnav-about" href="{{ route('landing') }}#about" @click="mobileMenu = false" class="nav-link block text-lg text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded">About</a>
                <a id="mnav-download" href="{{ route('landing') }}#download" @click="mobileMenu = false" class="nav-link block text-lg text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded">Download</a>
                <a id="mnav-contact" href="{{ route('contact.show') }}" @click="mobileMenu = false" class="nav-link block text-lg text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-teal-400 transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded">Contact Us</a>
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary" aria-label="Toggle dark mode">
                    <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <button class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-full hover:shadow-lg transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary">Get Started</button>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('public/storage/logo/logo.png') }}" alt="Pelevo Logo" height="200" width="200" />
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md">
                        Transform your podcast listening experience. Earn rewards while enjoying amazing content from creators around the world.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 2.567-1.645 0-2.861.967-2.861 2.168 0 1.029.653 2.567.992 2.567.348 0 1.029-.653 2.567-1.645 2.567z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Product</h3>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="text-gray-400 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="#download" class="text-gray-400 hover:text-white transition-colors">Download</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">About</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('privacy') }}" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-gray-400 hover:text-white transition-colors">Terms & Conditions</a></li>
                        <li><a href="{{ route('cookie-policy') }}" class="text-gray-400 hover:text-white transition-colors">Cookie Policy</a></li>
                        <li><a href="{{ route('gdpr') }}" class="text-gray-400 hover:text-white transition-colors">GDPR</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} Pelevo. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <!-- Demo Modal -->
    <div x-show="showDemo" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full p-6 relative">
            <button @click="showDemo = false" class="absolute top-3 right-3 text-gray-500 hover:text-primary dark:hover:text-teal-400 text-2xl">&times;</button>
            <div class="aspect-w-16 aspect-h-9 mb-4">
                <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen class="w-full h-64 rounded-lg"></iframe>
            </div>
            <div class="text-center">
                <button @click="showDemo = false" class="mt-4 px-6 py-2 bg-primary text-white rounded-full hover:bg-secondary transition">Close</button>
            </div>
        </div>
    </div>
    <script>
    // Scrollspy for nav highlighting
    (function() {
        // Only run on landing page
        if (!window.location.pathname.endsWith('/') && !window.location.pathname.endsWith('/landing')) return;
        const sections = [
            {id: 'features', nav: ['nav-features', 'mnav-features']},
            {id: 'how-it-works', nav: ['nav-how', 'mnav-how']},
            {id: 'about', nav: ['nav-about', 'mnav-about']},
            {id: 'download', nav: ['nav-download', 'mnav-download']},
        ];
        function clearActive() {
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        }
        function setActive(id) {
            clearActive();
            const section = sections.find(s => s.id === id);
            if (section) {
                section.nav.forEach(nid => {
                    const el = document.getElementById(nid);
                    if (el) el.classList.add('active');
                });
            }
        }
        function onScroll() {
            let found = false;
            for (let i = sections.length - 1; i >= 0; i--) {
                const sec = document.getElementById(sections[i].id);
                if (sec && sec.getBoundingClientRect().top <= 80) {
                    setActive(sections[i].id);
                    found = true;
                    break;
                }
            }
            if (!found) clearActive();
        }
        window.addEventListener('scroll', onScroll);
        // On load, highlight if hash present
        window.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash) {
                const hash = window.location.hash.replace('#','');
                setActive(hash);
            } else {
                onScroll();
            }
        });
        // On nav click, highlight immediately
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = link.getAttribute('href');
                if (href && href.includes('#')) {
                    const id = href.split('#')[1];
                    setTimeout(() => setActive(id), 100);
                }
            });
        });
    })();
    </script>
</body>
</html> 



