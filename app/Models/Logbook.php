<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'lokasi',
        'sasaran_pekerjaan',
        'jam_mulai',
        'jam_selesai',
        'kegiatan',
        'output',
        'link_bukti',
        'bukti_foto',
        // === FITUR BARU ===
        'kategori_ai',      // Kategori hasil klasifikasi AI
        'ai_confidence',    // Skor keyakinan AI (0.00 – 1.00)
        'is_duplicate',     // Flag duplikat
        'duplicate_of',     // ID entri asli jika duplikat
    ];

    protected $casts = [
        'is_duplicate'   => 'boolean',
        'ai_confidence'  => 'float',
    ];

    /** Relasi ke user pemilik logbook */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi ke entri "asli" (jika ini adalah duplikat) */
    public function originalEntry()
    {
        return $this->belongsTo(Logbook::class, 'duplicate_of');
    }

    /** Relasi ke entri duplikat yang merujuk entri ini */
    public function duplicates()
    {
        return $this->hasMany(Logbook::class, 'duplicate_of');
    }

    // =========================================================================
    // LABEL HELPER (untuk tampilan di View)
    // =========================================================================

    /** Label badge kategori AI dengan warna Tailwind */
    public function kategoriLabel(): array
    {
        $map = [
            'administrasi' => ['label' => 'Administrasi', 'color' => 'blue'],
            'lapangan'     => ['label' => 'Lapangan',     'color' => 'green'],
            'pelatihan'    => ['label' => 'Pelatihan',    'color' => 'purple'],
            'dokumentasi'  => ['label' => 'Dokumentasi',  'color' => 'yellow'],
            'pelayanan'    => ['label' => 'Pelayanan',    'color' => 'pink'],
            'lainnya'      => ['label' => 'Lainnya',      'color' => 'gray'],
        ];

        return $map[$this->kategori_ai] ?? ['label' => '—', 'color' => 'gray'];
    }
}
