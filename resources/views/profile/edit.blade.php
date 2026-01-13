@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Profil Saya</h2>
            <p class="text-gray-500 text-sm">
                @if(str_contains($user->email, 'admin'))
                    Perbarui keamanan akun administrator anda.
                @else
                    Perbarui informasi akun dan foto profil anda.
                @endif
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-blue-600 font-medium transition flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Kolom Foto Profil (Kiri) -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center sticky top-24">
                <div class="relative inline-block group">
                    <div class="w-32 h-32 mx-auto bg-gray-100 rounded-full overflow-hidden border-4 border-white shadow-md mb-4 relative z-10">
                         @if($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-400 to-cyan-300 text-white text-4xl font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <!-- Dekorasi background blur -->
                    <div class="absolute inset-0 bg-blue-400 rounded-full blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                </div>

                <h3 class="font-bold text-gray-800 text-lg">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ $user->email }}</p>

                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">
                    <i class="fas fa-shield-alt"></i>
                    {{ str_contains($user->email, 'admin') ? 'Administrator' : 'Pegawai ASN' }}
                </div>
            </div>
        </div>

        <!-- Kolom Form (Kanan) -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Upload Foto: HANYA MUNCUL JIKA BUKAN ADMIN -->
                    @if(!str_contains($user->email, 'admin'))
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ganti Foto Profil</label>
                            <div class="flex items-center gap-4">
                                <label class="cursor-pointer bg-gray-50 border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                                    <i class="fas fa-camera"></i> Pilih Foto
                                    <input type="file" name="photo" class="hidden" accept="image/png, image/jpeg, image/jpg">
                                </label>
                                <span class="text-xs text-gray-400">JPG, PNG. Max: 2MB.</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 my-2"></div>
                    @endif

                    <!-- Nama: READONLY JIKA ADMIN -->
                    <div class="relative group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="absolute left-0 top-9 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               @if(str_contains($user->email, 'admin')) readonly class="w-full pl-10 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed"
                               @else class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200" @endif
                               placeholder="Nama Lengkap Anda">
                        @if(str_contains($user->email, 'admin'))
                            <p class="text-[10px] text-gray-400 mt-1">*Nama Administrator tidak dapat diubah.</p>
                        @endif
                    </div>

                    <!-- Email: SELALU BISA DIEDIT -->
                    <div class="relative group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <div class="absolute left-0 top-9 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all duration-200"
                               placeholder="alamat@email.com">
                    </div>

                    <div class="border-t border-gray-100 my-2"></div>

                    <!-- Password -->
                    <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                        <p class="text-sm font-bold text-yellow-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-lock"></i> Ganti Password
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative group">
                                <input type="password" name="password"
                                       class="w-full px-4 py-2 bg-white border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-400 outline-none text-sm transition"
                                       placeholder="Password Baru">
                            </div>
                            <div class="relative group">
                                <input type="password" name="password_confirmation"
                                       class="w-full px-4 py-2 bg-white border border-yellow-200 rounded-lg focus:ring-2 focus:ring-yellow-400 outline-none text-sm transition"
                                       placeholder="Konfirmasi Password">
                            </div>
                        </div>
                        <p class="text-[10px] text-yellow-600 mt-2">*Kosongkan jika tidak ingin mengubah password.</p>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row gap-3 justify-end items-center">
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 hover:text-gray-800 transition text-center text-sm">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                            <i class="fas fa-save"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
