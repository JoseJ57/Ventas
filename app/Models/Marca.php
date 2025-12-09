<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'eslogan',
        'descripcion',
        'recomendacion',
        'image_url',
        'precio',
        'estado',
        'tipo_articulo_id',
        'marca_id',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con TipoArticulo (muchos a uno)
    public function tipoArticulo()
    {
        return $this->belongsTo(TipoArticulo::class);
    }

    // Relación con Marca (muchos a uno)
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    // Scopes útiles
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeAgotados($query)
    {
        return $query->where('estado', 'agotado');
    }

    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('tipo_articulo_id', $tipoId);
    }

    public function scopePorMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }

    // Accessor para formatear el precio
    public function getPrecioFormateadoAttribute()
    {
        return number_format($this->precio, 2, ',', '.');
    }

    // Verificar si está disponible
    public function estaDisponible()
    {
        return $this->estado === 'disponible';
    }
}
