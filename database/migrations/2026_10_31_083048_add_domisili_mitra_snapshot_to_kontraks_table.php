<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('kontraks', function (Blueprint $table) {
        $table->string('domisili_mitra_snapshot')->nullable()->after('alamat_mitra_snapshot');
    });
}

public function down(): void
{
    Schema::table('kontraks', function (Blueprint $table) {
        $table->dropColumn('domisili_mitra_snapshot');
    });
}
};
