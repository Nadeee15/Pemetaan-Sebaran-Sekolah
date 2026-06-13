<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom kondisi_fasilitas jika belum ada
        if (!Schema::hasColumn('sekolah', 'kondisi_fasilitas')) {
            Schema::table('sekolah', function (Blueprint $table) {
                $table->string('kondisi_fasilitas', 20)->nullable()->after('status');
            });
        }

        // Isi data sample secara acak bila kolom masih NULL
        DB::statement("
            UPDATE sekolah
            SET kondisi_fasilitas = (
                CASE (RANDOM() * 2)::INT
                    WHEN 0 THEN 'Baik'
                    WHEN 1 THEN 'Sedang'
                    ELSE 'Minim'
                END
            )
            WHERE kondisi_fasilitas IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn('kondisi_fasilitas');
        });
    }
};
