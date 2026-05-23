<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\User;
use App\Services\LogbookAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LogbookController extends Controller
{
    public function __construct(private LogbookAiService $aiService) {}

    // =========================================================================
    // BAGIAN 1: DASHBOARD & USER BIASA
    // =========================================================================

    public function dashboard()
    {
        $user = Auth::user();

        if (str_contains($user->email, 'admin')) {
            $queryStats = Logbook::query();
        } else {
            $queryStats = Logbook::where('user_id', $user->id);
        }

        $logs = $queryStats->orderBy('tanggal', 'desc')->get();

        $feedLogs = Logbook::with('user')
                           ->inRandomOrder()
                           ->limit(6)
                           ->get();

        // === STATISTIK KATEGORI AI (untuk chart di dashboard) ===
        $kategoriStats = Logbook::where('user_id', $user->id)
            ->whereNotNull('kategori_ai')
            ->selectRaw('kategori_ai, COUNT(*) as total')
            ->groupBy('kategori_ai')
            ->pluck('total', 'kategori_ai');

        // === JUMLAH DUPLIKAT MILIK USER ===
        $duplicateCount = Logbook::where('user_id', $user->id)
            ->where('is_duplicate', true)
            ->count();

        return view('dashboard', compact('logs', 'feedLogs', 'kategoriStats', 'duplicateCount'));
    }

    public function create()
    {
        return view('logbook.input');
    }

    public function history()
    {
        $user = Auth::user();

        $logs = Logbook::where('user_id', $user->id)
                       ->with('originalEntry')
                       ->orderBy('tanggal', 'desc')
                       ->paginate(10);

        $rowNumbers = [];
        $startNumber = $logs->firstItem() ?: 1;
        foreach ($logs as $index => $log) {
            $rowNumbers[$log->id] = $startNumber + $index;
        }

        return view('logbook.history', compact('logs', 'rowNumbers'));
    }

    // =========================================================================
    // STORE – dengan AI Classification + Duplicate Detection
    // =========================================================================

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
            'link_bukti'        => 'nullable|url',
            'bukti_foto'        => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // 2. Upload Foto
        $pathFoto = null;
        if ($request->hasFile('bukti_foto')) {
            $pathFoto = $request->file('bukti_foto')->store('bukti_kegiatan', 'public');
        }

        // 3. === FITUR A: AI Activity Classification ===
        $aiResult = $this->aiService->classifyActivity(
            $request->kegiatan,
            $request->sasaran_pekerjaan
        );

        // 4. === FITUR B: Duplicate Detection ===
        $dupResult = $this->aiService->detectDuplicate([
            'user_id'           => Auth::id(),
            'tanggal'           => $request->tanggal,
            'kegiatan'          => $request->kegiatan,
            'sasaran_pekerjaan' => $request->sasaran_pekerjaan,
            'lokasi'            => $request->lokasi ?? null,
        ]);

        // 5. Simpan ke Database
        $logbook = Logbook::create([
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
            // Hasil AI
            'kategori_ai'       => $aiResult['kategori'],
            'ai_confidence'     => $aiResult['confidence'],
            // Hasil Duplikat
            'is_duplicate'      => $dupResult['is_duplicate'],
            'duplicate_of'      => $dupResult['duplicate_of'],
        ]);

        // 6. Pesan notifikasi
        if ($dupResult['is_duplicate']) {
            $similarity = round($dupResult['similarity'] * 100);
            return redirect()->route('logbook.history')
                ->with('warning', "⚠️ Logbook tersimpan, tetapi terdapat kemungkinan duplikat ({$similarity}% mirip dengan entri lain di tanggal yang sama). Mohon cek kembali riwayat Anda.");
        }

        return redirect()->route('logbook.history')
            ->with('success', '✅ Logbook berhasil disimpan.');
    }

    // =========================================================================
    // EDIT & UPDATE – re-klasifikasi AI jika kegiatan diubah
    // =========================================================================

    public function edit($id)
    {
        $logbook = Logbook::findOrFail($id);

        if ($logbook->user_id !== Auth::id()) {
            return redirect()->route('logbook.history')->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        return view('logbook.edit', compact('logbook'));
    }

    public function update(Request $request, $id)
    {
        $logbook = Logbook::findOrFail($id);

        if ($logbook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

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

        $data = $request->except(['bukti_foto']);

        if ($request->hasFile('bukti_foto')) {
            if ($logbook->bukti_foto) {
                Storage::disk('public')->delete($logbook->bukti_foto);
            }
            $data['bukti_foto'] = $request->file('bukti_foto')->store('bukti_kegiatan', 'public');
        }

        // === Re-klasifikasi AI jika kegiatan/sasaran berubah ===
        $kegiatanChanged = $logbook->kegiatan !== $request->kegiatan
                        || $logbook->sasaran_pekerjaan !== $request->sasaran_pekerjaan;

        if ($kegiatanChanged) {
            $aiResult = $this->aiService->classifyActivity(
                $request->kegiatan,
                $request->sasaran_pekerjaan
            );
            $data['kategori_ai']   = $aiResult['kategori'];
            $data['ai_confidence'] = $aiResult['confidence'];

            // Re-cek duplikat
            $dupResult = $this->aiService->detectDuplicate([
                'user_id'           => Auth::id(),
                'tanggal'           => $request->tanggal,
                'kegiatan'          => $request->kegiatan,
                'sasaran_pekerjaan' => $request->sasaran_pekerjaan,
                'lokasi'            => $request->lokasi ?? $logbook->lokasi ?? null,
            ]);
            // Abaikan duplikat dengan dirinya sendiri
            if ($dupResult['duplicate_of'] !== $logbook->id) {
                $data['is_duplicate'] = $dupResult['is_duplicate'];
                $data['duplicate_of'] = $dupResult['duplicate_of'];
            }
        }

        $logbook->update($data);

        return redirect()->route('logbook.history')->with('success', 'Logbook berhasil diperbarui!');
    }

    // =========================================================================
    // DELETE
    // =========================================================================

    public function destroy($id)
    {
        $logbook = Logbook::findOrFail($id);

        if ($logbook->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($logbook->bukti_foto) {
            Storage::disk('public')->delete($logbook->bukti_foto);
        }

        $logbook->delete();

        return redirect()->route('logbook.history')->with('success', 'Data logbook berhasil dihapus.');
    }

    // =========================================================================
    // BAGIAN 2: FITUR ADMIN (MONITORING, EXPORT, PRINT)
    // =========================================================================

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        // === Filter tambahan: kategori AI ===
        if ($request->filled('kategori_ai')) {
            $query->where('kategori_ai', $request->kategori_ai);
        }

        // === Filter tambahan: status duplikat ===
        if ($request->filled('duplicate')) {
            if ($request->duplicate === '1') {
                $query->where('is_duplicate', true);
            } elseif ($request->duplicate === '0') {
                $query->where('is_duplicate', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kegiatan', 'like', "%{$search}%")
                  ->orWhere('sasaran_pekerjaan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('output', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($sub) => $sub->where('name', 'like', "%{$search}%"));
            });
        }

        return $query;
    }

    public function adminMonitoring(Request $request)
    {
        $user = Auth::user();

        if (!str_contains($user->email, 'admin')) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Halaman ini khusus Admin.');
        }

        $users = User::orderBy('name')->get();

        $query = Logbook::with('user');
        $query = $this->applyFilters($query, $request);

        // Jika flag session ada (hasil scan duplikat), naikan entri duplikat ke atas
        if (session('highlight_duplicates')) {
            $query = $query->orderBy('is_duplicate', 'desc');
            // hapus flag setelah dipakai agar hanya berlaku sekali
            session()->forget('highlight_duplicates');
        }

        $logs  = $query->orderBy('tanggal', 'desc')->paginate(15);

        $selectedUser = $request->filled('user_id') ? User::find($request->user_id) : null;

        if ($request->boolean('scan_duplicates')) {
            if ($request->filled('user_id')) {
                $marked = $this->aiService->scanAndMarkDuplicates((int) $request->user_id);
            } else {
                $marked = 0;
                foreach (User::select('id')->cursor() as $u) {
                    $marked += $this->aiService->scanAndMarkDuplicates($u->id);
                }
            }

            // Set session flag for highlighting duplicates
            session(['highlight_duplicates' => true]);

            return redirect()->route('admin.monitoring', $request->except('scan_duplicates'))
                ->with('success', "Scan selesai: {$marked} entri duplikat ditemukan.");
        }

        // === Statistik duplikat untuk info admin ===
        $totalDuplicates = Logbook::where('is_duplicate', true)->count();

        // === Statistik kategori untuk filter badge ===
        $kategoriCounts = Logbook::selectRaw('kategori_ai, COUNT(*) as total')
            ->whereNotNull('kategori_ai')
            ->groupBy('kategori_ai')
            ->pluck('total', 'kategori_ai');

        return view('admin.monitoring', compact(
            'logs', 'users', 'selectedUser',
            'totalDuplicates', 'kategoriCounts'
        ));
    }

    public function showUserLogbook($userId)
    {
        $request = request();
        $request->merge(['user_id' => $userId]);
        return $this->adminMonitoring($request);
    }

    /**
     * Admin: scan ulang duplikat untuk semua atau user tertentu
     */
    public function rescanDuplicates(Request $request)
    {
        $user = Auth::user();
        if (!str_contains($user->email, 'admin')) {
            abort(403);
        }

        $targetUserId = $request->input('user_id');

        if ($targetUserId) {
            $marked = $this->aiService->scanAndMarkDuplicates((int) $targetUserId);
            return back()->with('success', "Scan selesai: {$marked} entri duplikat ditemukan untuk user tersebut.");
        }

        // Scan semua user
        $total = 0;
        foreach (User::all() as $u) {
            $total += $this->aiService->scanAndMarkDuplicates($u->id);
        }
        return back()->with('success', "Scan selesai: {$total} total entri duplikat ditemukan.");
    }

    public function exportLogbooks(Request $request)
    {
        $user = Auth::user();
        if (!str_contains($user->email, 'admin')) abort(403, 'Unauthorized');

        $query = Logbook::with('user');
        $query = $this->applyFilters($query, $request);
        $logs  = $query->orderBy('tanggal', 'desc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=rekap_logbook_" . date('Y-m-d_H-i') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No', 'Nama Pegawai', 'Email', 'Tanggal', 'Waktu',
                'Lokasi', 'Sasaran SKP', 'Uraian Kegiatan', 'Output',
                'Link Bukti', 'Kategori AI', 'Duplikat',  // === kolom baru ===
            ]);

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
                    $log->link_bukti,
                    $log->kategori_ai ?? '—',
                    $log->is_duplicate ? 'Ya' : 'Tidak',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printLogbooks(Request $request)
    {
        $user = Auth::user();
        if (!str_contains($user->email, 'admin')) abort(403, 'Unauthorized');

        $query = Logbook::with('user');
        $query = $this->applyFilters($query, $request);
        $logs  = $query->orderBy('tanggal', 'desc')->get();

        $filterInfo = [];
        if ($request->filled('start_date')) {
            $filterInfo[] = "Periode: " . $request->start_date . " s.d " . $request->end_date;
        }
        if ($request->filled('user_id')) {
            $u = User::find($request->user_id);
            if ($u) $filterInfo[] = "Pegawai: " . $u->name;
        }
        if ($request->filled('kategori_ai')) {
            $filterInfo[] = "Kategori: " . ucfirst($request->kategori_ai);
        }

        return view('admin.print', compact('logs', 'filterInfo'));
    }
}
