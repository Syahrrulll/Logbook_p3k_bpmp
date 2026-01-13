<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
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

        // Ambil data untuk statistik ringkas
        $logs = $queryStats->orderBy('tanggal', 'desc')->get();

        // 2. Data Feed Galeri (Random dari SEMUA pegawai)
        // Mengambil 6 data acak untuk ditampilkan seperti postingan di dashboard
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
        // Menggunakan pagination 10 item per halaman
        $logs = Logbook::where('user_id', $user->id)
                       ->orderBy('tanggal', 'desc')
                       ->paginate(10);

        return view('logbook.history', compact('logs'));
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

        // Ambil semua data logbook beserta data user pemiliknya
        $query = Logbook::with('user');

        // Fitur Pencarian (Filter berdasarkan nama pegawai)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Tampilkan 15 data per halaman
        $logs = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('admin.monitoring', compact('logs'));
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

        // Ambil data (sama seperti logic monitoring, tapi ambil semua tanpa paginasi)
        $query = Logbook::with('user');

        // Jika ada pencarian, export hasil pencariannya saja
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

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

            // Header Kolom
            fputcsv($file, ['No', 'Nama Pegawai', 'Email', 'Tanggal', 'Waktu', 'Lokasi', 'Sasaran SKP', 'Uraian Kegiatan', 'Output']);

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

        // Ambil data (sama seperti logic monitoring)
        $query = Logbook::with('user');

        // Filter pencarian jika ada
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('tanggal', 'desc')->get();

        return view('admin.print', compact('logs'));
    }

    /**
     * Menyimpan Data Logbook Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'sasaran_pekerjaan' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kegiatan' => 'required|string',
            'output' => 'required|string',
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        $pathFoto = null;
        if ($request->hasFile('bukti_foto')) {
            $pathFoto = $request->file('bukti_foto')->store('bukti_kegiatan', 'public');
        }

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

        // Keamanan: Pastikan pemilik yang update
        if ($logbook->user_id !== Auth::id()) {
            return abort(403);
        }

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
            return abort(403);
        }

        // Hapus file foto dari storage jika ada
        if ($logbook->bukti_foto) {
            Storage::disk('public')->delete($logbook->bukti_foto);
        }

        $logbook->delete();

        return redirect()->route('logbook.history')->with('success', 'Data logbook berhasil dihapus.');
    }
}
