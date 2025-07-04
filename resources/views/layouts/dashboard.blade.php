<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Pelevo Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-indigo-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <a href="{{ route('dashboard') }}" class="text-white flex items-center space-x-2 px-4">
                <span class="text-2xl font-extrabold">Pelevo</span>
            </a>

            <nav>
                <a href="{{ route('dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
                <a href="{{ route('podcasts.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('podcasts.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-podcast mr-2"></i>Podcasts
                </a>
                <a href="{{ route('earnings.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('earnings.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-money-bill-wave mr-2"></i>Earnings
                </a>
                <a href="{{ route('withdrawals.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('withdrawals.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-wallet mr-2"></i>Withdrawals
                </a>
                <a href="{{ route('notifications.index') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('notifications.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-bell mr-2"></i>Notifications
                </a>
                <a href="{{ route('profile.edit') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('profile.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                    <i class="fas fa-user mr-2"></i>Profile
                </a>
            </nav>
        </div>

        <!-- Content -->
        <div class="flex-1">
            <!-- Top Navigation -->
            <div class="bg-white shadow-lg">
                <div class="container mx-auto px-6 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button class="text-gray-500 focus:outline-none md:hidden" id="sidebar-toggle">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                        <div class="flex items-center">
                            <div class="relative">
                                <button class="flex items-center text-gray-500 focus:outline-none" id="user-menu-button">
                                    <span class="mr-2">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden" id="user-menu">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <main class="container mx-auto px-6 py-8">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.bg-indigo-800').classList.toggle('-translate-x-full');
        });

        // Toggle user menu
        document.getElementById('user-menu-button').addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const userMenuButton = document.getElementById('user-menu-button');
            
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html> 