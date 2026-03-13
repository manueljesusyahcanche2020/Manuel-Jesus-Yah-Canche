<?php

namespace App\Http\Controllers\Administrador;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class AdminUserController extends Controller
{
    public function index()
    {
        // Cargar usuarios con su rol (seguridad + rendimiento)
        $usuarios = User::with('role')->get();

        return view('dashboard.administrador.usuarios', compact('usuarios'));
    }
    public function solicitudesVendedor()
    {   
        // Traer roles
        $roles = DB::table('roles')
            ->whereBetween('id', [1, 3])
            ->orderBy('id', 'desc')
            ->get();

        // Traer solicitudes desde la VISTA
        $solicitudes = DB::table('vista_solicitudes_vendedor')
            ->orderBy('created_at','desc')
            ->get();

        return view('dashboard.administrador.solicitudes_vendedor', compact('solicitudes','roles'));
    }
    public function aprobarSolicitud($id)
    {

        $solicitud = DB::table('solicitudes_ingre_vendedor')
            ->where('id_solicitud',$id)
            ->first();

        DB::table('solicitudes_ingre_vendedor')
            ->where('id_solicitud',$id)
            ->update([
                'estado'=>'aprobado',
                'updated_at'=>now()
            ]);

        DB::table('users')
            ->where('id',$solicitud->id_user)
            ->update([
                'role_id'=>'2'
            ]);

        return back()->with('success','Solicitud aprobada correctamente');
    }
    public function rechazarSolicitud($id)
    {

        DB::table('solicitudes_ingre_vendedor')
            ->where('id_solicitud',$id)
            ->update([
                'estado'=>'rechazado',
                'updated_at'=>now()
            ]);

        return back()->with('error','Solicitud rechazada');
    }
}
