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
        'link_bukti', // Tambahkan ini
        'bukti_foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
