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
        Schema::create('usulan_bukus', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('isbn')->unique();
            $table->string('penulis');
            $table->string('penerbit');
            $table->year('tahun_terbit');
            $table->string('kategori');
            $table->string('pengusul_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan_bukus');
    }
};
