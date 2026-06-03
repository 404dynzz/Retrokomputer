<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangRusak extends Model
{
    use HasFactory;

    protected $table = 'barang_rusaks';
    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
