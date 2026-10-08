<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan data legalitas Tenant.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {

            $table->string('nib')
                ->nullable()
                ->after('npwp');

            $table->string('bentuk_badan_usaha')
                ->nullable()
                ->after('nib');

            $table->text('alamat_perusahaan')
                ->nullable()
                ->after('bentuk_badan_usaha');

        });
    }

    /**
     * Mengembalikan struktur database seperti semula.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {

            $table->dropColumn([
                'nib',
                'bentuk_badan_usaha',
                'alamat_perusahaan',
            ]);

        });
    }
};