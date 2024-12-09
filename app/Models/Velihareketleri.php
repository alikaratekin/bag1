<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Velihareketleri extends Model
{
    use HasFactory;

    protected $table = 'velihareketleri';

    protected $fillable = [
        'islem_tipi',
        'veli_id',
        'ogrenci_id',
        'tarih',
        'borcu',
        'odedi',
        'hesap_no',
    ];

    public function veli()
    {
        return $this->belongsTo(Veli::class, 'veli_id');
    }
    public function ogrenci()
{
    return $this->belongsTo(Ogrenci::class, 'ogrenci_id');
}
}
