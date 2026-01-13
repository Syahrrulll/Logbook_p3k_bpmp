@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8 py-6 sm:py-8">

    <!-- Header dengan gradasi -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 sm:mb-8 gap-4 sm:gap-0">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-cyan-500">
                Formulir Logbook Harian
            </h2>
            <p class="text-gray-600 text-sm sm:text-base mt-1">Isi laporan kinerja Anda dengan lengkap dan akurat</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition-colors group">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-arrow-left text-blue-500"></i>
            </div>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Form Container dengan efek glass -->
    <div class="bg-gradient-to-br from-cyan to-blue-50/30 rounded-2xl sm:rounded-3xl shadow-2xl border border-blue-100/50 overflow-hidden backdrop-blur-sm">
        <!-- Header Form dengan gradasi -->
        <div class="relative overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500 px-6 sm:px-8 py-5 sm:py-6">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl sm:rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-book-open text-white text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-white">Form Input Kegiatan Harian</h3>
                        <p class="text-blue-100 text-sm mt-0.5">Silakan isi data kinerja sesuai Sasaran Kerja Pegawai (SKP)</p>
                    </div>
                </div>
                <!-- Progress Steps -->
                <div class="mt-4 sm:mt-6 flex items-center justify-between max-w-md">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                        <span class="text-white text-xs font-medium">Data Diri</span>
                    </div>
                    <div class="h-1 w-8 bg-white/40"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-blue-400 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-bold">2</span>
                        </div>
                        <span class="text-white text-xs font-medium">Kegiatan</span>
                    </div>
                    <div class="h-1 w-8 bg-white/40"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 text-xs font-bold">3</span>
                        </div>
                        <span class="text-white text-xs font-medium">Selesai</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <div class="p-6 sm:p-8">
            <form method="POST" action="{{ route('logbook.store') }}" enctype="multipart/form-data" class="space-y-6 sm:space-y-8">
                @csrf

                <!-- User Info Card -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-blue-100">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-full flex items-center justify-center shadow-md">
                            <i class="fas fa-user text-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Sedang mengisi sebagai:</p>
                            <p class="font-bold text-gray-800 text-sm sm:text-base">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="ml-auto text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                            {{ str_contains(Auth::user()->email, 'admin') ? 'Administrator' : 'Pegawai' }}
                        </div>
                    </div>
                </div>

                <!-- Grid Pertama -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Tanggal -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-md flex items-center justify-center">
                                <i class="fas fa-calendar text-white text-xs"></i>
                            </div>
                            <span>Tanggal Kegiatan</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                   class="w-full border-2 border-gray-200 rounded-xl sm:rounded-2xl p-3.5 sm:p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 bg-white shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-blue-300 cursor-pointer"
                                   onclick="this.showPicker()">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-blue-400">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Lokasi -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-gradient-to-br from-green-500 to-emerald-400 rounded-md flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-white text-xs"></i>
                            </div>
                            <span>Lokasi Kegiatan</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="lokasi" placeholder="Contoh: Aula BPMP, SMKN 1 Kendari..." required
                                   class="w-full border-2 border-gray-200 rounded-xl sm:rounded-2xl p-3.5 sm:p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 bg-white shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-green-300">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-green-400">
                                <i class="fas fa-location-dot"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sasaran SKP -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-pink-400 rounded-md flex items-center justify-center">
                            <i class="fas fa-bullseye text-white text-xs"></i>
                        </div>
                        <span>Sasaran Pekerjaan (SKP)</span>
                        <span class="text-xs text-gray-500 ml-auto">Wajib diisi sesuai SKP</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="sasaran_pekerjaan" placeholder="Contoh: Meningkatnya layanan administrasi..." required
                               class="w-full border-2 border-gray-200 rounded-xl sm:rounded-2xl p-3.5 sm:p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 bg-white shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-purple-300">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-purple-400">
                            <i class="fas fa-target"></i>
                        </div>
                    </div>
                </div>

                <!-- Jam Kerja -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Jam Mulai -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-gradient-to-br from-orange-500 to-amber-400 rounded-md flex items-center justify-center">
                                <i class="fas fa-play text-white text-xs"></i>
                            </div>
                            <span>Jam Mulai</span>
                        </label>
                        <div class="relative">
                            <input type="time" name="jam_mulai" value="07:30" required
                                   class="w-full border-2 border-gray-200 rounded-xl sm:rounded-2xl p-3.5 sm:p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 bg-white shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-orange-300 cursor-pointer"
                                   onclick="this.showPicker()">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-orange-400">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5">Waktu mulai kegiatan</p>
                    </div>

                    <!-- Jam Selesai -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-gradient-to-br from-red-500 to-pink-400 rounded-md flex items-center justify-center">
                                <i class="fas fa-stop text-white text-xs"></i>
                            </div>
                            <span>Jam Selesai</span>
                        </label>
                        <div class="relative">
                            <input type="time" name="jam_selesai" value="16:00" required
                                   class="w-full border-2 border-gray-200 rounded-xl sm:rounded-2xl p-3.5 sm:p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 bg-white shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-red-300 cursor-pointer"
                                   onclick="this.showPicker()">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-red-400">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5">Waktu selesai kegiatan</p>
                    </div>
                </div>

                <!-- Durasi Auto Calculate -->
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-300 rounded-lg flex items-center justify-center">
                                <i class="fas fa-hourglass-half text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Durasi Kegiatan</p>
                                <p id="durationDisplay" class="text-lg font-bold text-blue-600">8 jam 30 menit</p>
                            </div>
                        </div>
                        <div class="text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full">
                            <i class="fas fa-calculator mr-1"></i> Terhitung Otomatis
                        </div>
                    </div>
                </div>

                <!-- Uraian Kegiatan -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-indigo-500 to-blue-400 rounded-md flex items-center justify-center">
                            <i class="fas fa-tasks text-white text-xs"></i>
                        </div>
                        <span>Uraian Kegiatan</span>
                        <span class="text-xs text-gray-500 ml-auto">Jelaskan secara detail</span>
                    </label>
                    <div class="relative">
                        <textarea name="kegiatan" rows="4" placeholder="Deskripsikan kegiatan yang Anda lakukan hari ini secara detail..." required
                                  class="w-full border-2 border-gray-200 rounded-xl sm:rounded-2xl p-3.5 sm:p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 bg-white shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-indigo-300 resize-none"></textarea>
                        <div class="absolute right-3 top-3 text-indigo-400">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                            <span id="charCount">0</span>/1000 karakter
                        </div>
                    </div>
                </div>

                <!-- Hasil / Output -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-green-500 to-emerald-400 rounded-md flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-xs"></i>
                        </div>
                        <span>Hasil / Output</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="output" placeholder="Contoh: Laporan Selesai, Dokumen Terverifikasi, Rapat Selesai..." required
                               class="w-full border-2 border-gray-200 rounded-xl sm:rounded-2xl p-3.5 sm:p-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-400 bg-white shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-green-300">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-green-400">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                    </div>
                </div>

                <!-- Upload Bukti Foto -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <div class="w-6 h-6 bg-gradient-to-br from-pink-500 to-rose-400 rounded-md flex items-center justify-center">
                            <i class="fas fa-camera text-white text-xs"></i>
                        </div>
                        <span>Upload Bukti Foto</span>
                        <span class="text-xs text-gray-500 ml-auto">Opsional, maks. 5MB</span>
                    </label>

                    <div class="relative">
                        <div class="border-3 border-dashed border-gray-300 rounded-xl sm:rounded-2xl p-6 sm:p-8 text-center transition-all duration-500 group-hover:border-pink-400 group-hover:bg-pink-50/50 cursor-pointer bg-white shadow-sm hover:shadow-md"
                             onclick="document.getElementById('fileInput').click()">
                            <input type="file" name="bukti_foto" id="fileInput" accept="image/*" class="hidden" onchange="previewFile()">

                            <!-- Upload Placeholder -->
                            <div id="uploadPlaceholder" class="space-y-4">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto bg-gradient-to-br from-blue-100 to-pink-100 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-cloud-upload-alt text-2xl sm:text-3xl text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Seret & lepas atau klik untuk upload</p>
                                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG (Maks. 5MB)</p>
                                </div>
                                <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-full">
                                    <i class="fas fa-image"></i>
                                    <span>Pilih File</span>
                                </div>
                            </div>

                            <!-- File Preview -->
                            <div id="filePreview" class="hidden">
                                <div class="space-y-4">
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-xl overflow-hidden border-4 border-white shadow-lg">
                                        <img id="previewImage" class="w-full h-full object-cover" src="" alt="Preview">
                                    </div>
                                    <div>
                                        <p id="fileName" class="font-medium text-green-600"></p>
                                        <p id="fileSize" class="text-sm text-gray-500"></p>
                                    </div>
                                    <button type="button" onclick="removeFile()" class="inline-flex items-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-full transition-colors">
                                        <i class="fas fa-trash"></i>
                                        <span>Hapus Foto</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Camera Icon -->
                        <div class="absolute top-4 right-4 text-pink-400">
                            <i class="fas fa-camera-retro text-lg"></i>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 sm:pt-6">
                    <button type="submit"
                            class="group w-full bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold py-4 px-6 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 active:scale-95">
                        <div class="flex items-center justify-center gap-3">
                            <i class="fas fa-save text-xl group-hover:rotate-12 transition-transform duration-300"></i>
                            <span class="text-lg">Simpan Logbook</span>
                            <i class="fas fa-arrow-right text-xl opacity-0 group-hover:opacity-100 transform group-hover:translate-x-2 transition-all duration-300"></i>
                        </div>
                        <p class="text-sm text-blue-200 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Data akan tersimpan di sistem dan dapat dilihat di riwayat
                        </p>
                    </button>

                    <!-- Quick Tips -->
                    <div class="mt-4 p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-100">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-lightbulb text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700 font-medium">Tips: Pastikan data yang diisi sesuai dengan kegiatan yang sebenarnya dilakukan.</p>
                                <p class="text-xs text-gray-500 mt-1">Data logbook akan digunakan untuk penilaian kinerja.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Counter -->
    <div class="mt-6 sm:mt-8 text-center">
        <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-sm px-4 py-2 rounded-full">
            <i class="fas fa-shield-alt"></i>
            <span>Formulir ini aman dan terenkripsi</span>
        </div>
        <p class="text-xs text-gray-500 mt-2">Data Anda terlindungi dengan sistem keamanan terbaik</p>
    </div>
