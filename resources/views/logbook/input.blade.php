@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8 py-6 sm:py-8">

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 sm:mb-8 gap-4 sm:gap-0">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-cyan-500">
                Formulir Logbook Harian
            </h2>
            <p class="text-gray-600 text-sm sm:text-base mt-1">Isi laporan kinerja Anda dengan lengkap dan akurat</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition-colors group">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-arrow-left text-blue-500"></i>
            </div>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-gradient-to-br from-white to-blue-50/30 rounded-2xl sm:rounded-3xl shadow-2xl border border-blue-100/50 overflow-hidden backdrop-blur-sm">
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 px-6 sm:px-8 py-5 sm:py-6">
            <h3 class="text-lg sm:text-xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-book-open"></i> Form Input Kegiatan
            </h3>
        </div>

        <div class="p-6 sm:p-8">
            <form id="logbookForm" method="POST" action="{{ route('logbook.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Grid Waktu & Lokasi -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Kegiatan</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="w-full border-2 border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 cursor-pointer" onclick="if(this.showPicker) this.showPicker()">
                    </div>
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Kegiatan</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Aula BPMP..." required class="w-full border-2 border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Sasaran SKP -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Sasaran Pekerjaan (SKP)</label>
                    <input type="text" name="sasaran_pekerjaan" value="{{ old('sasaran_pekerjaan') }}" placeholder="Sesuai SKP Tahunan..." required class="w-full border-2 border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Jam Kerja -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jam Mulai</label>
                        <input type="time" name="jam_mulai" value="{{ old('jam_mulai', '07:30') }}" required class="w-full border-2 border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500" onclick="if(this.showPicker) this.showPicker()">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jam Selesai</label>
                        <input type="time" name="jam_selesai" value="{{ old('jam_selesai', '16:00') }}" required class="w-full border-2 border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500" onclick="if(this.showPicker) this.showPicker()">
                    </div>
                </div>

                <!-- Durasi Auto Calculate -->
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-hourglass-half text-blue-500 text-xl"></i>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Estimasi Durasi</p>
                            <p id="durationDisplay" class="text-lg font-bold text-blue-700">8 jam 30 menit</p>
                        </div>
                    </div>
                </div>

                <!-- Uraian -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Uraian Kegiatan</label>
                    <div class="relative">
                        <textarea name="kegiatan" rows="4" placeholder="Deskripsikan kegiatan..." required class="w-full border-2 border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500">{{ old('kegiatan') }}</textarea>
                        <div class="absolute bottom-3 right-3 text-xs text-gray-400"><span id="charCount">0</span> kar</div>
                    </div>
                </div>

                <!-- Output -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Hasil / Output</label>
                    <input type="text" name="output" value="{{ old('output') }}" placeholder="Laporan Selesai..." required class="w-full border-2 border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- INPUT BUKTI (FOTO & LINK) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-xl border border-gray-200">

                    <!-- 1. Upload Foto -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-camera text-pink-500"></i> Upload Foto
                        </label>
                        <div class="relative">
                            <input type="file" name="bukti_foto" id="fileInput" accept="image/*" class="hidden" onchange="previewFile()">
                            <button type="button" onclick="document.getElementById('fileInput').click()"
                                    class="w-full border-2 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-pink-400 hover:bg-pink-50 transition text-gray-500 text-sm">
                                <span id="fileNameDisplay">Pilih Gambar (Maks 5MB)</span>
                            </button>
                        </div>
                        <!-- Preview Container -->
                        <div id="imagePreview" class="hidden mt-2">
                            <img src="" id="previewSrc" class="h-20 w-auto rounded-lg border border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <!-- 2. Link Bukti -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-link text-blue-500"></i> Link Dokumen / Bukti
                        </label>
                        <input type="url" name="link_bukti" value="{{ old('link_bukti') }}" placeholder="https://drive.google.com/..." class="w-full border-2 border-gray-200 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Opsional: Google Drive, Youtube, dll.</p>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                        <i class="fas fa-save mr-2"></i> Simpan Logbook
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Tampilkan Error dari Server (PENTING AGAR TAHU KENAPA TIDAK TERSIMPAN)
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            let errorMsg = '';
            @foreach($errors->all() as $error)
                errorMsg += '{{ $error }}\n';
            @endforeach
            // Gunakan showToast dari layout jika ada, atau alert biasa
            if (typeof showToast === 'function') {
                showToast(errorMsg, 'error');
            } else {
                alert("Gagal Menyimpan:\n" + errorMsg);
            }
        @endif

        // Hitung durasi awal
        calculateDuration();
    });

    // 2. Preview File Foto
    function previewFile() {
        const input = document.getElementById('fileInput');
        const display = document.getElementById('fileNameDisplay');
        const previewContainer = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewSrc');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            display.textContent = file.name; // Ganti teks tombol

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    // 3. Hitung Durasi Otomatis
    const startTime = document.querySelector('input[name="jam_mulai"]');
    const endTime = document.querySelector('input[name="jam_selesai"]');
    const durationDisplay = document.getElementById('durationDisplay');

    function calculateDuration() {
        if (startTime.value && endTime.value) {
            const start = startTime.value.split(':');
            const end = endTime.value.split(':');

            let startMin = parseInt(start[0]) * 60 + parseInt(start[1]);
            let endMin = parseInt(end[0]) * 60 + parseInt(end[1]);
            let diff = endMin - startMin;

            if (diff < 0) diff += 24 * 60; // Handle lewat tengah malam

            const h = Math.floor(diff / 60);
            const m = diff % 60;
            durationDisplay.textContent = `${h} jam ${m} menit`;
        }
    }

    startTime.addEventListener('change', calculateDuration);
    endTime.addEventListener('change', calculateDuration);

    // 4. Hitung Karakter
    const textarea = document.querySelector('textarea[name="kegiatan"]');
    const charCount = document.getElementById('charCount');
    if(textarea) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // 5. Validasi Form Sebelum Submit
    document.getElementById('logbookForm').addEventListener('submit', function(e) {
        let valid = true;
        this.querySelectorAll('[required]').forEach(el => {
            if (!el.value.trim()) {
                el.classList.add('border-red-500');
                valid = false;
            } else {
                el.classList.remove('border-red-500');
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Harap lengkapi semua kolom yang wajib diisi!');
        }
    });
</script>
@endsection
