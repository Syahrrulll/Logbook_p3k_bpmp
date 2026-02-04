<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogbookController extends Controller
{
    /**
     * =========================================================================
     * BAGIAN 1: DASHBOARD & USER BIASA
     * =========================================================================
     */

    /**
     * Menampilkan Dashboard Utama
     * Logika:
     * - Admin melihat statistik seluruh pegawai.
     * - User biasa melihat statistik dirinya sendiri.
     * - Feed Galeri menampilkan 6 aktivitas acak dari pegawai lain.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // 1. Logika Statistik (Admin vs User)
        if (str_contains($user->email, 'admin')) {
            // Admin: Query kosong (agar bisa menghitung semua data)
            $queryStats = Logbook::query();
        } else {
            // User: Hanya data miliknya
            $queryStats = Logbook::where('user_id', $user->id);
        }

        // Ambil data logbook diurutkan dari yang terbaru
        $logs = $queryStats->orderBy('tanggal', 'desc')->get();

        // 2. Data Feed Galeri (Random)
        // Mengambil 6 data acak untuk ditampilkan di dashboard sebagai "Feed"
        $feedLogs = Logbook::with('user')
                           ->inRandomOrder()
                           ->limit(6)
                           ->get();

        return view('dashboard', compact('logs', 'feedLogs'));
    }

    /**
     * Menampilkan Halaman Form Input Logbook
     */
    public function create()
    {
        return view('logbook.input');
    }

    /**
     * Menampilkan Riwayat Logbook Pribadi (Tabel)
     */
    public function history()
    {
        $user = Auth::user();

        // User hanya melihat datanya sendiri
        // Pagination 10 item per halaman
        $logs = Logbook::where('user_id', $user->id)
                       ->orderBy('tanggal', 'desc')
                       ->paginate(10);

        return view('logbook.history', compact('logs'));
    }

    /**
     * Menyimpan Data Logbook Baru (Store)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal'           => 'required|date',
            'lokasi'            => 'required|string|max:255',
            'sasaran_pekerjaan' => 'required|string|max:255',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'required',
            'kegiatan'          => 'required|string',
            'output'            => 'required|string',
            'link_bukti'        => 'nullable|url', // Validasi format URL
            'bukti_foto'        => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        // 2. Proses Upload Foto (Jika ada)
        $pathFoto = null;
        if ($request->hasFile('bukti_foto')) {
            $pathFoto = $request->file('bukti_foto')->store('bukti_kegiatan', 'public');
        }

        // 3. Simpan ke Database
        Logbook::create([
            'user_id'           => Auth::id(),
            'tanggal'           => $request->tanggal,
            'lokasi'            => $request->lokasi,
            'sasaran_pekerjaan' => $request->sasaran_pekerjaan,
            'jam_mulai'         => $request->jam_mulai,
            'jam_selesai'       => $request->jam_selesai,
            'kegiatan'          => $request->kegiatan,
            'output'            => $request->output,
            'link_bukti'        => $request->link_bukti,
            'bukti_foto'        => $pathFoto,
        ]);

        return redirect()->route('logbook.history')->with('success', 'Kegiatan berhasil disimpan!');
    }

    /**
     * Menampilkan Form Edit Logbook
     */
    public function edit($id)
    {
        $logbook = Logbook::findOrFail($id);

        // Keamanan: Pastikan yang mengedit adalah pemilik data
        if ($logbook->user_id !== Auth::id()) {
            return redirect()->route('logbook.history')->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        return view('logbook.edit', compact('logbook'));
    }

    /**
     * Memperbarui Data Logbook (Update)
     */
    public function update(Request $request, $id)
    {
        $logbook = Logbook::findOrFail($id);

        // Keamanan: Cek kepemilikan
        if ($logbook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 1. Validasi
        $request->validate([
            'tanggal'           => 'required|date',
            'lokasi'            => 'required|string',
            'sasaran_pekerjaan' => 'required|string',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'required',
            'kegiatan'          => 'required|string',
            'output'            => 'required|string',
            'link_bukti'        => 'nullable|url',
            'bukti_foto'        => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // 2. Ambil data input kecuali foto (karena foto butuh penanganan khusus)
        $data = $request->except(['bukti_foto']);

        // 3. Cek jika ada upload foto baru
        if ($request->hasFile('bukti_foto')) {
            // Hapus foto lama dari storage untuk menghemat ruang
            if ($logbook->bukti_foto) {
                Storage::disk('public')->delete($logbook->bukti_foto);
            }
            // Simpan foto baru
            $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_kegiatan', 'public');
        }

        // 4. Update Database
        $logbook->update($data);

        return redirect()->route('logbook.history')->with('success', 'Logbook berhasil diperbarui!');
    }

    /**
     * Menghapus Data Logbook (Delete)
     */
    public function destroy($id)
    {
        $logbook = Logbook::findOrFail($id);

        // Keamanan: Cek kepemilikan
        if ($logbook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hapus file foto dari storage jika ada
        if ($logbook->bukti_foto) {
            Storage::disk('public')->delete($logbook->bukti_foto);
        }

        // Hapus record dari database
        $logbook->delete();

        return redirect()->route('logbook.history')->with('success', 'Data logbook berhasil dihapus.');
    }

    /**
     * =========================================================================
     * BAGIAN 2: FITUR KHUSUS ADMIN (MONITORING, EXPORT, PRINT)
     * =========================================================================
     */

    /**
     * Helper Function: Menerapkan Filter Query
     * Digunakan oleh: adminMonitoring, exportLogbooks, printLogbooks
     * Agar kita tidak perlu menulis ulang logika filter di 3 tempat berbeda.
     */
    private function applyFilters($query, Request $request)
    {
        // 1. Filter Berdasarkan Pegawai (Per Orang)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 2. Filter Rentang Tanggal (Mulai)
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        // 3. Filter Rentang Tanggal (Selesai)
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // 4. Filter Pencarian Teks (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                  ->orWhere('sasaran_pekerjaan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('output', 'like', "%{$search}%")
                  // Cari juga berdasarkan nama pegawai
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    /**
     * Halaman Monitoring Khusus Admin
     * Menampilkan tabel data semua pegawai dengan fitur filter.
     */
    public function adminMonitoring(Request $request)
    {
        $user = Auth::user();

        // Cek Hak Akses (Hanya email yang mengandung 'admin')
        if (!str_contains($user->email, 'admin')) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Halaman ini khusus Admin.');
        }

        // Ambil daftar semua user untuk dropdown filter
        $users = User::orderBy('name')->get();

        // Mulai Query Logbook dengan Eager Loading user (biar query cepat)
        $query = Logbook::with('user');

        // Terapkan Filter (menggunakan helper di atas)
        $query = $this->applyFilters($query, $request);

        // Ambil data dengan Pagination (15 per halaman)
        $logs = $query->orderBy('tanggal', 'desc')->paginate(15);

        // Jika sedang memfilter User ID tertentu, ambil data user tersebut (untuk judul halaman opsional)
        $selectedUser = null;
        if($request->filled('user_id')){
            $selectedUser = User::find($request->user_id);
        }

        return view('admin.monitoring', compact('logs', 'users', 'selectedUser'));
    }

    /**
     * Helper: Route pintas untuk melihat logbook per orang
     * Contoh penggunaan: <a href="{{ route('admin.user.logbook', $user->id) }}">
     */
    public function showUserLogbook($userId)
    {
        // Kita gunakan logika yang sama dengan adminMonitoring,
        // tapi kita paksa inject user_id ke dalam request.
        $request = request();
        $request->merge(['user_id' => $userId]);

        return $this->adminMonitoring($request);
    }

    /**
     * Fitur Export Data ke CSV (Kompatibel dengan Excel)
     * Mengunduh data sesuai dengan filter yang sedang aktif.
     */
    public function exportLogbooks(Request $request)
    {
        $user = Auth::user();

        if (!str_contains($user->email, 'admin')) {
            abort(403, 'Unauthorized');
        }

        // Query data (gunakan filter yang sama)
        $query = Logbook::with('user');
        $query = $this->applyFilters($query, $request);

        // Ambil SEMUA data (tanpa pagination) untuk diexport
        $logs = $query->orderBy('tanggal', 'desc')->get();

        // Header HTTP untuk download file
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=rekap_logbook_" . date('Y-m-d_H-i') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Fungsi Callback untuk menulis baris CSV
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Tulis Judul Kolom (Header CSV)
            fputcsv($file, [
                'No',
                'Nama Pegawai',
                'Email',
                'Tanggal',
                'Waktu',
                'Lokasi',
                'Sasaran SKP',
                'Uraian Kegiatan',
                'Output',
                'Link Bukti'
            ]);

            // Loop data dan tulis baris per baris
            foreach ($logs as $index => $log) {
                fputcsv($file, [
                    $index + 1,
                    $log->user->name,
                    $log->user->email,
                    $log->tanggal,
                    $log->jam_mulai . ' - ' . $log->jam_selesai,
                    $log->lokasi,
                    $log->sasaran_pekerjaan,
                    $log->kegiatan,
                    $log->output,
                    $log->link_bukti // Sertakan link bukti dalam export
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Fitur Cetak Laporan (View PDF)
     * Menampilkan halaman siap cetak sesuai filter.
     */
    public function printLogbooks(Request $request)
    {
        $user = Auth::user();

        if (!str_contains($user->email, 'admin')) {
            abort(403, 'Unauthorized');
        }

        // Query data (gunakan filter yang sama)
        $query = Logbook::with('user');
        $query = $this->applyFilters($query, $request);

        // Ambil SEMUA data untuk dicetak
        $logs = $query->orderBy('tanggal', 'desc')->get();

        // Siapkan info filter untuk ditampilkan di Kop Surat (Opsional)
        $filterInfo = [];
        if($request->filled('start_date')) $filterInfo[] = "Periode: " . $request->start_date . " s.d " . $request->end_date;
        if($request->filled('user_id')) {
            $u = User::find($request->user_id);
            if($u) $filterInfo[] = "Pegawai: " . $u->name;
        }

        return view('admin.print', compact('logs', 'filterInfo'));
    }
}
