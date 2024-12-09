<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Personel extends Model
{
    use HasFactory, SoftDeletes;

   
    protected $table = 'personeller';

    
    protected $fillable = [
        'isim',
        'e_posta',
        'cep_telefonu',
        'ise_giris_tarihi',
        'isten_ayrilis_tarihi',
        'dogum_tarihi',
        'tc_kimlik_no',
        'aylik_net_maas',
        'banka_hesap_no',
        'departman',
        'adres',
        'banka_bilgileri',
        'not_alani',
        'team_id',
    ];

 
    protected $dates = [
        'ise_giris_tarihi',
        'isten_ayrilis_tarihi',
        'dogum_tarihi',
        'deleted_at', // Soft delete alanı
    ];

}
