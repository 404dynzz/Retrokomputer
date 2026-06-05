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
        Schema::create('profil_kasirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('kode_khusus');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('nama_kasir')->nullable();
            $table->string('nama_pembeli')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['nama_kasir', 'nama_pembeli']);
        });

        Schema::dropIfExists('profil_kasirs');
    }
};
