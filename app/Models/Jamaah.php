<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jamaah extends Model
{
    use HasFactory;
    protected $fillable = ['nama, nik, alamat, tempat_lahir, tanggal_lahir, jenis_kelamin, no_paspor, masa_berlaku_paspor, ktp, kk, foto, paspor, paket, kamar'];
}
