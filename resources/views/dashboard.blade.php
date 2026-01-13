@extends('layout')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8 pb-8 sm:pb-12">

    <!-- Welcome Banner dengan Gradasi -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl mb-6 sm:mb-10 shadow-xl sm:shadow-2xl">
        <!-- Background dengan gradasi dan pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>

        <!-- Konten Banner -->
        <div class="relative z-10 p-6 sm:p-8 md:p-12 flex flex-col md:flex-row justify-between items-center">
            <div class="text-white mb-4 sm:mb-6 md:mb-0 text-center md:text-left">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-blue-100 to-cyan-300">
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>
                <p class="text-blue-200 text-sm sm:text-base md:text-lg mt-1 sm:mt-2">Sistem Informasi Logbook Kinerja PPPK BPMP</p>
                <div class="flex flex-wrap justify-center md:justify-start items-center gap-2 sm:gap-3 mt-3 sm:mt-4">
                    <div class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm bg-blue-800/50 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full">
                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span>Sistem Aktif</span>
                    </div>
                    <div class="text-xs sm:text-sm text-blue-300">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        {{ now()->translatedFormat('d F Y') }}
                    </div>
                </div>
            </div>

            <!-- Icon dengan animasi -->
            <div class="relative mt-4 md:mt-0">
                <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-32 lg:h-32 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-xl sm:shadow-2xl floating-animation">
                    <div class="text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl">👋</div>
                </div>
                <div class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg pulse-animation">
                    <i class="fas fa-star text-[8px] sm:text-xs lg:text-sm text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- LOGIKA TAMPILAN KHUSUS ADMIN -->
    @if(str_contains(Auth::user()->email, 'admin'))
        <!-- Panel Monitoring Utama untuk Admin -->
        <div class="mb-6 sm:mb-10">
            <div class="bg-gradient-to-r from-blue-600/10 to-cyan-500/10 rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-blue-200/50 backdrop-blur-sm">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg sm:rounded-xl flex items-center justify-center">
                        <i class="fas fa-crown text-white text-sm sm:text-base"></i>
                    </div>
                    <span>Panel Administrator</span>
                </h3>

                <a href="{{ route('admin.monitoring') }}"
                   class="group block bg-gradient-to-r from-white to-gray-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-lg hover:shadow-2xl border border-blue-100 hover:border-yellow-400 transition-all duration-500 transform hover:-translate-y-1 sm:hover:-translate-y-2">
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                        <!-- Icon Area -->
                        <div class="relative">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg sm:shadow-xl group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-chart-line text-xl sm:text-2xl md:text-3xl text-white"></i>
                            </div>
                            <div class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 w-5 h-5 sm:w-6 sm:h-6 md:w-8 md:h-8 bg-blue-600 rounded-full flex items-center justify-center animate-bounce">
                                <i class="fas fa-arrow-up text-[8px] sm:text-xs text-white"></i>
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1 text-center sm:text-left mt-2 sm:mt-0">
                            <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 group-hover:text-blue-700 transition-colors">
                                Monitoring Kinerja Pegawai
                            </h3>
                            <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base md:text-lg">
                                Akses dashboard lengkap untuk memantau rekapitulasi kinerja seluruh pegawai.
                            </p>
                            <div class="mt-3 sm:mt-4 flex flex-wrap justify-center sm:justify-start gap-1.5 sm:gap-2 md:gap-3">
                                <span class="bg-blue-100 text-blue-700 text-xs sm:text-sm px-2 sm:px-3 py-1 sm:py-1.5 rounded-full font-medium">
                                    <i class="fas fa-users mr-1"></i> Semua Pegawai
                                </span>
                                <span class="bg-green-100 text-green-700 text-xs sm:text-sm px-2 sm:px-3 py-1 sm:py-1.5 rounded-full font-medium">
                                    <i class="fas fa-chart-bar mr-1"></i> Analisis Data
                                </span>
                                <span class="bg-purple-100 text-purple-700 text-xs sm:text-sm px-2 sm:px-3 py-1 sm:py-1.5 rounded-full font-medium">
                                    <i class="fas fa-download mr-1"></i> Export Data
                                </span>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="hidden sm:block">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-full flex items-center justify-center text-white text-base sm:text-lg md:text-xl group-hover:scale-125 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Statistik Ringkas Admin -->
        <div class="mb-8 sm:mb-12">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-md sm:rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-pie text-white text-xs sm:text-sm"></i>
                </div>
                <span>Dashboard Statistik</span>
            </h3>

            <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Total Semua Logbook -->
                <div class="bg-gradient-to-br from-white to-blue-50 p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg border border-blue-100 hover:shadow-xl transition-all duration-300 group hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-400 to-cyan-300 rounded-lg sm:rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-book text-white text-sm sm:text-base"></i>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-blue-600 group-hover:scale-110 transition-transform">
                            {{ $logs->count() }}
                        </div>
                    </div>
                    <p class="text-gray-600 text-xs sm:text-sm font-medium">Total Logbook Masuk</p>
                    <p class="text-gray-400 text-[10px] sm:text-xs mt-0.5 sm:mt-1">Semua waktu</p>
                </div>

                <!-- Logbook Bulan Ini -->
                <div class="bg-gradient-to-br from-white to-green-50 p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg border border-green-100 hover:shadow-xl transition-all duration-300 group hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-400 to-emerald-300 rounded-lg sm:rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-calendar-alt text-white text-sm sm:text-base"></i>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-green-600 group-hover:scale-110 transition-transform">
                            {{ $logs->filter(fn($l) => \Carbon\Carbon::parse($l->tanggal)->format('Y-m') == date('Y-m'))->count() }}
                        </div>
                    </div>
                    <p class="text-gray-600 text-xs sm:text-sm font-medium">Logbook Bulan Ini</p>
                    <p class="text-gray-400 text-[10px] sm:text-xs mt-0.5 sm:mt-1">{{ now()->translatedFormat('F Y') }}</p>
                </div>

                <!-- Logbook Hari Ini -->
                <div class="bg-gradient-to-br from-white to-orange-50 p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg border border-orange-100 hover:shadow-xl transition-all duration-300 group hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-400 to-amber-300 rounded-lg sm:rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-bolt text-white text-sm sm:text-base"></i>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-orange-600 group-hover:scale-110 transition-transform">
                            @php
                                $hariIni = now()->format('Y-m-d');
                                $logsHariIni = $logs->filter(fn($l) => $l->tanggal == $hariIni);
                            @endphp
                            {{ $logsHariIni->count() }}
                        </div>
                    </div>
                    <p class="text-gray-600 text-xs sm:text-sm font-medium">Logbook Hari Ini</p>
                    <p class="text-gray-400 text-[10px] sm:text-xs mt-0.5 sm:mt-1">{{ $logsHariIni->count() }} Kegiatan</p>
                </div>
            </div>
        </div>

    @else
        <!-- LOGIKA TAMPILAN KHUSUS USER -->
        <!-- Menu Navigasi Utama -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-10">
            <!-- Input Kegiatan Baru -->
            <a href="{{ route('logbook.create') }}"
               class="group relative overflow-hidden bg-gradient-to-br from-cyan-blue-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 lg:p-8 shadow-lg hover:shadow-2xl border border-blue-200/70 transition-all duration-500 transform hover:-translate-y-1 sm:hover:-translate-y-2">
                <!-- Background efek hover -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/5 to-blue-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                    <div class="relative">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-lg sm:rounded-xl lg:rounded-2xl flex items-center justify-center shadow-lg sm:shadow-xl group-hover:rotate-12 transition-transform duration-500">
                            <i class="fas fa-pen-alt text-lg sm:text-xl lg:text-2xl text-white"></i>
                        </div>
                        <div class="absolute -bottom-1 -right-1 sm:-bottom-2 sm:-right-2 w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 bg-green-400 rounded-full flex items-center justify-center animate-pulse">
                            <i class="fas fa-plus text-[8px] sm:text-xs text-white"></i>
                        </div>
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 group-hover:text-blue-700 transition-colors">
                            Input Kegiatan Baru
                        </h3>
                        <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base">Isi laporan kinerja harian Anda dengan mudah dan cepat</p>
                        <div class="mt-2 sm:mt-3 lg:mt-4">
                            <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs sm:text-sm px-2 sm:px-3 py-1 sm:py-1.5 rounded-full">
                                <i class="fas fa-clock text-[10px] sm:text-xs"></i>
                                <span>Proses Cepat</span>
                            </span>
                        </div>
                    </div>
                    <div class="text-xl sm:text-2xl lg:text-3xl text-blue-300 group-hover:text-blue-500 transition-colors mt-2 sm:mt-0">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>

            <!-- Lihat Riwayat -->
            <a href="{{ route('logbook.history') }}"
               class="group relative overflow-hidden bg-gradient-to-br from-cyan to-purple-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 lg:p-8 shadow-lg hover:shadow-2xl border border-purple-200/70 transition-all duration-500 transform hover:-translate-y-1 sm:hover:-translate-y-2">
                <!-- Background efek hover -->
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500/0 via-purple-500/5 to-purple-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                    <div class="relative">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-purple-500 to-pink-400 rounded-lg sm:rounded-xl lg:rounded-2xl flex items-center justify-center shadow-lg sm:shadow-xl group-hover:rotate-12 transition-transform duration-500">
                            <i class="fas fa-history text-lg sm:text-xl lg:text-2xl text-white"></i>
                        </div>
                        <div class="absolute -bottom-1 -right-1 sm:-bottom-2 sm:-right-2 w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 bg-yellow-400 rounded-full flex items-center justify-center animate-pulse">
                            <i class="fas fa-chart-line text-[8px] sm:text-xs text-white"></i>
                        </div>
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 group-hover:text-purple-700 transition-colors">
                            Lihat Riwayat Logbook
                        </h3>
                        <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base">Pantau progress dan analisis kinerja Anda dari waktu ke waktu</p>
                        <div class="mt-2 sm:mt-3 lg:mt-4">
                            <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 text-xs sm:text-sm px-2 sm:px-3 py-1 sm:py-1.5 rounded-full">
                                <i class="fas fa-chart-bar text-[10px] sm:text-xs"></i>
                                <span>Analisis Lengkap</span>
                            </span>
                        </div>
                    </div>
                    <div class="text-xl sm:text-2xl lg:text-3xl text-purple-300 group-hover:text-purple-500 transition-colors mt-2 sm:mt-0">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Statistik Ringkas User -->
        <div class="mb-8 sm:mb-12">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-md sm:rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-white text-xs sm:text-sm"></i>
                </div>
                <span>Statistik Kinerja Anda</span>
            </h3>

            <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Total Kegiatan -->
                <div class="bg-gradient-to-br from-cyan to-blue-50 p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg border border-blue-100 hover:shadow-xl transition-all duration-300 group hover:-translate-y-1">
                    <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-lg sm:rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-tasks text-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Total Kegiatan</p>
                            <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-0.5 sm:mt-1">{{ $logs->count() }}</p>
                        </div>
                    </div>
                    <div class="h-1.5 sm:h-2 bg-blue-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full"
                             style="width: {{ min(100, ($logs->count() / max(1, $logs->count())) * 100) }}%"></div>
                    </div>
                </div>

                <!-- Bulan Ini -->
                <div class="bg-gradient-to-br from-cyan to-green-50 p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg border border-green-100 hover:shadow-xl transition-all duration-300 group hover:-translate-y-1">
                    <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-500 to-emerald-400 rounded-lg sm:rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-calendar-check text-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Bulan Ini</p>
                            <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-0.5 sm:mt-1">
                                {{ $logs->filter(fn($l) => \Carbon\Carbon::parse($l->tanggal)->format('Y-m') == date('Y-m'))->count() }}
                            </p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-[10px] sm:text-xs">{{ now()->translatedFormat('F Y') }}</p>
                </div>

                <!-- Kinerja Hari Ini -->
                <div class="bg-gradient-to-br from-cyan to-orange-50 p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg border border-orange-100 hover:shadow-xl transition-all duration-300 group hover:-translate-y-1">
                    <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-orange-500 to-amber-400 rounded-lg sm:rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-bolt text-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Kinerja Hari Ini</p>
                            @php
                                $hariIni = now()->format('Y-m-d');
                                $logsHariIni = $logs->filter(fn($l) => $l->tanggal == $hariIni);
                            @endphp
                            <p class="text-2xl sm:text-3xl font-bold text-orange-600 mt-0.5 sm:mt-1">
                                {{ $logsHariIni->count() }}
                                <span class="text-xs sm:text-sm font-normal text-gray-500">Kegiatan</span>
                            </p>
                        </div>
                    </div>
                    @if($logsHariIni->count() > 0)
                        <div class="flex items-center gap-1.5 text-[10px] sm:text-xs text-green-600">
                            <i class="fas fa-check-circle text-[10px] sm:text-xs"></i>
                            <span>Anda sudah mengisi logbook hari ini</span>
                        </div>
                    @else
                        <div class="flex items-center gap-1.5 text-[10px] sm:text-xs text-orange-600">
                            <i class="fas fa-exclamation-circle text-[10px] sm:text-xs"></i>
                            <span>Belum ada logbook hari ini</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- GALERI AKTIVITAS PEGAWAI (FEED) -->
    <!-- ========================================== -->
    <div class="border-t border-blue-200/50 pt-6 sm:pt-8 lg:pt-10 mt-6 sm:mt-8 lg:mt-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 lg:mb-8">
            <div class="text-center sm:text-left mb-4 sm:mb-0">
                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800 mb-1.5 sm:mb-2 flex items-center justify-center sm:justify-start gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 bg-gradient-to-br from-pink-500 to-rose-400 rounded-lg sm:rounded-xl flex items-center justify-center shadow-md">
                        <i class="fas fa-camera text-white text-sm sm:text-base"></i>
                    </div>
                    <span>Galeri Aktivitas Pegawai</span>
                </h3>
                <p class="text-gray-600 text-sm sm:text-base">Koleksi kegiatan terbaru dari seluruh tim</p>
            </div>
            <div class="text-center sm:text-right">
                <div class="flex items-center justify-center sm:justify-end gap-1.5 text-xs sm:text-sm text-blue-600">
                    <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-blue-400 rounded-full animate-pulse"></div>
                    <span>Update Real-time</span>
                </div>
            </div>
        </div>

        @if($feedLogs->count() > 0)
            <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($feedLogs as $feed)
                    <div class="group bg-gradient-to-b from-cyan to-gray-50 rounded-xl sm:rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl sm:hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 sm:hover:-translate-y-2">
                        <!-- Header dengan gradient -->
                        <div class="bg-gradient-to-r from-cyan to-cyan-50 p-3 sm:p-4 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <!-- User Info -->
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div class="relative">
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center shadow-md overflow-hidden">
                                            @if($feed->user->profile_photo)
                                                <img src="{{ asset('storage/' . $feed->user->profile_photo) }}"
                                                     alt="{{ $feed->user->name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <span class="text-white font-bold text-xs sm:text-sm">{{ substr($feed->user->name, 0, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="overflow-hidden flex-1">
                                        <p class="font-bold text-gray-800 text-sm sm:text-base truncate">{{ $feed->user->name }}</p>
                                        <div class="flex items-center gap-1 text-[10px] sm:text-xs text-gray-500">
                                            <i class="fas fa-map-marker-alt text-red-400 text-[10px] sm:text-xs"></i>
                                            <span class="truncate">{{ $feed->lokasi }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Time Badge -->
                                <div class="bg-cyan backdrop-blur-sm text-gray-700 text-[10px] sm:text-xs font-bold px-2 sm:px-3 py-1 sm:py-1.5 rounded-full shadow-sm whitespace-nowrap ml-2">
                                    <i class="fas fa-clock mr-1 text-[8px] sm:text-xs"></i>
                                    {{ \Carbon\Carbon::parse($feed->tanggal)->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <!-- Foto/Content Area -->
                        <div class="h-40 sm:h-48 md:h-56 bg-gray-100 relative overflow-hidden">
                            @if($feed->bukti_foto)
                                <img src="{{ asset('storage/' . $feed->bukti_foto) }}"
                                     alt="Bukti Kegiatan"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            @else
                                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center mb-2 sm:mb-3">
                                        <i class="fas fa-image text-lg sm:text-xl md:text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-xs sm:text-sm">Tidak ada foto</p>
                                </div>
                            @endif

                            <!-- Overlay Icon -->

                        </div>

                        <!-- Konten Detail -->
                        <div class="p-3 sm:p-4 md:p-5">
                            <!-- Kegiatan -->
                            <p class="text-gray-700 text-xs sm:text-sm line-clamp-2 mb-2 sm:mb-3 leading-relaxed">
                                <span class="text-blue-600 font-medium text-xs sm:text-sm">Kegiatan:</span>
                                {{ Str::limit($feed->kegiatan, 80) }}
                            </p>

                            <!-- Output -->
                            <div class="mb-3 sm:mb-4">
                                <p class="text-gray-700 text-xs sm:text-sm">
                                    <span class="text-green-600 font-medium text-xs sm:text-sm">Output:</span>
                                    {{ Str::limit($feed->output, 60) }}
                                </p>
                            </div>

                            <!-- Tags -->
                            <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                <span class="bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 text-[10px] sm:text-xs px-2 sm:px-3 py-1 sm:py-1.5 rounded-full font-medium truncate max-w-[150px]">
                                    <i class="fas fa-bullseye mr-1 text-[8px] sm:text-xs"></i>
                                    {{ Str::limit($feed->sasaran_pekerjaan, 20) }}
                                </span>
                                <span class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 text-[10px] sm:text-xs px-2 sm:px-3 py-1 sm:py-1.5 rounded-full font-medium">
                                    <i class="fas fa-clock mr-1 text-[8px] sm:text-xs"></i>
                                    {{ \Carbon\Carbon::parse($feed->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($feed->jam_selesai)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Show More Button -->
            <div class="text-center mt-6 sm:mt-8 lg:mt-10">
                <button onclick="refreshFeed()" class="group bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white px-6 sm:px-8 py-2.5 sm:py-3 rounded-full font-medium shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-sm sm:text-base">
                    <i class="fas fa-sync-alt mr-2 group-hover:rotate-180 transition-transform duration-500"></i>
                    Muat Lebih Banyak Aktivitas
                </button>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl sm:rounded-3xl p-6 sm:p-8 md:p-12 text-center border-2 border-dashed border-gray-300">
                <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg sm:rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-inner">
                    <i class="fas fa-images text-xl sm:text-2xl md:text-3xl text-gray-400"></i>
                </div>
                <h4 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-700 mb-1.5 sm:mb-2">Belum Ada Aktivitas</h4>
                <p class="text-gray-500 max-w-md mx-auto mb-4 sm:mb-6 text-sm sm:text-base">
                    Galeri aktivitas akan terisi ketika pegawai mulai mengunggah logbook dengan foto.
                </p>
                <a href="{{ route('logbook.create') }}"
                   class="inline-flex items-center gap-1.5 sm:gap-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-sm sm:text-base">
                    <i class="fas fa-plus"></i>
                    Jadilah yang Pertama Mengisi
                </a>
            </div>
        @endif
    </div>

</div>

<!-- JavaScript untuk interaktifitas -->
<script>
    // Function untuk refresh feed
    function refreshFeed() {
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;

        // Tampilkan loading state
        button.innerHTML = `
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Memuat...
        `;
        button.disabled = true;

        // Simulasi loading
        setTimeout(() => {
            location.reload();
        }, 1500);
    }

    // Hover effect untuk cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.group');

        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.zIndex = '10';
            });

            card.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        });

    });
</script>
@endsection
