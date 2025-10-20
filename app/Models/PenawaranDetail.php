<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenawaranDetail extends Model
{
    protected $primaryKey = 'id_penawaran_detail';
    protected $fillable = [
        'id_penawaran',
        'area',
        'nama_section',
        'no',
        'tipe',
        'deskripsi',
        'qty',
        'satuan',
        'harga_satuan',
        'harga_total',
        'hpp',
        'profit'
    ];

    public function penawaran()
    {
        return $this->belongsTo(Penawaran::class, 'id_penawaran');
    }
}
