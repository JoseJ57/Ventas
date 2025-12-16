<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ← AÑADE ESTA LÍNEA

class Talla extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tallas';

    protected $fillable = [
        'nombre',
    ];

    //FK uno a muchos
    public function talla_articulo()
    {
        return $this->hasMany(Talla_Articulo::class, 'talla_id');
    }

    public function talla_tipo_articulo()
    {
        return $this->hasMany(Talla_tipo_articulo::class, 'talla_id');
    }
}
