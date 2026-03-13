<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\MenuComidaModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\SolicitudVendedor;
use Illuminate\Http\Request;

class VendedorController extends Controller
{
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        // Traer solo los productos del vendedor logueado
        $productos = MenuComidaModel::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view(
            'dashboard.vendedor.productos.crear',
            compact('categorias', 'productos')
        );
    }

    public function vendedores()
    {
        $vendedores = \App\Models\User::where('role_id', 2)
            ->select('id','name','imagen','minimo_pedido')
            ->get();

        return view('dashboard.cliente.vendedores.index', compact('vendedores'));
    }

    public function catalogoVendedor($id)
    {
        // Trae el catálogo del vendedor
        $menu = collect(DB::select("CALL obtener_catalogo_vendedor(?)", [$id]));

        // Trae los datos del vendedor
        $vendedor = DB::table('users')->where('id', $id)->first();

        return view('dashboard.cliente.vendedores.catalogo_vendedor', compact('menu', 'vendedor'));
    }
    public function indexcambiar()
    {

        $solicitudPendiente = DB::table('solicitudes_ingre_vendedor')
            ->where('id_user', Auth::id())
            ->where('estado','pendiente')
            ->orderBy('created_at','desc')
            ->first();

        return view(
            'dashboard.vendedor.solicitud',
            compact('solicitudPendiente')
        );
    }

    public function cambiar(Request $request)
    {
        $request->validate([
            'motivo' => 'required|min:10|max:500'
        ]);

        $user = Auth::user();

        $resultado = DB::select(
            'CALL sp_solicitar_vendedor(?, ?, ?)',
            [
                $user->id,
                $user->name,
                $request->motivo
            ]
        );

        if ($resultado[0]->resultado) {
            return redirect()->route('dashboard')
                ->with('success', $resultado[0]->mensaje);
        } else {
            return redirect()->back()
                ->with('error', $resultado[0]->mensaje);
        }
    }
}