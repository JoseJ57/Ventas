<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material_Articulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'material_articulo';

    protected $fillable = [
        'material_id',
        'articulo_id',
    ];

    //fk muchos a uno
    public function material()
    {
        return $this->belongsTo(Material::class,'material_id');
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class,'articulo_id');
    }

}
