<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EkVeli extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ek_veliler';

    protected $fillable = [
        'veli_id',
        'isim',
        'tc',
        'meslek',
        'tel',
        'eposta',
        'is_tel',
        'ev_tel',
        'yakinlik',
        'adres'
    ];

    protected $casts = [
        'yakinlik' => 'string',
    ];

    public function veli()
    {
        return $this->belongsTo(Veli::class);
    }
}
