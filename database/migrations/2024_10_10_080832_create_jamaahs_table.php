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
        Schema::create('jamaahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->bigInteger('nik');
            $table->string('alamat');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin');
            $table->string('no_paspor');
            $table->date('masa_berlaku_paspor');
            $table->string('no_visa')->nullable();
            $table->date('masa_berlaku_visa')->nullable();
            $table->string('ktp');
            $table->string('kk');
            $table->string('foto');
            $table->string('paspor');
            $table->enum('paket', ['Paket Itikaf', 'Paket Reguler', 'Paket VIP']);
            $table->enum('kamar', ['Quint', 'Quad', 'Triple', 'Double', 'Single']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamaahs');
    }
};
