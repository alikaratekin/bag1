<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ogrenci extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ogrenciler';

    protected $fillable = [
        'veli_id',
        'isim',
        'tc',
        'cinsiyet',
        'dogum_tarihi',
        'mudureyet',
        'sinifi',
        'egitim_donemi',
        'kontenjan',
        'egitimucreti',
        'yemekucreti',
        'etutucreti',
        'kirtasiyeucreti'
    ];

    protected $casts = [
        'dogum_tarihi' => 'date',
        'mudureyet' => 'string',
        'cinsiyet' => 'string',
        'kontenjan' => 'string',
        'egitim_donemi' => 'string'
    ];

    public function veli()
    {
        return $this->belongsTo(Veli::class);
    }
}
