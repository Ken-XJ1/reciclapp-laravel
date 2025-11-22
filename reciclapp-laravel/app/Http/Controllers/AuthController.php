<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario; // ✅ tu modelo personalizado
use App\Models\Auditoria;

class AuthController extends Controller
{
    /**
     * Mostrar vista de login
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // ✅ Intentar login con email y contraseña
        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ])) {
            $request->session()->regenerate();

            $rol = trim(strtolower(Auth::user()->rol ?? ''));

            // Registrar en auditoría el login exitoso
            Auditoria::create([
                'accion'          => 'login',
                'usuario'         => $credentials['email'],
                'fecha'           => now(),
                'descripcion'     => 'Inicio de sesión correcto',
                'modulo_afectado' => 'auth',
                'resultado'       => 'exitoso',
                'ip'              => $request->ip(),
            ]);

            // Redirigir según rol (cualquier valor que contenga "admin")
            if (str_contains($rol, 'admin')) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('panel');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        return view('register');
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        // 🔹 Validación de datos
        $request->validate([
            'id_usuario'       => 'required|string|max:20|unique:usuarios,id_usuario',
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:usuarios',
            'password'         => 'required|string|min:6|confirmed',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        // 🔹 Crear usuario
        $usuario = Usuario::create([
            'id_usuario'       => $request->id_usuario,
            'nombre'           => $request->nombre,
            'apellido'         => $request->apellido,
            'email'            => $request->email,
            'contrasena_hash'  => Hash::make($request->password),
            'rol'              => 'usuario',
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'estado'           => 'activo',
            'puntos_acumulados' => 0,
        ]);

        // Registrar en auditoría el registro de un nuevo usuario
        Auditoria::create([
            'accion'          => 'registro_usuario',
            'usuario'         => $usuario->email,
            'fecha'           => now(),
            'descripcion'     => 'Nuevo usuario registrado en el sistema',
            'modulo_afectado' => 'auth',
            'resultado'       => 'exitoso',
            'ip'              => $request->ip(),
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Registro exitoso 🚀 Ahora puedes iniciar sesión con tus credenciales.');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); // volver al login
    }
}
