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
    public function secciones_personal()
    {
        return $this->belongsTo(Secciones_personal::class,'seccion_id');
    }

    public function subsecc()
    {
        return $this->belongsTo(Sub_secciones::class,'subsecc_id');
    }
    //FK uno a muchos
    public function funcionarios()
    {
        return $this->hasMany(Funcionarios::class,'cargo_id');
    }

    //scopes and functions

}
