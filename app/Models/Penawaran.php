<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
    protected $primaryKey = 'id_penawaran';
    protected $fillable = [
        'perihal',
        'nama_perusahaan',
        'pic_perusahaan',
        'pic_admin',
        'no_penawaran',
        'lokasi',
        'tiket',
        'is_best_price',
        'best_price',
        'total',           
        'ppn_persen',      
        'ppn_nominal',     
        'grand_total',
        'note'
    ];

    public function details()
    {
        return $this->hasMany(PenawaranDetail::class, 'id_penawaran');
    }
    public function jasaDetails()
{
    return $this->hasMany(JasaDetail::class, 'id_penawaran');
}
}
