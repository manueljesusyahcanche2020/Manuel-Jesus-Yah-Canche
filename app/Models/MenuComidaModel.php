<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MenuComidaModel extends Model
{
    protected $table = 'menu_comidas';

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'categoria_id',
        'precio',
        'stock',
        'sin_stock',
        'imagen',
        'estado'
    ];

    // 🔗 Producto → Vendedor (User)
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function items()
    {
        return $this->hasMany(CarritoItem::class, 'menu_comida_id');
    }
     public function pedidoItems()
    {
        return $this->hasMany(PedidoItem::class, 'producto_id');
    }
        public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    
}
