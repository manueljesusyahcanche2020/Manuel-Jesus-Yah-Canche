<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'imagen',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function menu_comidas()
    {
        return $this->hasMany(MenuComidaModel::class, 'user_id'); 
        // 'user_id' es la columna en menu_comidas que indica a qué usuario pertenece
    }
// 🔹 Pedidos que hizo como cliente
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    // 🔹 Ventas como vendedor
    public function ventas()
    {
        return $this->hasMany(PedidoItem::class, 'vendor_id');
    }
    public function pagoVendedor()
    {
        return $this->hasOne(VendedorPago::class,'user_id');
    }
    public function direcciones()
    {
        return $this->hasMany(\App\Models\Direccion::class);
    }
}
