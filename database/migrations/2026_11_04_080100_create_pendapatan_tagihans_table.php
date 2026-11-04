<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendapatan_tagihans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('kerja_sama_id')
                ->constrained('kerja_samas')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');

            $table->decimal('jumlah_tagihan', 15, 2)->default(0);
            $table->boolean('status_invoice')->default(false);
            $table->date('tanggal_diberikan')->nullable();

            $table->timestamps();

            // Satu tagihan per tenant per bulan.
            $table->unique(['tenant_id', 'tahun', 'bulan']);
            $table->index(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendapatan_tagihans');
    }
};
