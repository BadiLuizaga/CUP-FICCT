<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;

class UsuarioController
{
    public function index()
    {
        \require_login();

        try {
            $usuarios = Usuario::obtenerTodos();

            \view('usuarios.index', [
                'titulo' => 'Usuarios - SIGIE',
                'usuarios' => $usuarios,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al listar usuarios: ' . $e->getMessage());

            \view('usuarios.index', [
                'titulo' => 'Usuarios - SIGIE',
                'usuarios' => [],
            ]);
        }
    }

    public function create()
    {
        \require_login();

        $roles = Rol::obtenerTodos();

        \view('usuarios.create', [
            'titulo' => 'Nuevo Usuario - SIGIE',
            'roles' => $roles,
        ]);
    }

    public function store()
    {
        \require_login();

        $datos = $this->obtenerDatosFormulario(false);
        $errores = $this->validar($datos, false);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/usuarios/create');
        }

        try {
            Usuario::crear($datos);

            \set_flash('success', 'Usuario creado correctamente.');
            \redirect('/usuarios');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo crear el usuario: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/usuarios/create');
        }
    }

    public function edit()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Usuario no válido.');
            \redirect('/usuarios');
        }

        $usuario = Usuario::buscarPorId($id);

        if (!$usuario) {
            \set_flash('error', 'El usuario no existe.');
            \redirect('/usuarios');
        }

        $roles = Rol::obtenerTodos();

        \view('usuarios.edit', [
            'titulo' => 'Editar Usuario - SIGIE',
            'usuario' => $usuario,
            'roles' => $roles,
        ]);
    }

    public function update()
    {
        \require_login();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Usuario no válido.');
            \redirect('/usuarios');
        }

        $usuarioActual = Usuario::buscarPorId($id);

        if (!$usuarioActual) {
            \set_flash('error', 'El usuario no existe.');
            \redirect('/usuarios');
        }

        $datos = $this->obtenerDatosFormulario(true);

        if (isset($_SESSION['usuario']['id']) && (int)$_SESSION['usuario']['id'] === $id && $datos['estado'] === false) {
            $datos['estado'] = true;
        }

        $errores = $this->validar($datos, true, $id, $usuarioActual['persona_id']);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/usuarios/edit&id=' . $id);
        }

        try {
            Usuario::actualizar($id, $datos);

            if (isset($_SESSION['usuario']['id']) && (int)$_SESSION['usuario']['id'] === $id) {
                $_SESSION['usuario']['username'] = $datos['username'];
                $_SESSION['usuario']['nombres'] = $datos['nombres'];
                $_SESSION['usuario']['apellidos'] = $datos['apellidos'];
                $_SESSION['usuario']['nombre_completo'] = trim($datos['nombres'] . ' ' . $datos['apellidos']);
                $_SESSION['usuario']['email'] = $datos['email'];
            }

            \set_flash('success', 'Usuario actualizado correctamente.');
            \redirect('/usuarios');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo actualizar el usuario: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/usuarios/edit&id=' . $id);
        }
    }

    public function cambiarEstado()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Usuario no válido.');
            \redirect('/usuarios');
        }

        if (isset($_SESSION['usuario']['id']) && (int)$_SESSION['usuario']['id'] === $id) {
            \set_flash('error', 'No puedes cambiar el estado de tu propio usuario mientras estás conectado.');
            \redirect('/usuarios');
        }

        try {
            Usuario::cambiarEstado($id);

            \set_flash('success', 'Estado del usuario actualizado correctamente.');
            \redirect('/usuarios');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo cambiar el estado del usuario: ' . $e->getMessage());
            \redirect('/usuarios');
        }
    }

    private function obtenerDatosFormulario($edicion = false)
    {
        return [
            'ci' => trim($_POST['ci'] ?? ''),
            'nombres' => trim($_POST['nombres'] ?? ''),
            'apellidos' => trim($_POST['apellidos'] ?? ''),
            'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
            'sexo' => trim($_POST['sexo'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'estado' => isset($_POST['estado']),
            'rol_id' => trim($_POST['rol_id'] ?? ''),
        ];
    }

    private function validar($datos, $edicion = false, $usuarioId = null, $personaId = null)
    {
        $errores = [];

        if ($datos['ci'] === '') {
            $errores[] = 'El CI es obligatorio.';
        }

        if ($datos['nombres'] === '') {
            $errores[] = 'Los nombres son obligatorios.';
        }

        if ($datos['apellidos'] === '') {
            $errores[] = 'Los apellidos son obligatorios.';
        }

        if ($datos['username'] === '') {
            $errores[] = 'El nombre de usuario es obligatorio.';
        }

        if (!$edicion && $datos['password'] === '') {
            $errores[] = 'La contraseña es obligatoria.';
        }

        if ($datos['password'] !== '' && strlen($datos['password']) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        if ($datos['rol_id'] === '') {
            $errores[] = 'Debes asignar un rol al usuario.';
        }

        if ($datos['sexo'] !== '' && !in_array($datos['sexo'], ['M', 'F'], true)) {
            $errores[] = 'El sexo seleccionado no es válido.';
        }

        if ($datos['email'] !== '' && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato válido.';
        }

        if ($datos['username'] !== '' && Usuario::existeUsername($datos['username'], $usuarioId)) {
            $errores[] = 'El nombre de usuario ya existe.';
        }

        if ($datos['ci'] !== '' && Usuario::existeCI($datos['ci'], $personaId)) {
            $errores[] = 'El CI ya está registrado.';
        }

        if ($datos['email'] !== '' && Usuario::existeEmail($datos['email'], $personaId)) {
            $errores[] = 'El email ya está registrado.';
        }

        if ($datos['rol_id'] !== '' && !Rol::buscarPorId($datos['rol_id'])) {
            $errores[] = 'El rol seleccionado no existe.';
        }

        return $errores;
    }
}