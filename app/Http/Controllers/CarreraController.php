<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\PeriodoAcademico;

class CarreraController
{
    public function index()
    {
        \require_login();

        try {
            $carreras = Carrera::obtenerTodos();

            \view('carreras.index', [
                'titulo' => 'Carreras - SIGIE',
                'carreras' => $carreras,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al listar carreras: ' . $e->getMessage());

            \view('carreras.index', [
                'titulo' => 'Carreras - SIGIE',
                'carreras' => [],
            ]);
        }
    }

    public function show()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Carrera no válida.');
            \redirect('/carreras');
        }

        $carrera = Carrera::buscarPorId($id);

        if (!$carrera) {
            \set_flash('error', 'La carrera no existe.');
            \redirect('/carreras');
        }

        \view('carreras.show', [
            'titulo' => 'Detalle de Carrera - SIGIE',
            'carrera' => $carrera,
        ]);
    }

    public function create()
    {
        \require_login();

        \view('carreras.create', [
            'titulo' => 'Nueva Carrera - SIGIE',
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
            \redirect('/carreras/create');
        }

        try {
            Carrera::crear($datos);

            \set_flash('success', 'Carrera registrada correctamente.');
            \redirect('/carreras');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo registrar la carrera: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/carreras/create');
        }
    }

    public function edit()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Carrera no válida.');
            \redirect('/carreras');
        }

        $carrera = Carrera::buscarPorId($id);

        if (!$carrera) {
            \set_flash('error', 'La carrera no existe.');
            \redirect('/carreras');
        }

        \view('carreras.edit', [
            'titulo' => 'Editar Carrera - SIGIE',
            'carrera' => $carrera,
        ]);
    }

    public function update()
    {
        \require_login();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Carrera no válida.');
            \redirect('/carreras');
        }

        if (!Carrera::buscarPorId($id)) {
            \set_flash('error', 'La carrera no existe.');
            \redirect('/carreras');
        }

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validar($datos, $id);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/carreras/edit&id=' . $id);
        }

        try {
            Carrera::actualizar($id, $datos);

            \set_flash('success', 'Carrera actualizada correctamente.');
            \redirect('/carreras');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo actualizar la carrera: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/carreras/edit&id=' . $id);
        }
    }

    public function cupos()
    {
        \require_login();

        try {
            $periodos = PeriodoAcademico::obtenerTodos();
            $periodoActivo = PeriodoAcademico::obtenerActivo();
            $periodoId = (int)($_GET['periodo_id'] ?? 0);

            if ($periodoId <= 0 && $periodoActivo) {
                $periodoId = (int)$periodoActivo['id'];
            }

            if ($periodoId <= 0 && !empty($periodos)) {
                $periodoId = (int)$periodos[0]['id'];
            }

            $periodoSeleccionado = $periodoId > 0 ? PeriodoAcademico::buscarPorId($periodoId) : null;
            $carreras = $periodoId > 0 ? Carrera::obtenerCuposPorPeriodo($periodoId) : [];

            \view('carreras.cupos', [
                'titulo' => 'Cupos por Carrera y Gestión - SIGIE',
                'periodos' => $periodos,
                'periodoSeleccionado' => $periodoSeleccionado,
                'periodoId' => $periodoId,
                'carreras' => $carreras,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar cupos: ' . $e->getMessage());

            \view('carreras.cupos', [
                'titulo' => 'Cupos por Carrera y Gestión - SIGIE',
                'periodos' => [],
                'periodoSeleccionado' => null,
                'periodoId' => 0,
                'carreras' => [],
            ]);
        }
    }

    public function actualizarCupo()
    {
        \require_login();

        $periodoId = (int)($_POST['periodo_id'] ?? 0);
        $carreraId = (int)($_POST['carrera_id'] ?? 0);
        $cupoMaximo = trim($_POST['cupo_maximo'] ?? '');

        if ($periodoId <= 0 || !PeriodoAcademico::buscarPorId($periodoId)) {
            \set_flash('error', 'Periodo académico no válido.');
            \redirect('/carreras/cupos');
        }

        if ($carreraId <= 0 || !Carrera::buscarPorId($carreraId)) {
            \set_flash('error', 'Carrera no válida.');
            \redirect('/carreras/cupos&periodo_id=' . $periodoId);
        }

        if ($cupoMaximo === '' || !ctype_digit($cupoMaximo) || (int)$cupoMaximo <= 0) {
            \set_flash('error', 'El cupo máximo debe ser un número entero mayor a cero.');
            \redirect('/carreras/cupos&periodo_id=' . $periodoId);
        }

        try {
            Carrera::actualizarCupoPorPeriodo($periodoId, $carreraId, $cupoMaximo);
            Carrera::actualizarCupo($carreraId, $cupoMaximo);

            \set_flash('success', 'Cupo actualizado correctamente para la gestión seleccionada.');
            \redirect('/carreras/cupos&periodo_id=' . $periodoId);
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo actualizar el cupo: ' . $e->getMessage());
            \redirect('/carreras/cupos&periodo_id=' . $periodoId);
        }
    }

    private function obtenerDatosFormulario()
    {
        return [
            'codigo' => trim($_POST['codigo'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'cupo_maximo' => trim($_POST['cupo_maximo'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
        ];
    }

    private function validar($datos, $id = null)
    {
        $errores = [];

        if ($datos['codigo'] === '') {
            $errores[] = 'El código de la carrera es obligatorio.';
        }

        if ($datos['nombre'] === '') {
            $errores[] = 'El nombre de la carrera es obligatorio.';
        }

        if ($datos['cupo_maximo'] === '') {
            $errores[] = 'El cupo máximo es obligatorio.';
        }

        if ($datos['codigo'] !== '' && strlen($datos['codigo']) > 20) {
            $errores[] = 'El código no debe superar los 20 caracteres.';
        }

        if ($datos['nombre'] !== '' && strlen($datos['nombre']) > 100) {
            $errores[] = 'El nombre no debe superar los 100 caracteres.';
        }

        if ($datos['cupo_maximo'] !== '' && (!ctype_digit($datos['cupo_maximo']) || (int)$datos['cupo_maximo'] <= 0)) {
            $errores[] = 'El cupo máximo debe ser un número entero mayor a cero.';
        }

        if ($datos['codigo'] !== '' && Carrera::existeCodigo($datos['codigo'], $id)) {
            $errores[] = 'Ya existe una carrera con ese código.';
        }

        if ($datos['nombre'] !== '' && Carrera::existeNombre($datos['nombre'], $id)) {
            $errores[] = 'Ya existe una carrera con ese nombre.';
        }

        return $errores;
    }
}