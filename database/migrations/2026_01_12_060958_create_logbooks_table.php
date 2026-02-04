<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->date('tanggal');
            $table->string('lokasi');
            $table->string('sasaran_pekerjaan');

            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('kegiatan');
            $table->string('output');

            // KOLOM BARU: Link Bukti (Google Drive, dll)
            $table->string('link_bukti')->nullable();

            // Bukti Foto (Upload)
            $table->string('bukti_foto')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