</div>

<script>
// File Preview Function
function previewFile() {
    const input = document.getElementById('fileInput');
    const placeholder = document.getElementById('uploadPlaceholder');
    const preview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const previewImage = document.getElementById('previewImage');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImage.src = e.target.result;
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
            fileName.textContent = file.name;
            fileSize.textContent = formatBytes(file.size);

            // Add animation
            preview.style.animation = 'fadeIn 0.5s ease-in';
        }

        reader.readAsDataURL(file);
    }
}

// Remove File Function
function removeFile() {
    const input = document.getElementById('fileInput');
    const placeholder = document.getElementById('uploadPlaceholder');
    const preview = document.getElementById('filePreview');

    input.value = '';
    placeholder.classList.remove('hidden');
    preview.classList.add('hidden');
}

// Format Bytes
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// Character Counter for Textarea
const textarea = document.querySelector('textarea[name="kegiatan"]');
const charCount = document.getElementById('charCount');

if (textarea) {
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;

        // Change color based on length
        if (this.value.length > 900) {
            charCount.classList.remove('text-gray-400');
            charCount.classList.add('text-red-500');
        } else if (this.value.length > 700) {
            charCount.classList.remove('text-gray-400', 'text-red-500');
            charCount.classList.add('text-orange-500');
        } else {
            charCount.classList.remove('text-gray-400', 'text-red-500', 'text-orange-500');
            charCount.classList.add('text-gray-400');
        }
    });
}

