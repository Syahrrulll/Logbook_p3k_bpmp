<?php

namespace App\Services;

use App\Models\Logbook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * LogbookAiService
 *
 * Menangani dua fitur AI untuk aplikasi Logbook P3K BPMP:
 *  A) AI Activity Classification  – mengklasifikasikan kategori kegiatan
 *  B) Duplicate Detection         – mendeteksi kegiatan yang terduplikasi
 */
class LogbookAiService
{
    // =========================================================================
    // A. AI ACTIVITY CLASSIFICATION
    // =========================================================================

    /**
     * Kategori yang tersedia beserta kata kunci pendekatan rule-based.
     * Digunakan sebagai fallback jika API Claude tidak tersedia.
     */
    private array $categoryKeywords = [
        'pelatihan' => [
            'bimtek', 'bimbingan teknis', 'workshop', 'pelatihan', 'training',
            'diklat', 'seminar', 'webinar', 'lokakarya', 'in house training',
            'capacity building', 'orientasi', 'sosialisasi',
        ],
        'lapangan' => [
            'kunjungan', 'visitasi', 'supervisi', 'pendampingan', 'monitoring',
            'evaluasi lapangan', 'audit', 'verifikasi lapangan', 'observasi',
            'inspeksi', 'survey', 'pengamatan', 'pemantauan lapangan',
        ],
        'dokumentasi' => [
            'dokumentasi', 'laporan', 'menyusun laporan', 'membuat laporan',
            'notulen', 'berita acara', 'rekap', 'rekapitulasi', 'arsip',
            'pengarsipan', 'pencatatan', 'input data', 'entry data',
            'penyusunan dokumen', 'pengetikan',
        ],
        'pelayanan' => [
            'konsultasi', 'layanan', 'pelayanan', 'menerima tamu',
            'helpdesk', 'pengaduan', 'fasilitasi', 'mediasi', 'koordinasi',
            'advokasi', 'asistensi', 'pendampingan peserta',
        ],
        'administrasi' => [
            'administrasi', 'surat', 'persuratan', 'rapat', 'meeting',
            'koordinasi internal', 'piket', 'absensi', 'presensi',
            'pengelolaan', 'manajemen', 'perencanaan', 'anggaran', 'keuangan',
            'kepegawaian', 'pengadaan', 'disposisi',
        ],
    ];

    /**
     * Mengklasifikasikan kategori kegiatan menggunakan pendekatan hybrid:
     *  1. Coba via Anthropic Claude API (lebih akurat, context-aware)
     *  2. Fallback ke rule-based jika API tidak tersedia
     *
     * @param  string  $kegiatan         Uraian kegiatan yang diinput user
     * @param  string  $sasaranPekerjaan Sasaran pekerjaan / SKP
     * @return array   ['kategori' => string, 'confidence' => float]
     */
    public function classifyActivity(string $kegiatan, string $sasaranPekerjaan): array
    {
        // Coba klasifikasi via Claude API terlebih dahulu
        $apiResult = $this->classifyViaApi($kegiatan, $sasaranPekerjaan);
        if ($apiResult !== null) {
            return $apiResult;
        }

        // Fallback: rule-based keyword matching
        return $this->classifyViaKeywords($kegiatan . ' ' . $sasaranPekerjaan);
    }

