<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hareketler extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hareketler'; // Tablo adını belirtiyoruz
    protected $fillable = [
        'tarih',
        'islem_tipi',
        'giden',
        'gelen',
        'aciklama',
        'kaynak_hesap_no',
        'hedef_hesap_no',
        'team_id',
        'kullanici',
    ];

    // İlişkileri tanımlama örneği
    public function kaynakKasa()
    {
        return $this->belongsTo(KasaTanimlari::class, 'kaynak_hesap_no', 'hesap_no');
    }

    public function hedefKasa()
    {
        return $this->belongsTo(KasaTanimlari::class, 'hedef_hesap_no', 'hesap_no');
    }

    public function kaynakBankaHesap()
    {
        return $this->belongsTo(BankaHesaplari::class, 'kaynak_hesap_no', 'hesap_no');
    }

    public function hedefBankaHesap()
    {
        return $this->belongsTo(BankaHesaplari::class, 'hedef_hesap_no', 'hesap_no');
    }

    public function kaynakPosHesap()
    {
        return $this->belongsTo(POSHesaplari::class, 'kaynak_hesap_no', 'hesap_no');
    }

    public function hedefPosHesap()
    {
        return $this->belongsTo(POSHesaplari::class, 'hedef_hesap_no', 'hesap_no');
    }

    public function kaynakKrediKart()
    {
        return $this->belongsTo(KrediKartlari::class, 'kaynak_hesap_no', 'hesap_no');
    }

    public function hedefKrediKart()
    {
        return $this->belongsTo(KrediKartlari::class, 'hedef_hesap_no', 'hesap_no');
    }
}