// Calculate Duration
const startTime = document.querySelector('input[name="jam_mulai"]');
const endTime = document.querySelector('input[name="jam_selesai"]');
const durationDisplay = document.getElementById('durationDisplay');

function calculateDuration() {
    if (startTime.value && endTime.value) {
        const start = startTime.value.split(':');
        const end = endTime.value.split(':');

        let startHour = parseInt(start[0]);
        let startMinute = parseInt(start[1]);
        let endHour = parseInt(end[0]);
        let endMinute = parseInt(end[1]);

        // Convert to minutes
        let startTotal = startHour * 60 + startMinute;
        let endTotal = endHour * 60 + endMinute;

        // Calculate difference
        let diff = endTotal - startTotal;

        if (diff < 0) {
            diff += 24 * 60; // Handle overnight
        }

        const hours = Math.floor(diff / 60);
        const minutes = diff % 60;

        durationDisplay.textContent = `${hours} jam ${minutes} menit`;

        // Color coding based on duration
        if (hours >= 8) {
            durationDisplay.classList.remove('text-blue-600', 'text-orange-600');
            durationDisplay.classList.add('text-green-600');
        } else if (hours >= 4) {
            durationDisplay.classList.remove('text-blue-600', 'text-green-600');
            durationDisplay.classList.add('text-orange-600');
        } else {
            durationDisplay.classList.remove('text-green-600', 'text-orange-600');
            durationDisplay.classList.add('text-blue-600');
        }
    }
}

// Event listeners for time inputs
if (startTime && endTime) {
    startTime.addEventListener('change', calculateDuration);
    endTime.addEventListener('change', calculateDuration);
    calculateDuration(); // Initial calculation
}

// Drag and drop for file upload
const dropZone = document.querySelector('.border-dashed');
if (dropZone) {
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const input = document.getElementById('fileInput');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(files[0]);
            input.files = dataTransfer.files;
            previewFile();
        }
    });
}

// Form validation on submit
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;

            // Add shake animation
            field.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                field.style.animation = '';
            }, 500);
        } else {
            field.classList.remove('border-red-500');
        }
    });

    if (!isValid) {
        e.preventDefault();

        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50';
        errorDiv.innerHTML = `
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <span>Harap isi semua bidang yang wajib diisi!</span>
            </div>
        `;
        document.body.appendChild(errorDiv);

        setTimeout(() => {
            errorDiv.remove();
        }, 3000);
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    input:focus, textarea:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .group:hover label {
        color: #2563eb;
    }
`;
document.head.appendChild(style);
</script>
@endsection
