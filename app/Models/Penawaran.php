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
        'tiket'
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
