<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proje extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Tablonun adı
     *
     * @var string
     */
    protected $table = 'projeler';

    /**
     * Mass-assignable (doldurulabilir) alanlar
     *
     * @var array
     */
    protected $fillable = [
        'ad',
        'aciklama',
        'team_id',
    ];

    /**
     * Takıma (team) ilişki.
     * Eğer ileride bir takım ilişkisi kurarsanız, bu metot kullanılabilir.
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    /**
     * Soft delete için tarih alanları.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getDurumAttribute($value)
{
    return (bool) $value; // Durumu boolean olarak döndür
}

}
