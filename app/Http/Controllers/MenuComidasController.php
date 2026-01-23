<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuComidaModel;
use Illuminate\Support\Facades\Auth;

class MenuComidasController extends Controller
{
    // 🔹 Vista del menú para clientes
    public function index()
    {
        return view('dashboard.cliente.menu');
    }

    // 🔹 Listar comidas (clientes)
    public function listar()
    {
        $menu = MenuComidaModel::where('estado', 1)
            ->whereHas('vendedor', function ($q) {
                $q->where('role_id', 2);
            })
            ->with('vendedor:id,name,imagen')
            ->orderBy('categoria_id')
            ->get();

        return response()->json($menu);
    }


    // 🔹 Buscar comidas
    public function buscar(Request $request)
    {
        $query = $request->input('buscar', '');

        $menu = MenuComidaModel::where('estado', 1)
            ->where('nombre', 'like', "%{$query}%")
            ->with('vendedor:id,name')
            ->orderBy('categoria')
            ->get();

        return response()->json($menu);
    }

    public function store(Request $request)
    {
        // 🔐 VALIDAR QUE SEA VENDEDOR
        if (Auth::user()->rol_id != 2) { // 2 = vendedor
            abort(403, 'Solo los vendedores pueden agregar productos');
        }

        $request->validate([
            'nombre' => 'required|string|max:150',
            'categoria_id' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'required|image'
        ]);

        $path = $request->file('imagen')->store('comidas', 'public');

        MenuComidaModel::create([
            'user_id' => Auth::id(), // vendedor dueño del producto
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'categoria_id' => $request->categoria,
            'precio' => $request->precio,
            'imagen' => $path,
            'estado' => 1
        ]);

        return redirect()->back()->with('success', 'Producto agregado');
    }

    // 🔹 Productos del vendedor logueado
    public function misProductos()
    {
        $menu = MenuComidaModel::where('user_id', Auth::id())->get();
        return view('dashboard.vendedor.productos', compact('menu'));
    }
}
