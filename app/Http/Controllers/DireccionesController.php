<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Direccion;
use Illuminate\Support\Collection;
class DireccionesController extends Controller
{

    // ==============================
    // MOSTRAR DIRECCIONES DEL USUARIO
    // ==============================
    public function index()
    {
        // Usamos el ID del usuario autenticado
        $userId = Auth::id();

        // Ejecutamos el procedimiento
        // Usamos DB::select para obtener los datos directamente
        $direcciones = DB::select("CALL ObtenerDireccionesUsuario(?)", [$userId]);

        // Importante: Si vas a retornar JSON, asegúrate de que no esté vacío
        return response()->json($direcciones);
    }

    // ==============================
    // GUARDAR NUEVA DIRECCIÓN
    // ==============================
    public function store(Request $request)
    {
        // 1. Validación ajustada a los campos de tu imagen de DB
        $request->validate([
            'calle' => 'required|string|max:255',
            'colonia' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:255',
            'nombre_direccion' => 'nullable|string|max:255', // Campo 'casa', 'trabajo', etc.
        ]);

        // 2. Creación del registro
        $direccion = Direccion::create([
            'user_id'          => Auth::id(),
            'nombre_direccion' => $request->nombre_direccion ?? 'casa', // Valor por defecto
            'calle'            => $request->calle,
            'colonia'          => $request->colonia,
            'ciudad'           => $request->ciudad,
            'estado'           => $request->estado,
            'referencia'       => $request->referencia,
        ]);

        // 3. Respuesta condicional (AJAX o normal)
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Dirección guardada con éxito',
                'direccion' => $direccion
            ]);
        }

        return back()->with('success', 'Dirección agregada correctamente');
    }


    // ==============================
    // ACTUALIZAR DIRECCIÓN

    // ==============================
    // ELIMINAR DIRECCIÓN
    // ==============================
    public function destroy($id)
    {
        $direccion = Direccion::where('user_id', Auth::id())
                              ->where('id', $id)
                              ->firstOrFail();

        $direccion->delete();

        return back()->with('success','Dirección eliminada');
    }
    public function update(Request $request, $id)
    {
        // Ejecutamos y capturamos el resultado del SELECT interno del procedimiento
        $resultado = DB::select('CALL ActualizarDireccionConMensaje(?, ?, ?, ?, ?, ?)', [
            $id,
            Auth::id(),
            $request->nombre_direccion,
            $request->calle,
            $request->colonia,
            $request->referencia
        ]);

        // Como DB::select devuelve un array, accedemos al primer elemento [0]
        $status = $resultado[0]->status;
        $mensaje = $resultado[0]->mensaje;

        if ($status === 'SUCCESS') {
            return response()->json([
                'success' => true, 
                'message' => $mensaje
            ]);
        } else {
            return response()->json([
                'success' => false, 
                'message' => $mensaje
            ], 404);
        }
    }
}