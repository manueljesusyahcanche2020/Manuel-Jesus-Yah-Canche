<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'user_id',
        'total',
        'direccion',
        'tipo_pago',
        'estado_pago',
        'estado'
    ];

    // 🔹 Cliente que hizo el pedido
    public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function historial()
{
    return $this->hasMany(PedidoHistorial::class, 'pedido_id');
}
public function direccion()
{
    return $this->belongsTo(Direccion::class, 'direccion_id');
}
// Pedido.php
public function tipo_pago()
{
    return $this->belongsTo(TipoPago::class, 'tipo_pago_id');
}
}