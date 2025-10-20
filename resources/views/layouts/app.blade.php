<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Puterako Super App</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar-collapsed {
            width: 4rem !important;
        }

        .sidebar-expanded {
            width: 14rem !important;
        }

        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }

        .menu-label {
            transition: opacity 0.2s, visibility 0.2s;
        }

        .menu-label.hide {
            opacity: 0;
            visibility: hidden;
            width: 0;
            padding: 0;
        }

        .menu-label.show {
            opacity: 1;
            visibility: visible;
            width: auto;
        }

        .menu-item {
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.1) 0%, transparent 100%);
            transition: width 0.3s ease;
        }

        .menu-item:hover::before,
        .menu-item.active::before {
            width: 100%;
        }

        .menu-item.active {
            background: rgba(59, 130, 246, 0.05);
        }

        .sidebar-collapsed .menu-item {
            padding: 0.75rem;
            justify-content: center;
        }

        .sidebar-collapsed .sidebar-header {
            padding: 0.5rem 0.75rem;
        }

        .sidebar-collapsed .sidebar-footer {
            padding: 1rem 0.75rem;
        }

        /* Hide dropdown icon when sidebar collapsed */
        .sidebar-collapsed .dropdown-icon {
            display: none;
        }

        /* Adjust icon container when collapsed */
        .sidebar-collapsed .menu-item>div:first-child {
            margin: 0;
        }

        /* Center icons when sidebar is collapsed */
        .sidebar-collapsed .menu-item {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }

        .dropdown-menu.open {
            max-height: 300px;
        }

        .dropdown-icon {
            transition: transform 0.3s ease;
        }

        .dropdown-icon.rotate {
            transform: rotate(180deg);
        }

        .submenu-item {
            position: relative;
            padding-left: 3.5rem;
        }

        

        .submenu-item:hover::before,
        .submenu-item.active::before {
            background: #3B82F6;
            width: 0.75rem;
            height: 0.75rem;
        }

        .submenu-item.active {
            background: rgba(59, 130, 246, 0.05);
        }

        /* Tooltip styles */
        .menu-tooltip {
            position: absolute;
            left: 100%;
            margin-left: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: #1F2937;
            color: white;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
            pointer-events: none;
            z-index: 50;
        }

        .sidebar-collapsed .menu-item:hover .menu-tooltip,
        .sidebar-collapsed .menu-item:focus .menu-tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* Adjust button layout when collapsed */
        .sidebar-collapsed button.menu-item {
            position: relative;
        }
    </style>
</head>

