<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'material';

    protected $fillable = [
        'nombre',
    ];


    //FK uno a muchos
    public function material_articulo()
    {
        return $this->hasMany(Material_Articulo::class,'material_articulo_id');
    }
    public function tipo_articulo_material()
    {
        return $this->hasMany(Tipo_articulo_material::class,'tipo_material_articulo_id');
    }
}
