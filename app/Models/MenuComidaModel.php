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
        'imagen',
        'estado'
    ];

    // 🔗 Producto → Vendedor (User)
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
