<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TallaTipoArticulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'talla_tipo_articulo';

    protected $fillable = [
        'talla_id',
        'tipo_articulo_id',
    ];

    //fk muchos a uno
    public function talla()
    {
        return $this->belongsTo(Talla::class,'talla_id');
    }

    public function tipo_articulo()
    {
        return $this->belongsTo(Tipo_articulo::class,'tipo_articulo_id');
    }
}
