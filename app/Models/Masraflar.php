<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Masraflar extends Model
{
    use HasFactory, SoftDeletes;

    // Tablo adı
    protected $table = 'masraflar';

    // Kütüphanedeki sütunların değiştirilebilmesi için fillable özelliğini belirtiyoruz.
    protected $fillable = [
        
        'tarih', 
        'kullanici', 
        'masraf_kalemi_id', 
        'aciklama', 
        'kaynak_hesap_no', 
        'tutar', 
        'proje', 
        'team_id'
    ];

    // Tarih formatlarını ayarlıyoruz
    protected $dates = ['tarih', 'deleted_at'];

    // İlişkiler
    public function masrafKalemi()
{
    return $this->belongsTo(MasrafKalemi::class, 'masraf_kalemi_id');
}


    // İlişkili olduğu Team modelini belirtir
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
    public function kaynakHesap()
{
    return $this->morphTo(null, 'hesap_turu', 'kaynak_hesap_no', 'hesap_no');
}

}
