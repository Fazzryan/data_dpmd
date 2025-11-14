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
        Schema::create('bantuan_provinsi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kecamatan');
            $table->string('nama_desa');

            // Pagu Prioritas
            $table->decimal('tpapd', 15, 2)->nullable();
            $table->decimal('bpd', 15, 2)->nullable();
            $table->decimal('fisik', 15, 2)->nullable();
            $table->decimal('total_banprov', 15, 2)->nullable();

            // Lainnya
            $table->boolean('lolos_verifikasi')->default(false);
            $table->boolean('sudah_cair')->default(false);
            $table->year('tahun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bantuan_provinsi');
    }
};
