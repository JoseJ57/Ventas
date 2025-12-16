<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talla_Articulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'talla_articulo';

    protected $fillable = [
        'talla_id',
        'articulo_id',
    ];

    //fk muchos a uno
    public function talla()
    {
        return $this->belongsTo(Talla::class,'talla_id');
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class,'articulo_id');
    }
}
