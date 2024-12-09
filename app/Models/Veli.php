<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veli extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'veliler';

    protected $fillable = [
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
}
