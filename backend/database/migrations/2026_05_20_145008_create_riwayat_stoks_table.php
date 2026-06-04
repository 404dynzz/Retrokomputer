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
        Schema::create('riwayat_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->integer('qty');
            $table->string('sumber'); // e.g., 'Penjualan', 'Pembelian', 'Barang Rusak'
            $table->unsignedBigInteger('referensi_id')->nullable(); // ID trx/pembelian/retur
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_stoks');
    }
};
