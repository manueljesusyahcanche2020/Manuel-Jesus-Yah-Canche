<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoHistorial extends Model
{
    protected $table = 'pedido_historial';

    protected $fillable = [
        'pedido_id',
        'estado',
        'comentario'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}