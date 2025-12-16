<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleOrden extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'detalle_orden';
    protected $primaryKey = 'ID_Detalle_Recibo_NUMBER';

    protected $fillable = [
        'Cantidad',
        'Subtotal',
        'Observacion',
        'Estado',
        'Tipo',
        'Descuento',
        'ID_Articulo',
        'ID_Recibo_NUMBER',
    ];

    protected $casts = [
        'Cantidad' => 'integer',
        'Subtotal' => 'decimal:2',
        'Descuento' => 'decimal:2',
    ];

    // Relación con Orden
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'ID_Recibo_NUMBER', 'ID_Recibo_NUMBER');
    }

    // Relación con Artículo
    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'ID_Articulo', 'ID_Articulo_NUMBER');
    }

    // Calcular subtotal automáticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detalle) {
            if ($detalle->articulo) {
                $precioUnitario = $detalle->articulo->Precio;
                $subtotalBase = $precioUnitario * $detalle->Cantidad;
                $detalle->Subtotal = $subtotalBase - $detalle->Descuento;
            }
        });
    }
}