<body class="bg-gray-50">

    <div class="flex flex-col min-h-screen">
        <!-- Navbar -->
        <header
            class="bg-white shadow-sm border-b border-gray-200 w-full p-4 flex justify-between items-center fixed top-0 left-0 z-20"
            style="height:64px;">
            <div class="flex items-center space-x-4">
                <!-- Sidebar Toggle Button -->
                <button id="sidebarToggle"
                    class="p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 transition-colors"
                    type="button">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="flex items-center space-x-2">
                    {{-- <div class="w-8 h-8 bg-[#3B82F6] rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm"></span>
                    </div> --}}
                    <img src="{{ asset('assets/puterako_logo.png') }}" alt="Puterako Logo" class="h-5 w-auto">
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">R</p>
                    <p class="text-xs text-gray-500"></p>
                </div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-sm"></span>
                </div>
            </div>
        </header>

        <div class="flex flex-1 pt-16">
            <!-- Sidebar -->
            <aside id="sidebar"
                class="bg-white border-r border-gray-200 min-h-screen sidebar-expanded sidebar-transition flex flex-col shadow-sm">
                <!-- Sidebar Header -->
                <div class="sidebar-header p-4 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div class="menu-label show ">
                            <h3 class="font-bold text-gray-900">
                                Puterako Super App - Sales</h3>
                            <p class="text-xs text-gray-500">Puterako ERP</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 overflow-y-auto">
                    <div class="space-y-2 p-4">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}"
                            class="menu-item group flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 relative">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                                <svg class="w-6 h-6 transition-colors text-gray-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                            </div>
                            <div class="menu-label show">
                                <span class="font-medium transition-colors">Dashboard</span>
                                <p class="text-xs text-gray-500 mt-0.5">Monitoring Dashboard</p>
                            </div>
                            <div class="menu-tooltip">Dashboard</div>
                        </a>

                        <!-- Penawaran (dengan Dropdown) -->
                        <div>
                            <button id="penawaranDropdown" type="button"
                                class="menu-item group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200 relative">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                                        <svg class="w-6 h-6 transition-colors text-gray-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="menu-label show text-left">
                                        <span class="font-medium transition-colors">Penawaran</span>
                                        <p class="text-xs text-gray-500 mt-0.5">Kelola penawaran</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dropdown-icon menu-label show" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <div class="menu-tooltip">Penawaran</div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="penawaranMenu" class="dropdown-menu">
                                <div class="space-y-1 py-2">
                                    <a href="{{ route('penawaran.list') }}"
                                        class="submenu-item block py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                        <x-lucide-scroll-text class="w-4 h-4 inline-block mr-2 text-gray-600" />
                                        <span class="menu-label show text-sm text-gray-700">List Penawaran</span>
                                    </a>
                                    <a href="{{ route('penawaran.followup') }}"
                                        class="submenu-item block py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                        <x-lucide-phone-call class="w-4 h-4 inline-block mr-2 text-gray-600" />
                                        <span class="menu-label show text-sm text-gray-700">Follow Up</span>
                                    </a>
                                    <a href="{{ route('penawaran.rekap-survey') }}"
                                        class="submenu-item block py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                        <x-lucide-files class="w-4 h-4 inline-block mr-2 text-gray-600" />
                                        <span class="menu-label show text-sm text-gray-700">Rekap Survey</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        

                         <!-- Klien (dengan Dropdown) -->
                        <div>
                            <button id="klienDropdown" type="button"
                                class="menu-item group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200 relative">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                                        <svg class="w-6 h-6 transition-colors text-gray-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="menu-label show text-left">
                                        <span class="font-medium transition-colors">Klien</span>
                                        <p class="text-xs text-gray-500 mt-0.5">Kelola klien</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 dropdown-icon menu-label show" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <div class="menu-tooltip">Klien</div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="klienMenu" class="dropdown-menu">
                                <div class="space-y-1 py-2">
                                    <a href="#"
                                        class="submenu-item block py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                        <span class="menu-label show text-sm text-gray-700">List Klien</span>
                                    </a>
                                    <a href="#"
                                        class="submenu-item block py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                        <span class="menu-label show text-sm text-gray-700">Klien Baru</span>
                                    </a>
                                    <a href="#"
                                        class="submenu-item block py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                        <span class="menu-label show text-sm text-gray-700">Detail Klien</span>
                                    </a>
                                </div>
                            </div>
                        </div>


                    </div>
                </nav>

                <!-- Sidebar Footer -->
                <div class="sidebar-footer p-4 border-t border-gray-100">
                    <form method="POST" action="#">
                        @csrf
                        <button type="submit"
                            class="w-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2 rounded flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1">
                                </path>
                            </svg>
                            <span class="menu-label show">Logout</span>
                        </button>
                    </form>
                    <div class="menu-label show mt-4">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-8 h-8 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-600 " fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Need Help?</p>
                                    <p class="text-xs text-gray-600">Contact support</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 transition-all duration-300">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const labels = document.querySelectorAll('.menu-label');
        const penawaranDropdown = document.getElementById('penawaranDropdown');
        const penawaranMenu = document.getElementById('penawaranMenu');
        const klienDropdown = document.getElementById('klienDropdown');
        const klienMenu = document.getElementById('klienMenu');
        const dropdownIcon = penawaranDropdown.querySelector('.dropdown-icon');
        let expanded = true;
        let dropdownOpen = false;

        // Sidebar Toggle
        toggleBtn.addEventListener('click', function() {
            expanded = !expanded;
            if (expanded) {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                labels.forEach(label => {
                    label.classList.remove('hide');
                    label.classList.add('show');
                });
            } else {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                labels.forEach(label => {
                    label.classList.remove('show');
                    label.classList.add('hide');
                });
                // Close dropdown when sidebar collapses
                if (dropdownOpen) {
                    penawaranMenu.classList.remove('open');
                    dropdownIcon.classList.remove('rotate');
                    dropdownOpen = false;
                }
            }
        });

        // Dropdown Toggle
        penawaranDropdown.addEventListener('click', function() {
            if (expanded) {
                dropdownOpen = !dropdownOpen;
                if (dropdownOpen) {
                    penawaranMenu.classList.add('open');
                    dropdownIcon.classList.add('rotate');
                } else {
                    penawaranMenu.classList.remove('open');
                    dropdownIcon.classList.remove('rotate');
                }
            }
        });
        klienDropdown.addEventListener('click', function() {
            if (expanded) {
                dropdownOpen = !dropdownOpen;
                if (dropdownOpen) {
                    klienMenu.classList.add('open');
                    dropdownIcon.classList.add('rotate');
                } else {
                    klienMenu.classList.remove('open');
                    dropdownIcon.classList.remove('rotate');
                }
            }
        });

        // Set active menu based on current URL
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('.menu-item, .submenu-item');
        menuItems.forEach(item => {
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active');
                // If it's a submenu item, open the parent dropdown
                if (item.classList.contains('submenu-item')) {
                    penawaranMenu.classList.add('open');
                    dropdownIcon.classList.add('rotate');
                    dropdownOpen = true;
                }
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
