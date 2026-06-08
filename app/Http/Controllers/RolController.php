<?php

namespace App\Http\Controllers;

use App\Models\Rol;

class RolController
{
    public function index()
    {
        \require_login();

        try {
            $roles = Rol::obtenerTodos();

            \view('roles.index', [
                'titulo' => 'Roles - SIGIE',
                'roles' => $roles,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al listar roles: ' . $e->getMessage());

            \view('roles.index', [
                'titulo' => 'Roles - SIGIE',
                'roles' => [],
            ]);
        }
    }

    public function create()
    {
        \require_login();

        \view('roles.create', [
            'titulo' => 'Nuevo Rol - SIGIE',
        ]);
    }

    public function store()
    {
        \require_login();

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validar($datos);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/roles/create');
        }

        try {
            Rol::crear($datos);

            \set_flash('success', 'Rol creado correctamente.');
            \redirect('/roles');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo crear el rol: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/roles/create');
        }
    }

    public function edit()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Rol no válido.');
            \redirect('/roles');
        }

        $rol = Rol::buscarPorId($id);

        if (!$rol) {
            \set_flash('error', 'El rol no existe.');
            \redirect('/roles');
        }

        \view('roles.edit', [
            'titulo' => 'Editar Rol - SIGIE',
            'rol' => $rol,
        ]);
    }

    public function update()
    {
        \require_login();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Rol no válido.');
            \redirect('/roles');
        }

        if (!Rol::buscarPorId($id)) {
            \set_flash('error', 'El rol no existe.');
            \redirect('/roles');
        }

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validar($datos, $id);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/roles/edit&id=' . $id);
        }

        try {
            Rol::actualizar($id, $datos);

            \set_flash('success', 'Rol actualizado correctamente.');
            \redirect('/roles');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo actualizar el rol: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/roles/edit&id=' . $id);
        }
    }

    private function obtenerDatosFormulario()
    {
        return [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
        ];
    }

    private function validar($datos, $id = null)
    {
        $errores = [];

        if ($datos['nombre'] === '') {
            $errores[] = 'El nombre del rol es obligatorio.';
        }

        if ($datos['nombre'] !== '' && Rol::existeNombre($datos['nombre'], $id)) {
            $errores[] = 'Ya existe un rol con ese nombre.';
        }

        return $errores;
    }
}