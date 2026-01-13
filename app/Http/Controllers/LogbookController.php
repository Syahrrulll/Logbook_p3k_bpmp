<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Penting untuk hapus foto lama

class LogbookController extends Controller
{
    // 1. DASHBOARD (Statistik + Feed Galeri)
    public function dashboard()
    {
        $user = Auth::user();

        // Data Statistik (Admin lihat semua, User lihat sendiri)
        $queryStats = str_contains($user->email, 'admin') ? Logbook::query() : Logbook::where('user_id', $user->id);
        $logs = $queryStats->orderBy('tanggal', 'desc')->get();

        // Data Feed Galeri (Random dari SEMUA pegawai untuk ditampilkan di dashboard)
        $feedLogs = Logbook::with('user')
                           ->inRandomOrder()
                           ->limit(6)
                           ->get();

        return view('dashboard', compact('logs', 'feedLogs'));
    }

    // 2. FORM INPUT
    public function create()
    {
        return view('logbook.input');
    }

    // 3. RIWAYAT PRIBADI
    public function history()
    {
        $user = Auth::user();
        // User hanya melihat datanya sendiri di halaman riwayat
        $logs = Logbook::where('user_id', $user->id)->orderBy('tanggal', 'desc')->paginate(10);
        return view('logbook.history', compact('logs'));
    }

    // 4. MONITORING ADMIN
    public function adminMonitoring(Request $request)
    {
        $user = Auth::user();

        if (!str_contains($user->email, 'admin')) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $query = Logbook::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('admin.monitoring', compact('logs'));
    }

    // 5. SIMPAN DATA BARU (STORE)
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
            'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
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

    // === FITUR EDIT & DELETE (YANG SEMPAT HILANG) ===

    // 6. FORM EDIT
    public function edit($id)
    {
        $logbook = Logbook::findOrFail($id);

        // Keamanan: Pastikan yang edit adalah pemilik data
        if ($logbook->user_id !== Auth::id()) {
            return redirect()->route('logbook.history')->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        return view('logbook.edit', compact('logbook'));
    }

    // 7. UPDATE DATA
    public function update(Request $request, $id)
    {
        $logbook = Logbook::findOrFail($id);

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

        $data = $request->except(['bukti_foto']);

        // Cek jika ada upload foto baru
        if ($request->hasFile('bukti_foto')) {
            // Hapus foto lama agar server tidak penuh
            if ($logbook->bukti_foto) {
                Storage::disk('public')->delete($logbook->bukti_foto);
            }
            $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_kegiatan', 'public');
        }

        $logbook->update($data);

        return redirect()->route('logbook.history')->with('success', 'Logbook berhasil diperbarui!');
    }

    // 8. HAPUS DATA (DESTROY)
    public function destroy($id)
    {
        $logbook = Logbook::findOrFail($id);

        if ($logbook->user_id !== Auth::id()) {
            return abort(403);
        }

        // Hapus file foto jika ada
        if ($logbook->bukti_foto) {
            Storage::disk('public')->delete($logbook->bukti_foto);
        }

        $logbook->delete();

        return redirect()->route('logbook.history')->with('success', 'Data logbook berhasil dihapus.');
    }
}
