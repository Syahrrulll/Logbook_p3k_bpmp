<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom untuk fitur AI Classification dan Duplicate Detection
     * - kategori_ai  : hasil klasifikasi kategori aktivitas oleh AI
     * - is_duplicate : flag apakah entri ini terdeteksi duplikat
     * - duplicate_of : id logbook yang dianggap "asli" (jika duplikat)
     */
    public function up(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            // Kategori hasil klasifikasi AI
            $table->enum('kategori_ai', [
                'administrasi',
                'lapangan',
                'pelatihan',
                'dokumentasi',
                'pelayanan',
                'lainnya',
            ])->nullable()->after('output');

            // Skor keyakinan AI (0.00 – 1.00)
            $table->decimal('ai_confidence', 3, 2)->nullable()->after('kategori_ai');

            // Flag duplikat
            $table->boolean('is_duplicate')->default(false)->after('ai_confidence');

            // Referensi ke entri asli (nullable; diisi jika is_duplicate = true)
            $table->unsignedBigInteger('duplicate_of')->nullable()->after('is_duplicate');
            $table->foreign('duplicate_of')->references('id')->on('logbooks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropForeign(['duplicate_of']);
            $table->dropColumn(['kategori_ai', 'ai_confidence', 'is_duplicate', 'duplicate_of']);
        });
    }
};
