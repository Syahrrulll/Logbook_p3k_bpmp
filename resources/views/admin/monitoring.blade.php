@extends('layout')

@section('title', 'Monitoring Logbook - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">

    <!-- Header dengan Gradient & Statistik -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-6">
            <div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>Monitoring Logbook
                </h2>
                <p class="text-gray-600 mt-2 text-sm">Pantau aktivitas kinerja pegawai secara real-time.</p>
            </div>

            <!-- Tombol Buka Menu Download (Pemicu Modal) -->
            <button onclick="openExportModal()"
                    class="group relative inline-flex items-center justify-center px-8 py-3 font-bold text-white transition-all duration-200 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600 shadow-lg hover:shadow-green-500/30 hover:-translate-y-1">
                <i class="fas fa-cloud-download-alt mr-2 text-lg"></i>
                Menu Download Laporan
                <div class="absolute -top-2 -right-2 w-4 h-4 bg-red-500 rounded-full animate-ping"></div>
            </button>
        </div>

        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg border border-blue-100 group hover:border-blue-300 transition-all">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-50 to-transparent opacity-50"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Entri</p>
                    <div class="flex items-baseline gap-2 mt-2">
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $logs->total() }}</h3>
                        <span class="text-xs text-green-600 font-bold bg-green-100 px-2 py-1 rounded-full"><i class="fas fa-arrow-up"></i> Data</span>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 text-blue-200 group-hover:text-blue-500 transition-colors duration-300">
                    <i class="fas fa-database text-4xl"></i>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg border border-purple-100 group hover:border-purple-300 transition-all">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-purple-50 to-transparent opacity-50"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pegawai Aktif</p>
                    <div class="flex items-baseline gap-2 mt-2">
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $users->count() }}</h3>
                        <span class="text-xs text-purple-600 font-bold bg-purple-100 px-2 py-1 rounded-full">Orang</span>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 text-purple-200 group-hover:text-purple-500 transition-colors duration-300">
                    <i class="fas fa-users text-4xl"></i>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="relative overflow-hidden bg-white rounded-2xl p-6 shadow-lg border border-emerald-100 group hover:border-emerald-300 transition-all">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-emerald-50 to-transparent opacity-50"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Input Hari Ini</p>
                    <div class="flex items-baseline gap-2 mt-2">
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ \App\Models\Logbook::whereDate('tanggal', today())->count() }}</h3>
                        <span class="text-xs text-emerald-600 font-bold bg-emerald-100 px-2 py-1 rounded-full">Baru</span>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 text-emerald-200 group-hover:text-emerald-500 transition-colors duration-300">
                    <i class="fas fa-calendar-day text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER KHUSUS TAMPILAN TABEL -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-gray-800 font-bold flex items-center gap-2">
                <i class="fas fa-filter text-blue-500"></i> Filter Tampilan Tabel
            </h3>
            <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200">
                Filter ini tidak mempengaruhi download
            </span>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.monitoring') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <!-- Filter Pegawai -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Pegawai</label>
                    <select name="user_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
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

                <!-- Filter Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Pencarian -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wide">Cari Kata Kunci</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Kegiatan..." class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Tombol -->
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow flex-1 transition">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.monitoring') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2.5 rounded-lg text-sm font-bold shadow transition" title="Reset">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL DATA -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-64">Pegawai</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-40">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-48">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider w-1/4">Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Output</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider w-24">Bukti</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logs as $index => $log)
                    <tr class="hover:bg-blue-50/50 transition-colors {{ $index % 2 == 0 ? 'bg-gray-50/30' : 'bg-white' }}">
                        <!-- Nama Pegawai -->
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs shrink-0 overflow-hidden shadow-sm">
                                    @if($log->user->profile_photo)
                                        <img src="{{ asset('storage/' . $log->user->profile_photo) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($log->user->name, 0, 2) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm">{{ $log->user->name }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $log->user->email }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Waktu -->
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->jam_selesai)->format('H:i') }}
                            </div>
                        </td>

                        <!-- Lokasi -->
                        <td class="px-6 py-4 align-top">
                            <div class="text-sm text-gray-700 flex items-start gap-1">
                                <i class="fas fa-map-pin text-red-500 mt-1 text-xs"></i>
                                {{ $log->lokasi }}
                            </div>
                        </td>

                        <!-- Kegiatan -->
                        <td class="px-6 py-4 align-top">
                            <span class="inline-block bg-blue-50 text-blue-700 text-[10px] px-2 py-0.5 rounded border border-blue-100 font-medium mb-1">
                                SKP: {{ Str::limit($log->sasaran_pekerjaan, 30) }}
                            </span>
                            <p class="text-sm text-gray-800 leading-relaxed">{{ $log->kegiatan }}</p>
                        </td>

                        <!-- Output -->
                        <td class="px-6 py-4 align-top text-sm text-gray-600">
                            {{ $log->output }}
                        </td>

                        <!-- Bukti -->
                        <td class="px-6 py-4 align-top text-center">
                            @if($log->bukti_foto)
                                <a href="{{ asset('storage/' . $log->bukti_foto) }}" target="_blank" class="inline-block group relative">
                                    <img src="{{ asset('storage/' . $log->bukti_foto) }}" class="w-10 h-10 object-cover rounded-lg border border-gray-200 shadow-sm group-hover:scale-150 transition-transform bg-white z-10">
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">No Img</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                                <p>Tidak ada data ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL EXPORT / DOWNLOAD (Hidden by Default) -->
<!-- ============================================== -->
<div id="exportModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" onclick="closeExportModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <!-- Modal Panel -->
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

            <!-- Header Modal -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-file-export"></i> Download Laporan
                    </h3>
                    <button onclick="closeExportModal()" class="text-blue-100 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Form Download -->
            <!-- Form Action akan diubah via JS tergantung format yang dipilih -->
            <form id="exportForm" method="GET" action="{{ route('admin.export') }}" class="p-6 space-y-6">

                <!-- 1. Pilih Format -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Pilih Format File</label>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Pilihan Excel -->
                        <label class="cursor-pointer relative">
                            <input type="radio" name="format" value="excel" class="peer sr-only" checked onchange="updateFormAction('excel')">
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all text-center hover:bg-gray-50">
                                <i class="fas fa-file-excel text-3xl text-green-600 mb-2"></i>
                                <p class="font-bold text-gray-800">Excel / CSV</p>
                                <p class="text-[10px] text-gray-500">Format Data Tabel</p>
                            </div>
                            <div class="absolute top-2 right-2 text-green-600 opacity-0 peer-checked:opacity-100 transition">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </label>

                        <!-- Pilihan PDF/Word -->
                        <label class="cursor-pointer relative">
                            <input type="radio" name="format" value="pdf" class="peer sr-only" onchange="updateFormAction('pdf')">
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 transition-all text-center hover:bg-gray-50">
                                <i class="fas fa-file-pdf text-3xl text-red-600 mb-2"></i>
                                <p class="font-bold text-gray-800">PDF / Cetak</p>
                                <p class="text-[10px] text-gray-500">Format Dokumen Resmi</p>
                            </div>
                            <div class="absolute top-2 right-2 text-red-600 opacity-0 peer-checked:opacity-100 transition">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- 2. Pilih Filter Data (Terpisah dari filter tabel) -->
                <div class="space-y-4">
                    <p class="text-sm font-bold text-gray-700 uppercase tracking-wide">Filter Data Download</p>

                    <!-- Pegawai -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pegawai (Opsional)</label>
                        <select name="user_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
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
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 bg-gray-50">
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeExportModal()" class="w-1/3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="w-2/3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <i class="fas fa-download"></i> Download Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JAVASCRIPT UNTUK MODAL -->
<script>
    const modal = document.getElementById('exportModal');
    const form = document.getElementById('exportForm');

    // Buka Modal
    function openExportModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Disable scroll body
    }

    // Tutup Modal
    function closeExportModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Enable scroll body
    }

    // Ganti Action Form Berdasarkan Pilihan
    function updateFormAction(format) {
        if (format === 'pdf') {
            form.action = "{{ route('admin.print') }}";
            // Jika PDF, buka di tab baru agar user bisa print
            form.target = "_blank";
        } else {
            form.action = "{{ route('admin.export') }}";
            // Jika Excel, download langsung (tidak perlu tab baru)
            form.removeAttribute('target');
        }
    }
</script>

<style>
    /* Styling Pagination Laravel */
    .pagination { display: flex; justify-content: center; margin-top: 1rem; gap: 0.25rem; }
    .page-item.active .page-link { background-color: #2563eb; border-color: #2563eb; color: white; }
    .page-link { padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; color: #4b5563; font-size: 0.875rem; }
    .page-link:hover { background-color: #f3f4f6; }
</style>
@endsection
