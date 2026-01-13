@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Edit Logbook</h2>
        <a href="{{ route('logbook.history') }}" class="text-sm text-gray-500 hover:text-blue-600">← Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="bg-yellow-500 px-8 py-4">
            <p class="text-white text-sm font-bold">Mode Edit Data</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('logbook.update', $logbook->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal" value="{{ $logbook->tanggal }}" required class="w-full border-gray-300 rounded-lg p-3 border">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Kegiatan</label>
                        <input type="text" name="lokasi" value="{{ $logbook->lokasi }}" required class="w-full border-gray-300 rounded-lg p-3 border">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Sasaran SKP</label>
                    <input type="text" name="sasaran_pekerjaan" value="{{ $logbook->sasaran_pekerjaan }}" required class="w-full border-gray-300 rounded-lg p-3 border">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jam Mulai</label>
                        <input type="time" name="jam_mulai" value="{{ \Carbon\Carbon::parse($logbook->jam_mulai)->format('H:i') }}" required class="w-full border-gray-300 rounded-lg p-3 border">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jam Selesai</label>
                        <input type="time" name="jam_selesai" value="{{ \Carbon\Carbon::parse($logbook->jam_selesai)->format('H:i') }}" required class="w-full border-gray-300 rounded-lg p-3 border">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Uraian Kegiatan</label>
                    <textarea name="kegiatan" rows="4" required class="w-full border-gray-300 rounded-lg p-3 border">{{ $logbook->kegiatan }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hasil / Output</label>
                    <input type="text" name="output" value="{{ $logbook->output }}" required class="w-full border-gray-300 rounded-lg p-3 border">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Update Bukti Foto (Opsional)</label>
                    @if($logbook->bukti_foto)
                        <div class="mb-2 text-xs text-green-600 flex items-center gap-1">
                            <i class="fas fa-check-circle"></i> File tersimpan: {{ basename($logbook->bukti_foto) }}
                        </div>
                    @endif
                    <input type="file" name="bukti_foto" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition">Simpan Perubahan</button>
                    <a href="{{ route('logbook.history') }}" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-xl shadow text-center transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
