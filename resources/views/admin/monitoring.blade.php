@extends('layout')

@section('title', 'Monitoring Logbook - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">

    <!-- Header dengan Gradient Text & Tombol Aksi -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-8">
            <div>
                <h2 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent animate-gradient-x">
                    <i class="fas fa-chart-line mr-2 text-blue-500"></i>Monitoring Logbook
                </h2>
                <p class="text-gray-600 mt-2 text-lg font-medium">Pantau aktivitas kinerja pegawai secara real-time.</p>
            </div>

            <!-- Tombol Menu Download -->
            <button onclick="openExportModal()"
                    class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white transition-all duration-200 bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 rounded-2xl hover:scale-105 focus:outline-none shadow-lg hover:shadow-emerald-500/40">
                <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-black"></span>
                <i class="fas fa-cloud-download-alt mr-2 text-xl animate-bounce"></i>
                <span class="relative">Menu Download Laporan</span>
                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-ping"></div>
            </button>
        </div>

        <!-- Statistik Cards (Full Color Gradient) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total Entri (Blue Gradient) -->
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-cyan-400 rounded-3xl p-6 shadow-xl text-white transform hover:-translate-y-2 transition-transform duration-300">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-20 rounded-full blur-xl"></div>
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 font-semibold tracking-wider uppercase text-xs">Total Entri</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ $logs->total() }}</h3>
                        <span class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-xs font-bold backdrop-blur-sm">
                            <i class="fas fa-arrow-up mr-1"></i> Data Masuk
                        </span>
                    </div>
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <i class="fas fa-database text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Pegawai Aktif (Purple Gradient) -->
            <div class="relative overflow-hidden bg-gradient-to-br from-purple-600 to-pink-500 rounded-3xl p-6 shadow-xl text-white transform hover:-translate-y-2 transition-transform duration-300">
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-white opacity-20 rounded-full blur-xl"></div>
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-purple-100 font-semibold tracking-wider uppercase text-xs">Pegawai Aktif</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ $users->count() }}</h3>
                        <span class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-xs font-bold backdrop-blur-sm">
                            <i class="fas fa-users mr-1"></i> Terdaftar
                        </span>
                    </div>
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <i class="fas fa-id-card-alt text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Input Hari Ini (Emerald Gradient) -->
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-400 rounded-3xl p-6 shadow-xl text-white transform hover:-translate-y-2 transition-transform duration-300">
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-emerald-100 font-semibold tracking-wider uppercase text-xs">Input Hari Ini</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ \App\Models\Logbook::whereDate('tanggal', today())->count() }}</h3>
                        <span class="inline-block mt-2 px-3 py-1 bg-white/20 rounded-full text-xs font-bold backdrop-blur-sm">
                            <i class="fas fa-calendar-check mr-1"></i> Log Baru
                        </span>
                    </div>
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <i class="fas fa-clock text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & PENCARIAN (Colorful Container) -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 mb-10 overflow-hidden relative">
        <!-- Dekorasi Top Bar Gradient -->
        <div class="h-2 bg-gradient-to-r from-orange-400 via-pink-500 to-purple-500"></div>

        <div class="p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <i class="fas fa-filter text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Filter Data Tabel</h3>
            </div>

            <form action="{{ route('admin.monitoring') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <!-- Filter Pegawai -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Pegawai</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-3 top-3 text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        <select name="user_id" class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:bg-white">
                            <option value="">Semua Pegawai</option>
                            @foreach($users as $user)
                                @if(!str_contains($user->email, 'admin'))
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filter Tanggal -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Tanggal</label>
                    <div class="relative group">
                        <i class="fas fa-calendar absolute left-3 top-3 text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all hover:bg-white cursor-pointer">
                    </div>
                </div>

                <!-- Pencarian -->
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Cari Kata Kunci</label>
                    <div class="relative group">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 group-focus-within:text-pink-500 transition-colors"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kegiatan, lokasi, output..." class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all hover:bg-white">
                    </div>
                </div>

                <!-- Tombol -->
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.monitoring') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2.5 rounded-xl text-sm font-bold shadow-sm transition hover:rotate-180 duration-500" title="Reset">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL DATA (Colorful Header) -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white">
                    <tr>
                        <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider w-64 rounded-tl-lg">
                            <i class="fas fa-user-circle mr-2 opacity-70"></i>Pegawai
                        </th>
                        <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider w-40">
                            <i class="fas fa-clock mr-2 opacity-70"></i>Waktu
                        </th>
                        <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider w-48">
                            <i class="fas fa-map-marked-alt mr-2 opacity-70"></i>Lokasi
                        </th>
                        <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider w-1/4">
                            <i class="fas fa-tasks mr-2 opacity-70"></i>Kegiatan
                        </th>
                        <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider">
                            <i class="fas fa-check-circle mr-2 opacity-70"></i>Output
                        </th>
                        <th class="px-6 py-5 text-center text-xs font-bold uppercase tracking-wider w-24 rounded-tr-lg">
                            <i class="fas fa-image mr-2 opacity-70"></i>Bukti
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logs as $index => $log)
                    <tr class="hover:bg-blue-50/40 transition-colors duration-200 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                        <!-- Nama Pegawai -->
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shrink-0 overflow-hidden shadow-md ring-2 ring-white">
                                    @if($log->user->profile_photo)
                                        <img src="{{ asset('storage/' . $log->user->profile_photo) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($log->user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('admin.monitoring', ['user_id' => $log->user_id]) }}" class="font-bold text-gray-900 hover:text-blue-600 hover:underline transition-colors block text-sm">
                                        {{ $log->user->name }}
                                    </a>
                                    <div class="text-[10px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full inline-block mt-1 border border-gray-200">
                                        {{ $log->user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Waktu -->
                        <td class="px-6 py-4 align-top">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded-md w-fit border border-gray-200">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}
                                </span>
                                <span class="text-xs text-gray-500 mt-2 font-mono flex items-center">
                                    <i class="far fa-clock mr-1 text-blue-400"></i>
                                    {{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->jam_selesai)->format('H:i') }}
                                </span>
                            </div>
                        </td>

                        <!-- Lokasi -->
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm text-gray-700 flex items-start gap-2 bg-red-50 p-2 rounded-lg border border-red-100 group hover:border-red-200 transition">
                                <i class="fas fa-map-pin text-red-500 mt-1 text-xs group-hover:animate-bounce"></i>
                                <span class="font-medium text-xs leading-relaxed">{{ $log->lokasi }}</span>
                            </div>
                        </td>

                        <!-- Kegiatan -->
                        <td class="px-6 py-4 align-top">
                            <span class="inline-block bg-gradient-to-r from-blue-50 to-indigo-50 text-indigo-700 text-[10px] px-2 py-1 rounded-md border border-indigo-100 font-bold mb-2 tracking-wide uppercase">
                                Sasaran SKP
                            </span>
                            <p class="text-xs text-gray-500 mb-3 italic">"{{ Str::limit($log->sasaran_pekerjaan, 50) }}"</p>

                            <p class="text-sm text-gray-800 leading-relaxed pl-3 border-l-2 border-indigo-300">
                                {{ $log->kegiatan }}
                            </p>
                        </td>

                        <!-- Output -->
                        <td class="px-6 py-4 align-top">
                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-green-100">
                                <i class="fas fa-check-circle text-xs"></i> {{ $log->output }}
                            </span>
                        </td>

                        <!-- Bukti -->
                        <td class="px-6 py-4 align-top text-center">
                            @if($log->bukti_foto)
                                <a href="{{ asset('storage/' . $log->bukti_foto) }}" target="_blank" class="inline-block group relative">
                                    <img src="{{ asset('storage/' . $log->bukti_foto) }}" class="w-12 h-12 object-cover rounded-xl border-2 border-white shadow-md group-hover:scale-150 transition-transform duration-300 z-10 cursor-zoom-in">
                                    <div class="absolute inset-0 bg-black/20 rounded-xl group-hover:bg-transparent transition-colors"></div>
                                </a>
                            @else
                                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center border-2 border-dashed border-gray-200 mx-auto text-gray-300">
                                    <i class="fas fa-image-slash"></i>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-folder-open text-4xl text-gray-300"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-600">Tidak ada data ditemukan</h3>
                                <p class="text-gray-400 text-sm mt-1">Coba sesuaikan filter pencarian Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL EXPORT / DOWNLOAD (Modern & Colorful) -->
<!-- ============================================== -->
<div id="exportModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-indigo-900/60 backdrop-blur-sm transition-opacity duration-300" onclick="closeExportModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <!-- Modal Panel -->
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-white/50">

            <!-- Header Modal with Pattern -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-8 py-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
                <div class="flex items-center justify-between relative z-10">
                    <h3 class="text-xl font-bold text-white flex items-center gap-3">
                        <span class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                            <i class="fas fa-file-export"></i>
                        </span>
                        Download Laporan
                    </h3>
                    <button onclick="closeExportModal()" class="text-white/80 hover:text-white transition bg-white/10 hover:bg-white/20 rounded-full w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Form Download -->
            <form id="exportForm" method="GET" action="{{ route('admin.export') }}" class="p-8 space-y-6">

                <!-- 1. Pilih Format -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider">Pilih Format File</label>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Pilihan Excel -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="format" value="excel" class="peer sr-only" checked onchange="updateFormAction('excel')">
                            <div class="p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 peer-checked:border-green-500 peer-checked:bg-green-50/50 transition-all text-center hover:bg-white hover:shadow-md h-full flex flex-col justify-center items-center">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-3 text-green-600 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-excel text-2xl"></i>
                                </div>
                                <p class="font-bold text-gray-800">Excel / CSV</p>
                                <p class="text-[10px] text-gray-500 mt-1">Data Mentah (.csv)</p>
                            </div>
                            <div class="absolute top-3 right-3 text-green-500 opacity-0 peer-checked:opacity-100 transition scale-0 peer-checked:scale-100">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </label>

                        <!-- Pilihan PDF -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="format" value="pdf" class="peer sr-only" onchange="updateFormAction('pdf')">
                            <div class="p-4 rounded-2xl border-2 border-gray-100 bg-gray-50 peer-checked:border-red-500 peer-checked:bg-red-50/50 transition-all text-center hover:bg-white hover:shadow-md h-full flex flex-col justify-center items-center">
                                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-3 text-red-600 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-pdf text-2xl"></i>
                                </div>
                                <p class="font-bold text-gray-800">PDF / Cetak</p>
                                <p class="text-[10px] text-gray-500 mt-1">Siap Print (.pdf)</p>
                            </div>
                            <div class="absolute top-3 right-3 text-red-500 opacity-0 peer-checked:opacity-100 transition scale-0 peer-checked:scale-100">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- 2. Filter Data -->
                <div class="space-y-4">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Filter Data Download</p>

                    <!-- Pegawai -->
                    <div class="relative group">
                        <i class="fas fa-user absolute left-3 top-3.5 text-gray-400"></i>
                        <select name="user_id" class="w-full pl-10 border-gray-200 bg-gray-50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all hover:bg-white py-3">
                            <option value="">-- Semua Pegawai --</option>
                            @foreach($users as $user)
                                @if(!str_contains($user->email, 'admin'))
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Rentang Tanggal -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="block text-[10px] font-bold text-gray-400 mb-1 ml-1">DARI TANGGAL</label>
                            <input type="date" name="start_date" class="w-full border-gray-200 bg-gray-50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all hover:bg-white py-2.5">
                        </div>
                        <div class="relative">
                            <label class="block text-[10px] font-bold text-gray-400 mb-1 ml-1">SAMPAI TANGGAL</label>
                            <input type="date" name="end_date" class="w-full border-gray-200 bg-gray-50 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all hover:bg-white py-2.5">
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeExportModal()" class="w-1/3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-3.5 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="w-2/3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <i class="fas fa-cloud-download-alt animate-bounce"></i> Download
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JAVASCRIPT & STYLE -->
<script>
    const modal = document.getElementById('exportModal');
    const form = document.getElementById('exportForm');

    function openExportModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeExportModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function updateFormAction(format) {
        if (format === 'pdf') {
            form.action = "{{ route('admin.print') }}";
            form.target = "_blank";
        } else {
            form.action = "{{ route('admin.export') }}";
            form.removeAttribute('target');
        }
    }
</script>

<style>
    /* Styling Pagination Laravel agar sesuai tema */
    .pagination { display: flex; justify-content: center; margin-top: 1rem; gap: 0.25rem; }
    .page-item.active .page-link { background: linear-gradient(to right, #4f46e5, #3b82f6); border: none; color: white; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5); }
    .page-link { padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #f3f4f6; color: #4b5563; font-size: 0.875rem; background: white; transition: all 0.2s; }
    .page-link:hover { background-color: #eff6ff; color: #2563eb; transform: translateY(-1px); }
</style>
@endsection
