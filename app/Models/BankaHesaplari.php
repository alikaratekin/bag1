<?php

namespace App\Models;

use App\Traits\MultiTenantModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankaHesaplari extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'banka_hesaplari'; // Tablo adını açıkça belirtiyoruz
    protected $fillable = [
        'tanım',
        'etiket_rengi',
        'para_birimi',
        'hesap_no',
        'aktiflik_durumu',
        'team_id',
    ];
}
