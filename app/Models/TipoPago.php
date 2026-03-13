<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    use HasFactory;

    protected $table = 'tipo_pago';

    protected $fillable = ['nombre'];

    // Relación inversa: un tipo de pago tiene muchos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'tipo_pago_id');
    }
}