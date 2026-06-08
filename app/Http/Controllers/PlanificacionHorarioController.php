<?php

namespace App\Http\Controllers;

use App\Models\PlanificacionHorario;

class PlanificacionHorarioController
{
    public function index()
    {
        \require_login();

        try {
            $planificaciones = PlanificacionHorario::obtenerTodos();
            $indicadores = PlanificacionHorario::obtenerIndicadores();

            \view('planificacion_horaria.index', [
                'titulo' => 'Planificación Horaria - SIGIE',
                'planificaciones' => $planificaciones,
                'indicadores' => $indicadores,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar la planificación horaria: ' . $e->getMessage());

            \view('planificacion_horaria.index', [
                'titulo' => 'Planificación Horaria - SIGIE',
                'planificaciones' => [],
                'indicadores' => [
                    'total_planificaciones' => 0,
                    'total_grupos_planificados' => 0,
                    'total_docentes_asignados' => 0,
                    'total_aulas_usadas' => 0,
                    'total_horarios_usados' => 0,
                ],
            ]);
        }
    }

    public function show()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Planificación no válida.');
            \redirect('/planificacion-horaria');
        }

        try {
            $planificacion = PlanificacionHorario::buscarPorId($id);

            if (!$planificacion) {
                \set_flash('error', 'La planificación no existe.');
                \redirect('/planificacion-horaria');
            }

            \view('planificacion_horaria.show', [
                'titulo' => 'Detalle de Planificación Horaria - SIGIE',
                'planificacion' => $planificacion,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar la planificación: ' . $e->getMessage());
            \redirect('/planificacion-horaria');
        }
    }

    public function create()
    {
        \require_login();

        try {
            $catalogos = PlanificacionHorario::obtenerCatalogos();

            \view('planificacion_horaria.create', [
                'titulo' => 'Nueva Planificación Horaria - SIGIE',
                'catalogos' => $catalogos,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            \redirect('/planificacion-horaria');
        }
    }

    public function store()
    {
        \require_login();

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validarFormulario($datos);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/planificacion-horaria/create');
        }

        try {
            PlanificacionHorario::crear($datos);

            \set_flash('success', 'Planificación horaria registrada correctamente.');
            \redirect('/planificacion-horaria');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo registrar la planificación: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/planificacion-horaria/create');
        }
    }

    public function edit()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Planificación no válida.');
            \redirect('/planificacion-horaria');
        }

        try {
            $planificacion = PlanificacionHorario::buscarPorId($id);

            if (!$planificacion) {
                \set_flash('error', 'La planificación no existe.');
                \redirect('/planificacion-horaria');
            }

            $catalogos = PlanificacionHorario::obtenerCatalogos();

            \view('planificacion_horaria.edit', [
                'titulo' => 'Editar Planificación Horaria - SIGIE',
                'planificacion' => $planificacion,
                'catalogos' => $catalogos,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar la planificación: ' . $e->getMessage());
            \redirect('/planificacion-horaria');
        }
    }

    public function update()
    {
        \require_login();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Planificación no válida.');
            \redirect('/planificacion-horaria');
        }

        $datos = $this->obtenerDatosFormulario();
        $errores = $this->validarFormulario($datos);

        if (!empty($errores)) {
            \set_flash('error', implode(' ', $errores));
            \keep_old($_POST);
            \redirect('/planificacion-horaria/edit&id=' . $id);
        }

        try {
            PlanificacionHorario::actualizar($id, $datos);

            \set_flash('success', 'Planificación horaria actualizada correctamente.');
            \redirect('/planificacion-horaria');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo actualizar la planificación: ' . $e->getMessage());
            \keep_old($_POST);
            \redirect('/planificacion-horaria/edit&id=' . $id);
        }
    }

    private function obtenerDatosFormulario()
    {
        return [
            'grupo_id' => trim($_POST['grupo_id'] ?? ''),
            'materia_id' => trim($_POST['materia_id'] ?? ''),
            'docente_id' => trim($_POST['docente_id'] ?? ''),
            'horario_id' => trim($_POST['horario_id'] ?? ''),
            'aula_id' => trim($_POST['aula_id'] ?? ''),
        ];
    }

    private function validarFormulario($datos)
    {
        $errores = [];

        if ($datos['grupo_id'] === '' || (int)$datos['grupo_id'] <= 0) {
            $errores[] = 'Debes seleccionar un grupo.';
        }

        if ($datos['materia_id'] === '' || (int)$datos['materia_id'] <= 0) {
            $errores[] = 'Debes seleccionar una materia.';
        }

        if ($datos['docente_id'] === '' || (int)$datos['docente_id'] <= 0) {
            $errores[] = 'Debes seleccionar un docente.';
        }

        if ($datos['horario_id'] === '' || (int)$datos['horario_id'] <= 0) {
            $errores[] = 'Debes seleccionar un horario.';
        }

        if ($datos['aula_id'] === '' || (int)$datos['aula_id'] <= 0) {
            $errores[] = 'Debes seleccionar un aula.';
        }

        return $errores;
    }
}