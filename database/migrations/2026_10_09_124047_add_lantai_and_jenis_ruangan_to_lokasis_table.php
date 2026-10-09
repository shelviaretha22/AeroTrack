<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lokasis', function (Blueprint $table) {
            $table->string('lantai')->nullable()->after('terminal');
            $table->string('jenis_ruangan')->nullable()->after('kode_ruang');
        });
    }

    public function down(): void
    {
        Schema::table('lokasis', function (Blueprint $table) {
            $table->dropColumn([
                'lantai',
                'jenis_ruangan',
            ]);
        });
    }
};