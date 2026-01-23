<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        // Cargar usuarios con su rol (seguridad + rendimiento)
        $usuarios = User::with('role')->get();

        return view('dashboard.administrador.usuarios', compact('usuarios'));
    }
}
