<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuComidaModel; // Asegúrate que este es el nombre correcto de tu modelo
use Illuminate\Support\Facades\Storage;

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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $imagenPath = $request->file('imagen')->store('menu_comidas', 'public');

        $nombre = trim(
            preg_replace(
                '/[^A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s\.,\-]/u',
                '',
                strip_tags($request->nombre)
            )
        );

        $descripcion = trim(
            preg_replace(
                '/[^A-Za-z0-9áéíóúÁÉÍÓÚñÑ\s\.,\-]/u',
                '',
                strip_tags($request->descripcion)
            )
        );

        $menu = MenuComidaModel::create([
            'user_id' => Auth::id(),
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'categoria_id' => (int) $request->categoria_id,
            'precio' => $request->precio,
            'imagen' => $imagenPath,
            'estado' => 1
        ]);

        return response()->json([
            'success' => true,
            'menu' => $menu
        ]);
    }

    public function update(Request $request, $id)
    {
        $menu = MenuComidaModel::findOrFail($id);

        $request->validate([
            'nombre' => [
                'required',
                'regex:/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]+$/'
            ],
            'categoria_id' => [
                'required',
                'regex:/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ]+$/'
            ],
            'precio' => 'required|numeric',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        // 🔥 Si viene una nueva imagen
        if ($request->hasFile('imagen')) {

            // 🗑️ Eliminar imagen anterior si existe
            if ($menu->imagen && Storage::disk('public')->exists($menu->imagen)) {
                Storage::disk('public')->delete($menu->imagen);
            }

            // 📂 Guardar nueva imagen
            $menu->imagen = $request->file('imagen')
                                    ->store('menu_comidas', 'public');
        }

        // ✏️ Actualizar datos
        $menu->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'precio' => $request->precio
        ]);

        return response()->json(['success' => true]);
    }
    public function destroy($id)
    {
        $producto = MenuComidaModel::findOrFail($id);
        $producto->delete();

        return response()->json(['ok' => true]);
    }

}
