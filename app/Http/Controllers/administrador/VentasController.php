<?php

namespace App\Http\Controllers\Administrador;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\CarritoItem;
use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class  ventasController extends Controller
{
    public function ventas()
    {
        return view('dashboard.vendedor.Ventas');
    }
    public function pedidos()
    {
        return view('dashboard.vendedor.Pedidos');
    }
}
