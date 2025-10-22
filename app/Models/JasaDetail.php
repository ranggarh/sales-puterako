<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JasaDetail extends Model
{
    protected $primaryKey = 'id_jasa_detail';
    protected $fillable = [
        'id_penawaran',
        'id_jasa',
        'nama_section',
        'no',
        'deskripsi',
        'vol',
        'hari',
        'orang',
        'unit',
        'total',
        'profit',
        'pph'
    ];

    public function penawaran()
    {
        return $this->belongsTo(Penawaran::class, 'id_penawaran');
    }
    public function jasa()
    {
        return $this->belongsTo(Jasa::class, 'id_jasa');
    }
}
