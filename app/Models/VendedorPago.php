<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class VendedorPago extends Model
{
    protected $table = 'vendedor_pagos';

    protected $fillable = [
        'user_id',
        'paypal_email',
        'paypal_client_id',
        'paypal_secret'
    ];
        public $timestamps = false; // ← IMPORTAN

    /* =============================
       RELACIÓN CON USUARIO
    ============================= */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    use App\Models\VendedorPago;

    public function obtenerPaypalVendedor($user_id)
    {
        $pago = VendedorPago::where('user_id',$user_id)->first();

        if(!$pago){
            return null;
        }

        return $pago->paypal_email;
    }
}