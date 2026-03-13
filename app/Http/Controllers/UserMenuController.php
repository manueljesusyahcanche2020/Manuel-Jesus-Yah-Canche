<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuComidaModel; // Asegúrate que este es el nombre correcto de tu modelo
use Illuminate\Support\Facades\Storage;
use BD;
use Illuminate\Support\Facades\DB;

class UserMenuController extends Controller
{
    // Mostrar productos del usuario logueado
    public function ajaxMenuUsuario()
    {
        $menus = auth()->user()->menu_comidas()
                    ->select('id', 'nombre', 'descripcion', 'categoria_id', 'precio', 'imagen')
                    ->get();

        return response()->json($menus);
    }
    public function store(Request $request)
    {

        $request->validate([
            'nombre' => 'required|string|max:255', // Nombre obligatorio
            'descripcion' => 'nullable|string', // Descripción opcional
            'categoria_id' => 'required|exists:categorias,id', // Debe existir en tabla categorias
            'precio' => 'required|numeric|min:0', // Precio numérico y mayor o igual a 0
            'stock' => 'nullable|integer|min:0', // Stock opcional pero si viene debe ser entero >= 0
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048' // Imagen válida
        ]);

        $imagenPath = $request->file('imagen')
                            ->store('menu_comidas', 'public');

        $nombre = trim(strip_tags($request->nombre));

        $descripcion = $request->descripcion
            ? trim(strip_tags($request->descripcion))
            : null;

        $sinStock = $request->has('sin_stock') ? 1 : 0;
        MenuComidaModel::create([
            'user_id' => Auth::id(),
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'categoria_id' => (int) $request->categoria_id,
            'precio' => $request->precio,

            // Si sin_stock está activo → stock será null
            'stock' => $sinStock ? null : $request->stock,

            'sin_stock' => $sinStock,
            'imagen' => $imagenPath,
            'estado' => 1 // Producto activo
        ]);

        return redirect()
            ->route('vendedor.productos.crear')
            ->with('success', 'Producto creado correctamente');
    }
    public function update(Request $request, $id)
    {
        $menu = MenuComidaModel::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $imagenRuta = $menu->imagen;

        if ($request->hasFile('imagen')) {

            if ($menu->imagen && Storage::disk('public')->exists($menu->imagen)) {
                Storage::disk('public')->delete($menu->imagen);
            }

            $imagenRuta = $request->file('imagen')->store('menu_comidas', 'public');
        }

        $resultado = DB::select(
            'CALL actualizar_producto_seguro(?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $id,
                auth()->id(),
                $request->nombre,
                $request->categoria_id,
                $request->descripcion,
                $request->precio,
                $request->stock ?? 0,
                $imagenRuta,
                1
            ]
        );

        $mensaje = $resultado[0]->mensaje ?? 'Producto actualizado';

        return redirect()
            ->route('vendedor.productos.crear')
            ->with('success', $mensaje);
    }
    public function destroy($id)
    {
        $producto = MenuComidaModel::findOrFail($id);
        $producto->delete();

        return redirect()
        ->route('vendedor.productos.crear')
        ->with('success', 'Producto eliminado correctamente');
    }

}
