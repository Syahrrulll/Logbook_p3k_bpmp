@extends('layout')

@section('content')
<div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header Admin -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Monitoring Kinerja Pegawai</h2>
            <p class="text-gray-500 text-sm">Panel Admin untuk melihat seluruh logbook yang masuk secara detail.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm">← Dashboard</a>

            <!-- Tombol Export Excel -->
            <a href="{{ route('admin.export', ['search' => request('search')]) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2 shadow transition">
                <i class="fas fa-file-excel"></i> Excel
            </a>

            <!-- Tombol Export PDF (BARU) -->
            <a href="{{ route('admin.print', ['search' => request('search')]) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2 shadow transition">
                <i class="fas fa-file-pdf"></i> PDF / Cetak
            </a>

            <!-- Pencarian Nama Pegawai -->
            <form action="{{ route('admin.monitoring') }}" method="GET" class="relative">
                <input type="text" name="search" placeholder="Cari nama pegawai..." value="{{ request('search') }}"
                       class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none shadow-sm w-64">
                <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
            </form>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-800 text-white uppercase text-xs font-bold">
                    <tr>
                        <th class="px-4 py-4 w-48">Pegawai</th>
                        <th class="px-4 py-4 w-32">Waktu</th>
                        <th class="px-4 py-4 w-40">Lokasi</th>
                        <th class="px-4 py-4 w-1/5">Sasaran SKP</th>
                        <th class="px-4 py-4 w-1/4">Uraian Kegiatan</th>
                        <th class="px-4 py-4 w-40">Output</th>
                        <th class="px-4 py-4 w-24 text-center">Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors text-sm">
                        <!-- 1. Nama Pegawai -->
                        <td class="px-4 py-4 align-top">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ substr($log->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $log->user->name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $log->user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- 2. Waktu & Durasi -->
                        <td class="px-4 py-4 align-top">
                            <div class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->jam_selesai)->format('H:i') }}
                            </div>
                            <!-- Hitung Durasi -->
                            @php
                                $start = \Carbon\Carbon::parse($log->jam_mulai);
                                $end = \Carbon\Carbon::parse($log->jam_selesai);
                                $mins = $end->diffInMinutes($start);
                            @endphp
                            <div class="mt-1">
                                <span class="text-[10px] font-semibold bg-gray-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200">
                                    {{ $mins }} Menit
                                </span>
                            </div>
                        </td>

                        <!-- 3. Lokasi (Terpisah) -->
                        <td class="px-4 py-4 align-top">
                            <div class="flex items-start gap-1 text-gray-700">
                                <span class="text-red-500 text-xs mt-0.5">📍</span>
                                <span>{{ $log->lokasi }}</span>
                            </div>
                        </td>

                        <!-- 4. Sasaran SKP (Terpisah) -->
                        <td class="px-4 py-4 align-top">
                            <span class="bg-blue-50 text-blue-800 text-[10px] font-bold px-2 py-1 rounded border border-blue-100 inline-block leading-relaxed">
                                {{ $log->sasaran_pekerjaan }}
                            </span>
                        </td>

                        <!-- 5. Uraian Kegiatan (Terpisah) -->
                        <td class="px-4 py-4 align-top">
                            <p class="text-gray-800 leading-relaxed">{{ $log->kegiatan }}</p>
                        </td>

                        <!-- 6. Output -->
                        <td class="px-4 py-4 align-top text-gray-600">
                            {{ $log->output }}
                        </td>

                        <!-- 7. Bukti Foto -->
                        <td class="px-4 py-4 align-top text-center">
                            @if($log->bukti_foto)
                                <a href="{{ asset('storage/' . $log->bukti_foto) }}" target="_blank" class="inline-block group relative">
                                    <img src="{{ asset('storage/' . $log->bukti_foto) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-300 shadow-sm group-hover:scale-110 transition-transform bg-white">
                                    <div class="text-[9px] text-gray-400 mt-1 truncate w-16" title="{{ basename($log->bukti_foto) }}">
                                        {{ Str::limit(basename($log->bukti_foto), 8) }}
                                    </div>
                                </a>
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 mx-auto">
                                    <span class="text-[10px] text-gray-400 italic">No File</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-2xl mb-2">📂</span>
                                <p>Belum ada data logbook dari pegawai.</p>
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
@endsection
