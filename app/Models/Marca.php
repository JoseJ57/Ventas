<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Marca extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marca';

    protected $fillable = [
        'nombre',
        'marca',
        'pais',
    ];


    //FK uno a muchos
    public function articulos()
    {
        return $this->hasMany(Articulo::class,'articulo_id');
    }
}
