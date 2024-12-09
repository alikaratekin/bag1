<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasrafKalemi extends Model
{
    use HasFactory, SoftDeletes;

    // Tablo adı
    protected $table = 'masraf_kalemleri';

    // Korumalı alanlar
    protected $fillable = ['ad', 'masraf_grubu_id', 'team_id'];

    // İlişki: Bir masraf kaleminin bir masraf grubuna ait olduğu
    public function masrafGrubu()
    {
        return $this->belongsTo(MasrafGrubu::class, 'masraf_grubu_id');
    }
}
