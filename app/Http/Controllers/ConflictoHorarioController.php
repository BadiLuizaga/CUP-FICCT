<?php

namespace App\Http\Controllers;

use App\Models\ConflictoHorario;

class ConflictoHorarioController
{
    public function index()
    {
        \require_login();

        try {
            $indicadores = ConflictoHorario::obtenerIndicadores();
            $conflictosAulaHorario = ConflictoHorario::obtenerConflictosAulaHorario();
            $conflictosDocenteHorario = ConflictoHorario::obtenerConflictosDocenteHorario();
            $conflictosGrupoHorario = ConflictoHorario::obtenerConflictosGrupoHorario();
            $conflictosGrupoMateria = ConflictoHorario::obtenerConflictosGrupoMateria();
            $planificacionesConflictivas = ConflictoHorario::obtenerPlanificacionesConflictivas();

            \view('conflictos_horarios.index', [
                'titulo' => 'Conflictos de Horario - SIGIE',
                'indicadores' => $indicadores,
                'conflictosAulaHorario' => $conflictosAulaHorario,
                'conflictosDocenteHorario' => $conflictosDocenteHorario,
                'conflictosGrupoHorario' => $conflictosGrupoHorario,
                'conflictosGrupoMateria' => $conflictosGrupoMateria,
                'planificacionesConflictivas' => $planificacionesConflictivas,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al validar conflictos de horario: ' . $e->getMessage());

            \view('conflictos_horarios.index', [
                'titulo' => 'Conflictos de Horario - SIGIE',
                'indicadores' => [
                    'total_planificaciones' => 0,
                    'conflictos_aula_horario' => 0,
                    'conflictos_docente_horario' => 0,
                    'conflictos_grupo_horario' => 0,
                    'conflictos_grupo_materia' => 0,
                    'total_conflictos' => 0,
                    'estado_general' => 'Sin datos',
                ],
                'conflictosAulaHorario' => [],
                'conflictosDocenteHorario' => [],
                'conflictosGrupoHorario' => [],
                'conflictosGrupoMateria' => [],
                'planificacionesConflictivas' => [],
            ]);
        }
    }
}