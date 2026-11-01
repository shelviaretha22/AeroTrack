<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            // BA yang sudah ditandatangani di luar sistem (hasil scan).
            $table->string('berita_acara_final_path')->nullable();
            $table->timestamp('berita_acara_final_uploaded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->dropColumn([
                'berita_acara_final_path',
                'berita_acara_final_uploaded_at',
            ]);
        });
    }
};
