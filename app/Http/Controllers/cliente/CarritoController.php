<?php
namespace App\Http\Controllers\cliente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use Illuminate\Http\Request;
use App\Models\CarritoItem;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\MenuComidaModel;

class CarritoController extends Controller
{
    public function listar()
    {
        // Obtenemos los carritos activos del usuario
        $carritos = DB::table('carritos as c')
            ->join('users as u', 'u.id', '=', 'c.vendedor_id')
            ->where('c.user_id', Auth::id())
            ->where('c.estado', 0)
            ->select('c.id as carrito_id', 'c.total', 'u.name as vendedor_nombre', DB::raw("IFNULL(u.imagen, 'default.png') as vendedor_icono"))
            ->get();

        if ($carritos->isEmpty()) {
            return response()->json([
                'carritos' => [],
            ]);
        }

        $resultado = [];

        foreach ($carritos as $carrito) {
            // Obtenemos los items de este carrito
            $items = DB::table('carrito_items as ci')
                ->join('menu_comidas as m', 'm.id', '=', 'ci.menu_comida_id')
                ->where('ci.carrito_id', $carrito->carrito_id)
                ->select('ci.id as item_id', 'ci.cantidad', 'ci.precio', 'ci.subtotal', 'm.id as producto_id', 'm.nombre as producto_nombre')
                ->get();

            $itemsArray = $items->map(function ($item) {
                return [
                    'id' => $item->item_id,
                    'cantidad' => $item->cantidad,
                    'precio' => $item->precio,
                    'subtotal' => $item->subtotal,
                    'producto' => [
                        'id' => $item->producto_id,
                        'nombre' => $item->producto_nombre,
                    ],
                ];
            })->toArray();

            $resultado[] = [
                'id' => $carrito->carrito_id,
                'total' => $carrito->total,
                'vendedor' => [
                    'nombre' => $carrito->vendedor_nombre,
                    'icono' => $carrito->vendedor_icono,
                ],
                'items' => $itemsArray,
            ];
        }

        return response()->json(['carritos' => $resultado]);
    }

    public function index()
    {
        $carritos = Carrito::with(['items.menu', 'vendedor']) // trae items y datos del vendedor
            ->where('user_id', Auth::id())
            ->where('estado', 0) // carrito activo
            ->get(); // <- traemos todos los carritos

        return view('dashboard.cliente.carrito', compact('carritos'));
    }

    // Agregar producto al carrito
    public function agregar($menuId)
    {
        $menu = MenuComidaModel::findOrFail($menuId);
        $vendedorId = $menu->user_id;

        // Buscar carrito activo para el usuario y el vendedor
        $carrito = Carrito::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'vendedor_id' => $vendedorId,
                'estado' => 0
            ],
            ['total' => 0]
        );

        // Buscar item existente
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

        // Actualizar total del carrito
        $carrito->total = $carrito->items->sum('subtotal');
        $carrito->save();

        return back()->with('success', 'Producto agregado al carrito');
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cantidad' => 'required|integer|min:1'
        ]);

        DB::statement(
            'SELECT actualizar_item_carrito(?, ?)',
            [
                $request->id,
                $request->cantidad
            ]
        );

        return $this->listar();
    }

    public function eliminar($id)
    {
        $item = CarritoItem::with('carrito')
            ->where('id', $id)
            ->firstOrFail();

        if ($item->carrito->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $item->delete();

        return $this->listar();
    }
    public function confirmarCompra(Request $request)
    {
        // Validamos que la dirección exista
        $request->validate([
            'direccion_id' => 'required|exists:direcciones,id',
            'carrito_id'  => 'required|exists:carritos,id', // validar que se envíe un carrito
        ]);

        // Validamos que el carrito pertenezca al usuario
        $carrito = DB::table('carritos')
                    ->where('id', $request->carrito_id)
                    ->where('user_id', Auth::id())
                    ->first();

        if (!$carrito) {
            return response()->json([
                'success' => false, 
                'message' => 'El carrito no existe o no te pertenece'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Llamamos al procedimiento solo para este carrito
            DB::statement('CALL confirmar_compra(?, ?, ?)', [
                Auth::id(),
                $request->direccion_id,
                $request->carrito_id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Compra confirmada con éxito'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}