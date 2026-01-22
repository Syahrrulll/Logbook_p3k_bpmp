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
     * Menampilkan Dashboard Utama
     * - User Biasa: Melihat statistik pribadi dan feed galeri.
     * - Admin: Melihat statistik keseluruhan dan feed galeri.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // 1. Data untuk Statistik
        // Jika admin, ambil query kosong (nanti difilter di view atau ambil semua)
        // Jika user biasa, filter berdasarkan ID user
        $queryStats = str_contains($user->email, 'admin') ? Logbook::query() : Logbook::where('user_id', $user->id);

        // Ambil data untuk statistik ringkas, diurutkan dari yang terbaru
        $logs = $queryStats->orderBy('tanggal', 'desc')->get();

        // 2. Data Feed Galeri (Random dari SEMUA pegawai)
        // Mengambil 6 data acak untuk ditampilkan seperti postingan di dashboard
        // Menggunakan 'with user' agar nama pegawai pemilik logbook bisa ditampilkan
        $feedLogs = Logbook::with('user')
                           ->inRandomOrder()
                           ->limit(6)
                           ->get();

        return view('dashboard', compact('logs', 'feedLogs'));
    }

    /**
     * Menampilkan Halaman Input Logbook
     */
    public function create()
    {
        return view('logbook.input');
    }

    /**
     * Menampilkan Riwayat Logbook Pribadi (User)
     */
    public function history()
    {
        $user = Auth::user();

        // User hanya melihat datanya sendiri di halaman riwayat
        // Menggunakan pagination 10 item per halaman agar tidak berat
        $logs = Logbook::where('user_id', $user->id)
                       ->orderBy('tanggal', 'desc')
                       ->paginate(10);

        return view('logbook.history', compact('logs'));
    }

    /**
     * Menyimpan Data Logbook Baru ke Database
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'sasaran_pekerjaan' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kegiatan' => 'required|string',
            'output' => 'required|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Maksimal 5MB
        ]);

        // Proses Upload Foto (Jika ada)
        $pathFoto = null;
        if ($request->hasFile('bukti_foto')) {
            $pathFoto = $request->file('bukti_foto')->store('bukti_kegiatan', 'public');
        }

        // Simpan Data ke Database
        Logbook::create([
            'user_id' => Auth::id(),
            'tanggal' => $request->tanggal,
            'lokasi' => $request->lokasi,
            'sasaran_pekerjaan' => $request->sasaran_pekerjaan,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kegiatan' => $request->kegiatan,
            'output' => $request->output,
            'bukti_foto' => $pathFoto,
        ]);

        return redirect()->route('logbook.history')->with('success', 'Kegiatan berhasil disimpan!');
    }

    /**
     * Menampilkan Form Edit Logbook
     */
    public function edit($id)
    {
        $logbook = Logbook::findOrFail($id);

        // Keamanan: Pastikan yang edit adalah pemilik data
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

        // Keamanan: Pastikan pemilik yang melakukan update
        if ($logbook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validasi Input
        $request->validate([
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'sasaran_pekerjaan' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kegiatan' => 'required|string',
            'output' => 'required|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Ambil semua input kecuali foto (foto diproses terpisah)
        $data = $request->except(['bukti_foto']);

        // Cek jika ada upload foto baru
        if ($request->hasFile('bukti_foto')) {
            // Hapus foto lama dari storage agar server tidak penuh
            if ($logbook->bukti_foto) {
                Storage::disk('public')->delete($logbook->bukti_foto);
            }
            // Simpan foto baru
            $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_kegiatan', 'public');
        }

        // Update data di database
        $logbook->update($data);

        return redirect()->route('logbook.history')->with('success', 'Logbook berhasil diperbarui!');
    }

    /**
     * Menghapus Data Logbook (Delete)
     */
    public function destroy($id)
    {
        $logbook = Logbook::findOrFail($id);

        // Keamanan: Pastikan pemilik yang menghapus
        if ($logbook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hapus file foto dari storage jika ada
        if ($logbook->bukti_foto) {
            Storage::disk('public')->delete($logbook->bukti_foto);
        }

        // Hapus data dari database
        $logbook->delete();

        return redirect()->route('logbook.history')->with('success', 'Data logbook berhasil dihapus.');
    }

    // =========================================================================
    // FITUR KHUSUS ADMIN (MONITORING, EXPORT, PRINT)
    // =========================================================================

    /**
     * Helper function untuk menerapkan filter pencarian
     * Digunakan oleh: adminMonitoring, exportLogbooks, printLogbooks
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by User (Per Orang)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Rentang Tanggal (Start Date)
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        // Filter Rentang Tanggal (End Date)
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Pencarian Text (Nama atau Kegiatan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                  ->orWhere('sasaran_pekerjaan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    /**
     * Halaman Monitoring Khusus Admin
     */
    public function adminMonitoring(Request $request)
    {
        $user = Auth::user();

        // Cek Hak Akses (Hanya email yang mengandung 'admin' yang boleh akses)
        if (!str_contains($user->email, 'admin')) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Halaman ini khusus Admin.');
        }

        // Ambil list semua user untuk dropdown filter
        $users = User::orderBy('name')->get();

        // Query Utama: Ambil logbook beserta data user
        $query = Logbook::with('user');

        // Terapkan filter dari request
        $query = $this->applyFilters($query, $request);

        // Tampilkan 15 data per halaman, urutkan dari terbaru
        $logs = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('admin.monitoring', compact('logs', 'users'));
    }

    /**
     * Fitur Export Data ke CSV (Bisa dibuka di Excel)
     */
    public function exportLogbooks(Request $request)
    {
        $user = Auth::user();

        if (!str_contains($user->email, 'admin')) {
            abort(403, 'Unauthorized');
        }

        // Query data dengan filter yang sama seperti di monitoring
        $query = Logbook::with('user');
        $query = $this->applyFilters($query, $request);
        $logs = $query->orderBy('tanggal', 'desc')->get();

        // Konfigurasi Header untuk Download File CSV
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=rekap_logbook_" . date('Y-m-d_H-i') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Callback untuk menulis isi CSV
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Tulis Header Kolom Excel
            fputcsv($file, ['No', 'Nama Pegawai', 'Email', 'Tanggal', 'Waktu', 'Lokasi', 'Sasaran SKP', 'Uraian Kegiatan', 'Output']);

            // Tulis Data Baris per Baris
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
                    $log->output
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Fitur Cetak Laporan (PDF via Browser Print)
     */
    public function printLogbooks(Request $request)
    {
        $user = Auth::user();

        if (!str_contains($user->email, 'admin')) {
            abort(403, 'Unauthorized');
        }

        // Query data dengan filter yang sama
        $query = Logbook::with('user');
        $query = $this->applyFilters($query, $request);
        $logs = $query->orderBy('tanggal', 'desc')->get();

        return view('admin.print', compact('logs'));
    }
}
