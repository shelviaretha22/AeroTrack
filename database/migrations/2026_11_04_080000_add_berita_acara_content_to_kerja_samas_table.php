<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            // Isi Berita Acara hasil edit di editor (HTML).
            $table->longText('berita_acara_content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kerja_samas', function (Blueprint $table) {
            $table->dropColumn('berita_acara_content');
        });
    }
};