    /**
     * Klasifikasi menggunakan Anthropic Claude API
     */
    private function classifyViaApi(string $kegiatan, string $sasaranPekerjaan): ?array
    {
        $apiKey = config('services.anthropic.api_key');
        if (empty($apiKey)) {
            return null; // Tidak ada API key, pakai fallback
        }

        try {
            $prompt = <<<EOT
Anda adalah sistem klasifikasi otomatis untuk logbook pegawai BPMP (Balai Penjaminan Mutu Pendidikan).

Tugasnya: Tentukan kategori aktivitas berdasarkan uraian kegiatan dan sasaran pekerjaan.

Pilihan kategori (pilih SATU yang paling tepat):
- administrasi : kegiatan surat-menyurat, rapat, piket, perencanaan, pengelolaan kepegawaian/keuangan
- lapangan     : kunjungan, visitasi, supervisi, monitoring ke sekolah/instansi, pendampingan di luar kantor
- pelatihan    : bimtek, workshop, diklat, seminar, webinar, training, orientasi, sosialisasi pelatihan
- dokumentasi  : membuat laporan, notulen, berita acara, rekapitulasi, pengarsipan, input data
- pelayanan    : konsultasi, fasilitasi, pendampingan peserta, layanan pengaduan, helpdesk
- lainnya      : jika tidak masuk kategori manapun di atas

Uraian Kegiatan: {$kegiatan}
Sasaran Pekerjaan: {$sasaranPekerjaan}

Jawab HANYA dengan JSON valid seperti ini (tanpa markdown, tanpa penjelasan):
{"kategori":"administrasi","confidence":0.92,"alasan":"singkat 1 kalimat"}
EOT;

            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])->timeout(10)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001', // Model ringan/cepat untuk klasifikasi
                'max_tokens' => 150,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                $content = $response->json('content.0.text', '');
                $parsed  = json_decode(trim($content), true);

