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
        Schema::create('dana_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kecamatan');
            $table->string('nama_desa');

            // Pagu Prioritas
            $table->decimal('pagu_blt', 15, 2)->nullable();
            $table->decimal('pagu_ketahanan_pangan', 15, 2)->nullable();
            $table->decimal('pagu_stunting', 15, 2)->nullable();
            $table->decimal('pagu_proklim', 15, 2)->nullable();
            $table->decimal('pagu_potensi_desa', 15, 2)->nullable();
            $table->decimal('pagu_ti', 15, 2)->nullable();
            $table->decimal('pagu_padat_karya', 15, 2)->nullable();

            // Tambahan
            $table->decimal('total_pagu_prioritas', 15, 2)->default(0); // otomatis dihitung
            $table->decimal('pagu_non_prioritas', 15, 2)->nullable();
            $table->decimal('total_dana_desa', 15, 2)->default(0); // otomatis dihitung

            // Lainnya
            // $table->boolean('tahap')->default(false);
            // $table->boolean('status_realisasi')->default(false);
            // Status dan tahap
            $table->string('status_realisasi')->nullable();
            $table->string('tahap')->nullable();
            $table->year('tahun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dana_desa');
    }
};
