<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Logbook PPPK</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' },
                        'secondary': { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e' }
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 25%, #bfdbfe 50%, #93c5fd 75%, #60a5fa 100%); min-height: 100vh; }
        .nav-gradient { background: linear-gradient(90deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%); }
        .card-gradient { background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); border: 1px solid rgba(59, 130, 246, 0.2); }
        .glass-effect { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .floating-animation { animation: floating 3s ease-in-out infinite; }
        @keyframes floating { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .pulse-animation { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #3b82f6, #1d4ed8); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #2563eb, #1e40af); }
    </style>
</head>
<body class="text-gray-800">
    @auth
    <!-- Navigation Bar -->
    <nav class="nav-gradient text-white shadow-2xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo/Brand -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-xl flex items-center justify-center shadow-lg floating-animation group-hover:scale-105 transition-transform">
                            <i class="fas fa-book text-white text-lg"></i>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full pulse-animation"></div>
                    </div>
                    <div>
                        <h1 class="font-bold text-xl tracking-tight">E-Logbook <span class="text-cyan-300">P3K</span></h1>
                        <p class="text-xs text-blue-200 opacity-90">Digital Activity Tracker</p>
                    </div>
                </a>

                <!-- User Info & Dropdown -->
                <div class="flex items-center gap-4">

                    <!-- User Dropdown Trigger -->
                    <div class="relative ml-3">
                        <button type="button" onclick="toggleUserMenu()" class="flex items-center gap-3 bg-blue-800/30 pl-3 pr-4 py-2 rounded-xl glass-effect hover:bg-blue-700/40 transition focus:outline-none focus:ring-2 focus:ring-white/20">
                            <!-- Foto Profil -->
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-cyan-300 rounded-full flex items-center justify-center overflow-hidden border border-white/30">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-user text-white text-sm"></i>
                                @endif
                            </div>

                            <!-- Nama User (Hidden di Mobile) -->
                            <div class="text-right hidden md:block">
                                <span class="text-sm font-semibold block text-white">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-blue-200 block font-light text-right">
                                     {{ str_contains(Auth::user()->email, 'admin') ? 'Administrator' : 'Pegawai ASN' }}
                                </span>
                            </div>

                            <!-- Chevron Icon -->
                            <i class="fas fa-chevron-down text-xs text-blue-200 ml-1 transition-transform duration-200" id="chevron-icon"></i>
                        </button>

                        <!-- Dropdown Menu Content -->
                        <div id="user-menu" class="hidden absolute right-0 z-50 mt-2 w-60 origin-top-right rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none transform transition-all duration-200 animate-fade-in-down">

                            <!-- Header Dropdown -->
                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50 rounded-t-xl">
                                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Akun Saya</p>
                                <p class="text-sm font-medium text-gray-900 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 group-hover:bg-blue-200 transition">
                                        <i class="fas fa-home text-xs"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-medium">Dashboard</span>
                                        <span class="text-[10px] text-gray-400">Halaman utama</span>
                                    </div>
                                </a>

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                                     <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-200 transition">
                                        <i class="fas fa-user-cog text-xs"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-medium">Edit Profil</span>
                                        <span class="text-[10px] text-gray-400">Foto & Password</span>
                                    </div>
                                </a>

                                 <a href="{{ route('logbook.history') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                                     <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 group-hover:bg-purple-200 transition">
                                        <i class="fas fa-history text-xs"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-medium">Riwayat Logbook</span>
                                        <span class="text-[10px] text-gray-400">Laporan saya</span>
                                    </div>
                                </a>
                            </div>

                            <!-- Logout -->
                            <div class="border-t border-gray-100 py-2 bg-gray-50/30 rounded-b-xl">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors group">
                                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-500 group-hover:bg-red-200 transition">
                                            <i class="fas fa-sign-out-alt text-xs"></i>
                                        </div>
                                        <span class="font-medium">Keluar Aplikasi</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Mobile Navigation Bottom -->
            <div class="md:hidden bg-blue-800/40 py-3 px-4 rounded-b-lg flex justify-between items-center text-sm text-blue-100">
                <span>{{ \Carbon\Carbon::now()->format('l, d M Y') }}</span>
                <span class="bg-blue-700/50 px-2 py-1 rounded text-xs">Mobile Mode</span>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Main Content -->
    <main class="py-6 px-4 sm:px-6 lg:px-8 min-h-screen">
        <!-- Background decorative elements -->
        <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden">
            <div class="absolute top-10 right-10 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-72 h-72 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse delay-1000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse delay-500"></div>
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-b from-blue-800 to-blue-900 text-white py-8 px-6 rounded-t-3xl shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">E-Logbook P3K</h3>
                                <p class="text-blue-200 text-sm">Sistem Pelaporan Digital</p>
                            </div>
                        </div>
                        <p class="text-blue-300 text-sm">Platform digital untuk pencatatan dan monitoring kegiatan harian pegawai secara real-time.</p>
                    </div>
                    <div class="space-y-4">
                        <h4 class="font-bold text-lg border-l-4 border-cyan-400 pl-3">Navigasi Cepat</h4>
                        <ul class="space-y-2 text-sm text-blue-200">
                            <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">Dashboard Utama</a></li>
                            <li><a href="{{ route('logbook.create') }}" class="hover:text-white transition">Input Kegiatan</a></li>
                            <li><a href="{{ route('logbook.history') }}" class="hover:text-white transition">Riwayat Laporan</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-white transition">Edit Profil</a></li>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h4 class="font-bold text-lg border-l-4 border-cyan-400 pl-3">Kontak & Support</h4>
                        <p class="text-sm text-blue-200">Support: help@elogbook.id<br>Sistem Terenkripsi & Aman</p>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-blue-700/50 text-center text-sm text-blue-300">
                    &copy; {{ date('Y') }} Sistem Informasi BPMP.
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = "{{ session('success') }}";
            const errorMessage = "{{ session('error') }}";
            if (successMessage) showToast(successMessage, 'success');
            if (errorMessage) showToast(errorMessage, 'error');
        });

        // Toggle User Dropdown
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            const icon = document.getElementById('chevron-icon');

            menu.classList.toggle('hidden');

            // Rotate icon animation
            if (menu.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('user-menu');
            const button = document.querySelector('button[onclick="toggleUserMenu()"]');
            const icon = document.getElementById('chevron-icon');

            if (menu && !menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
                if(icon) icon.style.transform = 'rotate(0deg)';
            }
        });

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 z-50 px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-500 ${type === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-gradient-to-r from-red-500 to-pink-600 text-white'}`;
            toast.innerHTML = `<div class="flex items-center gap-3"><i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-xl"></i><div><p class="font-semibold">${type === 'success' ? 'Sukses!' : 'Perhatian!'}</p><p class="text-sm">${message}</p></div><button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/80 hover:text-white"><i class="fas fa-times"></i></button></div>`;
            document.body.appendChild(toast);
            setTimeout(() => { if (toast.parentElement) toast.remove(); }, 5000);
        }
    </script>
</body>
</html>
