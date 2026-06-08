<?php

namespace App\Http\Controllers;

use App\Models\Docente;

class DocenteController
{
    public function index()
    {
        \require_login();

        try {
            $docentes = Docente::obtenerTodos();

            \view('docentes.index', [
                'titulo' => 'Docentes - SIGIE',
                'docentes' => $docentes,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al listar docentes: ' . $e->getMessage());

            \view('docentes.index', [
                'titulo' => 'Docentes - SIGIE',
                'docentes' => [],
            ]);
        }
    }

    public function show()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Docente no válido.');
            \redirect('/docentes');
        }

        try {
            $docente = Docente::buscarPorId($id);

            if (!$docente) {
                \set_flash('error', 'El docente no existe.');
                \redirect('/docentes');
            }

            $asignaciones = Docente::obtenerAsignaciones($id);

            \view('docentes.show', [
                'titulo' => 'Detalle de Docente - SIGIE',
                'docente' => $docente,
                'asignaciones' => $asignaciones,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar detalle del docente: ' . $e->getMessage());
            \redirect('/docentes');
        }
    }

    public function create()
    {
        \require_login();

        \view('docentes.create', [
            'titulo' => 'Nuevo Docente - SIGIE',
            'estadosContrato' => Docente::estadosContratoValidos(),
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
            \redirect('/docentes/create');
        }

        try {
            Docente::crear($datos);

            \set_flash('success', 'Docente registrado correctamente.');
            \redirect('/docentes');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo registrar el docente: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/docentes/create');
        }
    }

    public function edit()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Docente no válido.');
            \redirect('/docentes');
        }

        try {
            $docente = Docente::buscarPorId($id);

            if (!$docente) {
                \set_flash('error', 'El docente no existe.');
                \redirect('/docentes');
            }

            \view('docentes.edit', [
                'titulo' => 'Editar Docente - SIGIE',
                'docente' => $docente,
                'estadosContrato' => Docente::estadosContratoValidos(),
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar docente: ' . $e->getMessage());
            \redirect('/docentes');
        }
    }

    public function update()
    {
        \require_login();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Docente no válido.');
            \redirect('/docentes');
        }

        $docenteActual = Docente::buscarPorId($id);

        if (!$docenteActual) {
            \set_flash('error', 'El docente no existe.');
            \redirect('/docentes');
        }

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validar($datos, $id, $docenteActual['persona_id']);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/docentes/edit&id=' . $id);
        }

        try {
            Docente::actualizar($id, $datos);

            \set_flash('success', 'Docente actualizado correctamente.');
            \redirect('/docentes');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo actualizar el docente: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/docentes/edit&id=' . $id);
        }
    }

    private function obtenerDatosFormulario()
    {
        return [
            'codigo' => trim($_POST['codigo'] ?? ''),
            'ci' => trim($_POST['ci'] ?? ''),
            'nombres' => trim($_POST['nombres'] ?? ''),
            'apellidos' => trim($_POST['apellidos'] ?? ''),
            'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
            'sexo' => trim($_POST['sexo'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'profesion' => trim($_POST['profesion'] ?? ''),
            'maestria' => trim($_POST['maestria'] ?? ''),
            'diplomado_educacion' => isset($_POST['diplomado_educacion']),
            'experiencia' => trim($_POST['experiencia'] ?? ''),
            'estado_contrato' => trim($_POST['estado_contrato'] ?? 'Activo'),
        ];
    }

    private function validar($datos, $docenteId = null, $personaId = null)
    {
        $errores = [];

        if ($datos['codigo'] === '') {
            $errores[] = 'El código del docente es obligatorio.';
        }

        if ($datos['ci'] === '') {
            $errores[] = 'El CI es obligatorio.';
        }

        if ($datos['nombres'] === '') {
            $errores[] = 'Los nombres son obligatorios.';
        }

        if ($datos['apellidos'] === '') {
            $errores[] = 'Los apellidos son obligatorios.';
        }

        if ($datos['profesion'] === '') {
            $errores[] = 'La profesión es obligatoria.';
        }

        if ($datos['estado_contrato'] === '') {
            $errores[] = 'El estado de contrato es obligatorio.';
        }

        if ($datos['codigo'] !== '' && strlen($datos['codigo']) > 20) {
            $errores[] = 'El código no debe superar los 20 caracteres.';
        }

        if ($datos['ci'] !== '' && strlen($datos['ci']) > 20) {
            $errores[] = 'El CI no debe superar los 20 caracteres.';
        }

        if ($datos['nombres'] !== '' && strlen($datos['nombres']) > 100) {
            $errores[] = 'Los nombres no deben superar los 100 caracteres.';
        }

        if ($datos['apellidos'] !== '' && strlen($datos['apellidos']) > 100) {
            $errores[] = 'Los apellidos no deben superar los 100 caracteres.';
        }

        if ($datos['sexo'] !== '' && !in_array($datos['sexo'], ['M', 'F'], true)) {
            $errores[] = 'El sexo seleccionado no es válido.';
        }

        if ($datos['email'] !== '' && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato válido.';
        }

        if (!in_array($datos['estado_contrato'], Docente::estadosContratoValidos(), true)) {
            $errores[] = 'El estado de contrato seleccionado no es válido.';
        }

        if ($datos['codigo'] !== '' && Docente::existeCodigo($datos['codigo'], $docenteId)) {
            $errores[] = 'Ya existe un docente con ese código.';
        }

        if ($datos['ci'] !== '' && Docente::existeCI($datos['ci'], $personaId)) {
            $errores[] = 'El CI ya está registrado.';
        }

        if ($datos['email'] !== '' && Docente::existeEmail($datos['email'], $personaId)) {
            $errores[] = 'El email ya está registrado.';
        }

        return $errores;
    }
}