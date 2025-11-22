<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::orderBy('nombre')->paginate(15);
        return view('admin.usuarios', compact('usuarios'));
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);
        return view('admin.usuario_detalle', compact('usuario'));
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if($usuario){
            $usuario->delete();
        }
        return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado correctamente');
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'rol' => 'required|string',
        ]);

        $usuario = Usuario::find($id);

        if ($usuario) {
            $usuario->rol = $request->rol;
            $usuario->save();
        }

        return redirect()->route('admin.usuarios')->with('success', 'Rol de usuario actualizado correctamente');
    }
}
