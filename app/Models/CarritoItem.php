<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MenuComidaModel; // ✅ CORRECTO
class CarritoItem extends Model
{
    protected $fillable = [
        'carrito_id',
        'menu_comida_id',
        'cantidad',
        'precio',
        'subtotal'
    ];

    public function carrito()
    {
        return $this->belongsTo(Carrito::class);
    }

    public function menu()
    {
        return $this->belongsTo(MenuComidaModel::class, 'menu_comida_id');
    }
}

