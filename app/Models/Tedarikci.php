<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tedarikci extends Model
{
    use HasFactory, SoftDeletes;

  
    protected $table = 'tedarikciler';

  
    protected $fillable = [
        'ad',        // Tedarikçi adı
        'numara',    // Telefon numarası
        'vergino',   // Vergi numarası
        'adres',     // Adres bilgisi
        'not',       // Ek not bilgisi
        'team_id',   // Takım ID'si
    ];

  
    protected $dates = [
        'created_at', // Oluşturulma zamanı
        'updated_at', // Güncellenme zamanı
        'deleted_at', // Silinme zamanı (soft delete)
    ];
}
