<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class THareketleri extends Model
{
    use HasFactory;

    
    protected $table = 't_hareketleri';

   
    protected $fillable = [
        'islem_tipi',  // İşlem tipi
        'tarih',       // Tarih
        'kullanici',// Kullanıcı ID
        'aciklama',    // Açıklama
        'hesap_no',    // Hesap numarası
        'tutar',       // Tutar
        'tedarikci_id',
        'team_id',     // Takım ID
    ];
}
