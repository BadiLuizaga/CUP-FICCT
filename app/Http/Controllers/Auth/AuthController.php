<?php

namespace App\Http\Controllers\Auth;

use App\Models\Usuario;

class AuthController
{
    public function loginForm()
    {
        if (isset($_SESSION['usuario'])) {
            \redirect('/dashboard');
        }

        \view('auth.login', [
            'titulo' => 'Iniciar sesión - SIGIE',
        ]);
    }

    public function login()
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            \set_flash('error', 'Debes ingresar usuario y contraseña.');
            \keep_old($_POST);
            \redirect('/login');
        }

        try {
            $usuario = Usuario::buscarPorUsername($username);

            if (!$usuario) {
                \set_flash('error', 'El usuario no existe.');
                \keep_old($_POST);
                \redirect('/login');
            }

            if (!\is_active($usuario['estado'])) {
                \set_flash('error', 'El usuario está inactivo. No puede ingresar al sistema.');
                \keep_old($_POST);
                \redirect('/login');
            }

            /*
             * CONTRASEÑA NORMAL:
             * Aquí ya no usamos password_verify.
             * Ahora compara directamente lo escrito en el login
             * contra lo guardado en la base de datos.
             */
            if ($password !== $usuario['password']) {
                \set_flash('error', 'La contraseña es incorrecta.');
                \keep_old($_POST);
                \redirect('/login');
            }

            session_regenerate_id(true);

            $roles = Usuario::obtenerRolesUsuario($usuario['id']);

            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'codigo' => $usuario['codigo'],
                'persona_id' => $usuario['persona_id'],
                'username' => $usuario['username'],
                'ci' => $usuario['ci'],
                'nombres' => $usuario['nombres'],
                'apellidos' => $usuario['apellidos'],
                'nombre_completo' => trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? '')),
                'email' => $usuario['email'],
                'roles' => $roles,
            ];

            Usuario::actualizarUltimoAcceso($usuario['id']);

            \set_flash('success', 'Bienvenido al sistema SIGIE.');
            \redirect('/dashboard');
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al iniciar sesión: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/login');
        }
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        session_start();
        \set_flash('success', 'Sesión finalizada correctamente.');
        \redirect('/login');
    }
}