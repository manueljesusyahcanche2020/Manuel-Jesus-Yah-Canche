<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Carrito extends Model
{
    protected $fillable = ['user_id','vendedor_id', 'estado', 'total'];

    public function items()
    {
        return $this->hasMany(CarritoItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
        public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
}

