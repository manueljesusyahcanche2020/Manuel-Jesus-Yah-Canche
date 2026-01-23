<?php
namespace App\Http\Controllers\cliente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use Illuminate\Http\Request;
use App\Models\CarritoItem;


use App\Models\MenuComidaModel;

class CarritoController extends Controller
{


    public function listar()
    {
        $rows = DB::select(
            "SELECT
                ci.id AS item_id,
                ci.cantidad,
                ci.precio,
                ci.subtotal,
                m.id AS producto_id,
                m.nombre AS producto_nombre,
                u.name AS vendedor_nombre,
                IFNULL(u.imagen, 'default.png') AS vendedor_icono
            FROM carritos c
            JOIN carrito_items ci ON ci.carrito_id = c.id
            JOIN menu_comidas m ON m.id = ci.menu_comida_id
            JOIN users u ON u.id = m.user_id
            WHERE c.user_id = ?
            AND c.estado = 0",
            [Auth::id()]
        );

        if (empty($rows)) {
            return response()->json([
                'items' => [],
                'total' => 0
            ]);
        }

        $items = [];
        $total = 0;

        foreach ($rows as $row) {
            $items[] = [
                'id' => $row->item_id,
                'cantidad' => $row->cantidad,
                'precio' => $row->precio,
                'subtotal' => $row->subtotal,
                'producto' => [
                    'id' => $row->producto_id,
                    'nombre' => $row->producto_nombre
                ],
                'vendedor' => [
                    'nombre' => $row->vendedor_nombre,
                    'icono' => $row->vendedor_icono
                ]
            ];
            $total += $row->subtotal;
        }

        return response()->json([
            'items' => $items,
            'total' => $total
        ]);
    }

    // Ver carrito
    public function index()
    {
        $carrito = Carrito::with('items.menu')
            ->where('user_id', Auth::id())
            ->where('estado', 0)
            ->first();

        return view('dashboard.cliente.carrito', compact('carrito'));
    }

    // Agregar producto al carrito
    public function agregar($menuId)
    {
        $menu = MenuComidaModel::findOrFail($menuId);

        $carrito = Carrito::firstOrCreate(
            ['user_id' => Auth::id(), 'estado' => 0],
            ['total' => 0]
        );

        $item = CarritoItem::where('carrito_id', $carrito->id)
            ->where('menu_comida_id', $menu->id)
            ->first();

        if ($item) {
            $item->cantidad += 1;
        } else {
            $item = new CarritoItem([
                'menu_comida_id' => $menu->id,
                'cantidad' => 1,
                'precio' => $menu->precio
            ]);
        }

        $item->subtotal = $item->cantidad * $item->precio;
        $carrito->items()->save($item);

        $carrito->total = $carrito->items->sum('subtotal');
        $carrito->save();

        return back()->with('success', 'Producto agregado al carrito');
    }

    /*/ Eliminar item
    public function eliminar($id)
    {
        $item = CarritoItem::findOrFail($id);
        $carrito = $item->carrito;

        $item->delete();

        $carrito->total = $carrito->items()->sum('subtotal');
        $carrito->save();

        return back();
    }*/
    
    public function actualizar(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cantidad' => 'required|integer|min:1'
        ]);

        // 🔁 Ejecutar el PROCEDURE (NO devuelve datos)
        DB::statement(
            'CALL actualizar_item_carrito(?, ?, ?)',
            [
                $request->id,
                Auth::id(),
                $request->cantidad
            ]
        );

        // ✅ Reutilizar el método listar()
        return $this->listar();
    }
    public function eliminar($id)
    {
        $item = CarritoItem::with('carrito')
            ->where('id', $id)
            ->firstOrFail();

        // 🔐 Seguridad
        if ($item->carrito->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $item->delete();

        // 🔁 devolver carrito actualizado
        return $this->listar();
    }

}
