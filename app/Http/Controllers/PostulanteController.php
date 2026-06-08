<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use App\Models\Carrera;
use App\Models\PeriodoAcademico;

class PostulanteController
{
    public function index()
    {
        \require_login();

        try {
            $periodos = PeriodoAcademico::obtenerTodos();
            $periodoActivo = PeriodoAcademico::obtenerActivo();
            $periodoId = (int)($_GET['periodo_id'] ?? 0);

            if ($periodoId <= 0 && $periodoActivo) {
                $periodoId = (int)$periodoActivo['id'];
            }

            $postulantes = Postulante::obtenerTodos($periodoId > 0 ? $periodoId : null);

            \view('postulantes.index', [
                'titulo' => 'Postulantes - SIGIE',
                'postulantes' => $postulantes,
                'periodos' => $periodos,
                'periodoId' => $periodoId,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al listar postulantes: ' . $e->getMessage());

            \view('postulantes.index', [
                'titulo' => 'Postulantes - SIGIE',
                'postulantes' => [],
                'periodos' => [],
                'periodoId' => 0,
            ]);
        }
    }

    public function show()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Postulante no válido.');
            \redirect('/postulantes');
        }

        $postulante = Postulante::buscarPorId($id);

        if (!$postulante) {
            \set_flash('error', 'El postulante no existe.');
            \redirect('/postulantes');
        }

        $requisitos = Postulante::obtenerRequisitos($id);

        \view('postulantes.show', [
            'titulo' => 'Detalle de Postulante - SIGIE',
            'postulante' => $postulante,
            'requisitos' => $requisitos,
        ]);
    }

    public function create()
    {
        \require_login();

        $carreras = Carrera::obtenerTodos();
        $periodos = PeriodoAcademico::obtenerTodos();
        $periodoActivo = PeriodoAcademico::obtenerActivo();

        \view('postulantes.create', [
            'titulo' => 'Nuevo Postulante - SIGIE',
            'carreras' => $carreras,
            'periodos' => $periodos,
            'periodoActivo' => $periodoActivo,
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
            \redirect('/postulantes/create');
        }

        try {
            Postulante::crear($datos);

            \set_flash('success', 'Postulante registrado correctamente. Sus requisitos fueron generados en estado Pendiente.');
            \redirect('/postulantes');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo registrar el postulante: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/postulantes/create');
        }
    }

    public function edit()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Postulante no válido.');
            \redirect('/postulantes');
        }

        $postulante = Postulante::buscarPorId($id);

        if (!$postulante) {
            \set_flash('error', 'El postulante no existe.');
            \redirect('/postulantes');
        }

        $carreras = Carrera::obtenerTodos();
        $periodos = PeriodoAcademico::obtenerTodos();

        \view('postulantes.edit', [
            'titulo' => 'Editar Postulante - SIGIE',
            'postulante' => $postulante,
            'carreras' => $carreras,
            'periodos' => $periodos,
        ]);
    }

    public function update()
    {
        \require_login();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Postulante no válido.');
            \redirect('/postulantes');
        }

        $postulanteActual = Postulante::buscarPorId($id);

        if (!$postulanteActual) {
            \set_flash('error', 'El postulante no existe.');
            \redirect('/postulantes');
        }

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validar($datos, $id, $postulanteActual['persona_id']);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/postulantes/edit&id=' . $id);
        }

        try {
            Postulante::actualizar($id, $datos);

            \set_flash('success', 'Postulante actualizado correctamente.');
            \redirect('/postulantes');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo actualizar el postulante: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/postulantes/edit&id=' . $id);
        }
    }

    private function obtenerDatosFormulario()
    {
        return [
            'periodo_id' => trim($_POST['periodo_id'] ?? ''),
            'ci' => trim($_POST['ci'] ?? ''),
            'nombres' => trim($_POST['nombres'] ?? ''),
            'apellidos' => trim($_POST['apellidos'] ?? ''),
            'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
            'sexo' => trim($_POST['sexo'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'colegio' => trim($_POST['colegio'] ?? ''),
            'ciudad' => trim($_POST['ciudad'] ?? ''),
            'titulo_bachiller' => trim($_POST['titulo_bachiller'] ?? ''),
            'carrera_principal_id' => trim($_POST['carrera_principal_id'] ?? ''),
            'carrera_secundaria_id' => trim($_POST['carrera_secundaria_id'] ?? ''),
        ];
    }

    private function validar($datos, $postulanteId = null, $personaId = null)
    {
        $errores = [];

        if ($datos['periodo_id'] === '' || (int)$datos['periodo_id'] <= 0) {
            $errores[] = 'Debes seleccionar la gestión del CUP.';
        } elseif (!PeriodoAcademico::buscarPorId($datos['periodo_id'])) {
            $errores[] = 'La gestión seleccionada no existe.';
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

        if ($datos['sexo'] !== '' && !in_array($datos['sexo'], ['M', 'F'], true)) {
            $errores[] = 'El sexo seleccionado no es válido.';
        }

        if ($datos['email'] !== '' && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato válido.';
        }

        if ($datos['colegio'] === '') {
            $errores[] = 'El colegio es obligatorio.';
        }

        if ($datos['ciudad'] === '') {
            $errores[] = 'La ciudad es obligatoria.';
        }

        if ($datos['carrera_principal_id'] === '') {
            $errores[] = 'Debes seleccionar la carrera principal.';
        }

        if ($datos['carrera_secundaria_id'] === '') {
            $errores[] = 'Debes seleccionar la carrera secundaria.';
        }

        if (
            $datos['carrera_principal_id'] !== '' &&
            $datos['carrera_secundaria_id'] !== '' &&
            (string)$datos['carrera_principal_id'] === (string)$datos['carrera_secundaria_id']
        ) {
            $errores[] = 'La carrera principal y la carrera secundaria no pueden ser iguales.';
        }

        if ($datos['carrera_principal_id'] !== '' && !Carrera::buscarPorId($datos['carrera_principal_id'])) {
            $errores[] = 'La carrera principal seleccionada no existe.';
        }

        if ($datos['carrera_secundaria_id'] !== '' && !Carrera::buscarPorId($datos['carrera_secundaria_id'])) {
            $errores[] = 'La carrera secundaria seleccionada no existe.';
        }

        if ($datos['ci'] !== '' && Postulante::existeCI($datos['ci'], $personaId)) {
            $errores[] = 'El CI ya está registrado.';
        }

        if ($datos['email'] !== '' && Postulante::existeEmail($datos['email'], $personaId)) {
            $errores[] = 'El email ya está registrado.';
        }

        return $errores;
    }
}