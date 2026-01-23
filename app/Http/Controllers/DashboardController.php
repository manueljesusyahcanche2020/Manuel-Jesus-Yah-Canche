<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\menu;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function mostrarVista($menuId)
    {
        // Buscar el menú en la base de datos
        $menu = Menu::findOrFail($menuId);

        // Validar rol del usuario
        if ($menu->rol != 0 && $menu->rol != Auth::user()->rol) {
            abort(403, 'No autorizado');
        }

        // Retornar la vista que corresponde al menú
        return view($menu->ruta_blade);
    }
}
