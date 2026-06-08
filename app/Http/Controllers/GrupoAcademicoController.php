<?php

namespace App\Http\Controllers;

use App\Models\GrupoAcademico;

class GrupoAcademicoController
{
    public function index()
    {
        \require_login();

        try {
            $indicadores = GrupoAcademico::obtenerIndicadores();
            $grupos = GrupoAcademico::obtenerGrupos();

            \view('grupos_academicos.index', [
                'titulo' => 'Grupos Académicos - SIGIE',
                'indicadores' => $indicadores,
                'grupos' => $grupos,
                'estadosValidos' => GrupoAcademico::estadosValidos(),
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar grupos académicos: ' . $e->getMessage());

            \view('grupos_academicos.index', [
                'titulo' => 'Grupos Académicos - SIGIE',
                'indicadores' => [
                    'capacidad_aula' => GrupoAcademico::CAPACIDAD_AULA,
                    'total_grupos' => 0,
                    'total_inscritos' => 0,
                    'capacidad_total' => 0,
                    'cupos_libres' => 0,
                    'grupos_activos' => 0,
                    'grupos_saturados' => 0,
                    'grupos_inactivos' => 0,
                    'grupos_cerrados' => 0,
                    'grupos_necesarios_capacidad_60' => 0,
                    'porcentaje_general' => 0,
                ],
                'grupos' => [],
                'estadosValidos' => GrupoAcademico::estadosValidos(),
            ]);
        }
    }

    public function recalcular()
    {
        \require_login();

        try {
            $total = GrupoAcademico::sincronizarCantidadEstudiantes();

            \set_flash('success', 'Grupos académicos recalculados correctamente. Total de grupos actualizados: ' . $total . '.');
            \redirect('/grupos-academicos');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo recalcular los grupos académicos: ' . $e->getMessage());
            \redirect('/grupos-academicos');
        }
    }

    public function cambiarEstado()
    {
        \require_login();

        $grupoId = (int)($_POST['grupo_id'] ?? 0);
        $estado = trim($_POST['estado'] ?? '');

        if ($grupoId <= 0) {
            \set_flash('error', 'Grupo académico no válido.');
            \redirect('/grupos-academicos');
        }

        if ($estado === '') {
            \set_flash('error', 'Debes seleccionar un estado.');
            \redirect('/grupos-academicos');
        }

        try {
            GrupoAcademico::cambiarEstado($grupoId, $estado);

            \set_flash('success', 'Estado del grupo actualizado correctamente.');
            \redirect('/grupos-academicos');
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudo cambiar el estado del grupo: ' . $e->getMessage());
            \redirect('/grupos-academicos');
        }
    }
}