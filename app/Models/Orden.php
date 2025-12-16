<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orden extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orden';
    protected $primaryKey = 'ID_Recibo_NUMBER';

    protected $fillable = [
        'Fecha',
        'Forma_de_pago',
        'Total',
        'Estado_Dio',
        'Envio_Bool',
        'ID_Cliente',
        'ID_Empleado',
    ];

    protected $casts = [
        'Fecha' => 'datetime',
        'Total' => 'decimal:2',
        'Envio_Bool' => 'boolean',
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'ID_Cliente', 'ID_Cliente_NUMBER');
    }

    // Relación con Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'ID_Empleado', 'ID_Empleado_NUMBER');
    }

    // Relación con Detalles
    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'ID_Recibo_NUMBER', 'ID_Recibo_NUMBER');
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where('ID_Recibo_NUMBER', 'like', "%{$search}%")
                    ->orWhere('Estado_Dio', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function($q) use ($search) {
                        $q->where('Nombre_Cliente', 'like', "%{$search}%")
                          ->orWhere('Apellido', 'like', "%{$search}%");
                    });
    }

    public function scopeByEstado($query, $estado)
    {
        if ($estado) {
            return $query->where('Estado_Dio', $estado);
        }
        return $query;
    }

    public function scopeByFecha($query, $fechaInicio, $fechaFin)
    {
        if ($fechaInicio && $fechaFin) {
            return $query->whereBetween('Fecha', [$fechaInicio, $fechaFin]);
        }
        return $query;
    }
}
