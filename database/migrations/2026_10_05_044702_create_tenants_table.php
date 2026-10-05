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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Informasi Tenant
            $table->string('nama_tenant');
            $table->string('jenis_usaha');
            $table->string('bentuk_kerja_sama');

            // Identitas Perusahaan
            $table->string('npwp')->nullable();

            // Informasi PIC
            $table->string('nama_pic');
            $table->string('jabatan_pic')->nullable();
            $table->string('no_hp_pic');
            $table->string('email_pic')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};