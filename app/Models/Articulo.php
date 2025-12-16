<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articulo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'eslogan',
        'estado',
        'recomendacion',
        'image_url',
        'precio',
        'disponible',
        'tipo_articulo_id',
        'marca_id',
    ];

    //fk muchos a uno
    public function tipo_articulo()
    {
        return $this->belongsTo(Tipo_articulo::class,'tipo_articulo_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class,'marca_id');
    }
    //FK uno a muchos
    public function talla_articulo()
    {
        return $this->hasMany(Talla_Articulo::class,'talla_articulo_id');
    }
    public function material_articulo()
    {
        return $this->hasMany(Material_Articulo::class,'material_articulo_id');
    }

    //scopes and functions

}
