<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cliente';

    protected $fillable = [
        'nombre',
        'apellido',
        'celular',
        'frecuente',
    ];


    //FK uno a muchos
    public function funcionarios()
    {
        return $this->hasMany(Funcionarios::class,'orden_id');
    }

    //scopes and functions
}
