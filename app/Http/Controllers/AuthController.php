<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ]);
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function Register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        auth::login($user);
        return redirect()->intended('dashboard');
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'imagen' => 'nullable|url|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->telefono = $request->telefono; //  guardar teléfono

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->filled('imagen')) {
            $user->imagen = $request->imagen;
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente');
    }
    public function updateFoto(Request $request)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = Auth::user();

        if ($request->hasFile('imagen')) {

            // eliminar foto anterior si existe
            if ($user->imagen && Storage::exists('public/'.$user->imagen)) {
                Storage::delete('public/'.$user->imagen);
            }

            // guardar nueva foto
            $ruta = $request->file('imagen')->store('perfil', 'public');

            $user->imagen = $ruta;
            $user->save();
        }

        return back()->with('success','Foto de perfil actualizada');
    }
    public function cambiarRol(Request $request, $id)
    {
        // Solo admin puede cambiar roles
        if (Auth::user()->role_id != 1) {
            abort(403);
        }

        $request->validate([
            'role_id' => 'required|in:1,2,3'
        ]);

        $user = User::findOrFail($id);

        $user->role_id = $request->role_id;
        $user->save();

        return back()->with('success', 'Rol actualizado correctamente');
    }

}
