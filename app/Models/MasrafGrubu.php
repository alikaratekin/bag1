<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasrafGrubu extends Model
{
    use HasFactory, SoftDeletes;

    // Tablo adı
    protected $table = 'masraf_gruplari';

    // Korumalı alanlar
    protected $fillable = ['ad', 'team_id'];

    // İlişki: Bir masraf grubunun birden fazla masraf kalemi olabilir
    public function masrafKalemleri()
    {
        return $this->hasMany(MasrafKalemi::class, 'masraf_grubu_id');
    }
}