                if (
                    isset($parsed['kategori'], $parsed['confidence']) &&
                    in_array($parsed['kategori'], array_merge(array_keys($this->categoryKeywords), ['lainnya']))
                ) {
                    return [
                        'kategori'   => $parsed['kategori'],
                        'confidence' => min(1.0, max(0.0, (float) $parsed['confidence'])),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('LogbookAiService: API classification failed – ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Klasifikasi berbasis kata kunci (rule-based fallback)
     */
    private function classifyViaKeywords(string $text): array
    {
        $text  = strtolower($text);
        $scores = [];

        foreach ($this->categoryKeywords as $category => $keywords) {
            $matchCount = 0;
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) {
                    $matchCount++;
                }
            }
            $scores[$category] = $matchCount;
        }

        $maxScore = max($scores);

        if ($maxScore === 0) {
            return ['kategori' => 'lainnya', 'confidence' => 0.40];
        }

        $bestCategory = array_search($maxScore, $scores);

        // Confidence: sederhana – semakin banyak kata kunci cocok, semakin tinggi
        $totalKeywordsInCategory = count($this->categoryKeywords[$bestCategory]);
        $confidence = min(0.90, 0.50 + ($maxScore / $totalKeywordsInCategory) * 0.40);

        return ['kategori' => $bestCategory, 'confidence' => round($confidence, 2)];
    }

    // =========================================================================
    // B. DUPLICATE DETECTION
    // =========================================================================

    /**
     * Mendeteksi apakah sebuah entri logbook merupakan duplikat dari entri
     * yang sudah ada sebelumnya milik user yang sama.
     *
     * Strategi deteksi:
     *  1. Exact match  – tanggal + lokasi + sasaran_pekerjaan + kegiatan identik
     *
     * @param  Logbook  $newLogbook  Entri baru (belum disimpan, hanya data array juga bisa)
     * @param  array    $data        Data dari request (tanggal, kegiatan, sasaran_pekerjaan, user_id)
     * @return array    ['is_duplicate' => bool, 'duplicate_of' => int|null, 'similarity' => float]
     */
    public function detectDuplicate(array $data): array
    {
        $userId = $data['user_id'];

        // Cari entri di tanggal yang sama milik user yang sama
        $existingOnSameDay = Logbook::where('user_id', $userId)
            ->whereDate('tanggal', $data['tanggal'])
            ->where('is_duplicate', false) // Jangan bandingkan dengan duplikat sebelumnya
            ->get();

        if ($existingOnSameDay->isEmpty()) {
            return ['is_duplicate' => false, 'duplicate_of' => null, 'similarity' => 0.0];
        }

        foreach ($existingOnSameDay as $existing) {
            $sameKegiatan = isset($data['kegiatan']) && strtolower(trim($existing->kegiatan)) === strtolower(trim($data['kegiatan']));
            $sameSasaran  = isset($data['sasaran_pekerjaan']) && strtolower(trim($existing->sasaran_pekerjaan)) === strtolower(trim($data['sasaran_pekerjaan']));
            $sameLokasi   = isset($data['lokasi']) && !empty($data['lokasi']) && isset($existing->lokasi)
                && strtolower(trim($existing->lokasi)) === strtolower(trim($data['lokasi']));

            if ($sameKegiatan && $sameSasaran && $sameLokasi) {
                return [
                    'is_duplicate' => true,
                    'duplicate_of' => $existing->id,
                    'similarity'   => 1.0,
                ];
            }
        }

        return ['is_duplicate' => false, 'duplicate_of' => null, 'similarity' => 0.0];
    }

    /**
     * Menghitung kemiripan dua teks (0.0 – 1.0) menggunakan kombinasi:
     *  - Jaccard similarity pada level kata (bobot 60%)
     *  - similar_text PHP built-in (bobot 40%)
     */
    private function calculateSimilarity(string $a, string $b): float
    {
        $a = strtolower(preg_replace('/\s+/', ' ', trim($a)));
        $b = strtolower(preg_replace('/\s+/', ' ', trim($b)));

        if ($a === $b) return 1.0;
        if (empty($a) || empty($b)) return 0.0;

        // Jaccard similarity berbasis kata
        $wordsA = array_unique(explode(' ', $a));
        $wordsB = array_unique(explode(' ', $b));

        $intersection = count(array_intersect($wordsA, $wordsB));
        $union        = count(array_unique(array_merge($wordsA, $wordsB)));
        $jaccard      = $union > 0 ? $intersection / $union : 0;

        // similar_text bawaan PHP
        similar_text($a, $b, $phpSimilar);
        $phpSimilar = $phpSimilar / 100;

        return round(($jaccard * 0.6) + ($phpSimilar * 0.4), 4);
    }

    // =========================================================================
    // HELPER PUBLIK: scan seluruh data user untuk deteksi duplikat massal
    // (berguna untuk admin atau cron job)
    // =========================================================================

    /**
     * Scan seluruh logbook milik $userId dan tandai duplikat yang belum terdeteksi.
     * Mengembalikan jumlah entri yang baru ditandai.
     */
    public function scanAndMarkDuplicates(int $userId): int
    {
        $count = 0;
        $logs  = Logbook::where('user_id', $userId)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        // Reset semua flag duplikat user ini terlebih dahulu
        Logbook::where('user_id', $userId)->update([
            'is_duplicate' => false,
            'duplicate_of' => null,
        ]);

        $seen = []; // tanggal => [index dari $logs]

        foreach ($logs as $log) {
            $dateKey = $log->tanggal;

            if (!isset($seen[$dateKey])) {
                $seen[$dateKey] = [];
            }

            foreach ($seen[$dateKey] as $existingId) {
                $existing   = $logs->firstWhere('id', $existingId);

            $sameKegiatan = isset($log->kegiatan) && isset($existing->kegiatan)
                && strtolower(trim($log->kegiatan)) === strtolower(trim($existing->kegiatan));
            $sameSasaran = isset($log->sasaran_pekerjaan) && isset($existing->sasaran_pekerjaan)
                && strtolower(trim($log->sasaran_pekerjaan)) === strtolower(trim($existing->sasaran_pekerjaan));
            $sameLokasi = isset($log->lokasi) && isset($existing->lokasi)
                && strtolower(trim($log->lokasi)) === strtolower(trim($existing->lokasi));

            if ($sameKegiatan && $sameSasaran && $sameLokasi) {
                $log->update([
                    'is_duplicate' => true,
                    'duplicate_of' => $existingId,
                ]);
                $count++;
                break;
            }
            }

            if (!$log->is_duplicate) {
                $seen[$dateKey][] = $log->id;
            }
        }

        return $count;
    }
}
