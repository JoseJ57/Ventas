<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipo_articulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipo_articulo';

    protected $fillable = [
        'nombre',
    ];


    //FK uno a muchos
    public function talla_tipo_articulo()
    {
        return $this->hasMany(Talla_tipo_articulo::class,'talla_tipo_articulo_id');
    }
    public function tipo_articulo_material()
    {
        return $this->hasMany(Tipo_articulo_material::class,'tipo_articulo_material_id');
    }
}
