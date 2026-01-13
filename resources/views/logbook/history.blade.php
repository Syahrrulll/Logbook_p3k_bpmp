@extends('layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Logbook</h2>
            <p class="text-gray-500 text-sm">Kelola laporan kinerja harian anda.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('dashboard') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition">← Dashboard</a>
            <a href="{{ route('logbook.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow">+ Tambah Baru</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <span class="mr-2">✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
            <span class="mr-2">⚠️</span> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 w-24">Bukti</th>
                        <th class="px-6 py-4 w-32">Waktu</th>
                        <th class="px-6 py-4 w-1/5">Lokasi</th>
                        <th class="px-6 py-4 w-1/5">Sasaran SKP</th>
                        <th class="px-6 py-4">Uraian Kegiatan</th>
                        <th class="px-6 py-4 w-1/6">Aksi</th> <!-- Kolom Aksi -->
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 align-top">
                            @if($log->bukti_foto)
                                <a href="{{ asset('storage/' . $log->bukti_foto) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $log->bukti_foto) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-300 shadow-sm hover:scale-105 transition-transform">
                                </a>
                            @else
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 border border-gray-200">
                                    <span class="text-[10px]">No Img</span>
                                </div>
                            @endif
                        </td>

                        <td class="px-6 py-4 align-top">
                            <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500 mt-1 mb-2">
                                {{ \Carbon\Carbon::parse($log->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->jam_selesai)->format('H:i') }}
                            </div>
                        </td>

                        <td class="px-6 py-4 align-top text-sm text-gray-700">
                            {{ $log->lokasi }}
                        </td>

                        <td class="px-6 py-4 align-top">
                            <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-1 rounded border border-blue-200 uppercase tracking-wide inline-block">
                                {{ Str::limit($log->sasaran_pekerjaan, 50) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 align-top">
                            <p class="text-gray-800 font-medium text-sm leading-relaxed">{{ $log->kegiatan }}</p>
                            <div class="text-xs text-gray-500 mt-1">Output: {{ $log->output }}</div>
                        </td>

                        <!-- Tombol Aksi -->
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('logbook.edit', $log->id) }}" class="p-2 bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>

                                <form action="{{ route('logbook.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                            <p class="text-xl">📭</p>
                            <p class="mt-2">Belum ada data logbook.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
