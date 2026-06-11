<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Nota;
use App\Models\Postulante;
use App\Models\GrupoAcademico;

class ExamenController
{
    public function index()
    {
        require_login();

        $grupos = GrupoAcademico::obtenerGrupos();

        $examenesDisponibles = [];
        foreach ($grupos as $g) {
            $examenesGrupo = Examen::obtenerExamenesPorGrupo($g['id']);
            foreach ($examenesGrupo as $ex) {
                $examenesDisponibles[] = $ex;
            }
        }

        view('examenes.index', [
            'titulo' => 'Exámenes Parciales - SIGIE',
            'grupos' => $grupos,
            'examenesDisponibles' => $examenesDisponibles
        ]);
    }

    public function rendir()
    {
        require_login();

        $examenId = (int)($_GET['examen_id'] ?? 0);
        $usuarioId = (int)($_SESSION['usuario']['id'] ?? 0);

        $postulante = Postulante::obtenerPorUsuarioId($usuarioId);

        if (!$postulante) {
            set_flash('error', 'No eres un postulante válido');
            redirect('/examenes');
        }

        $postulanteId = $postulante['id'];

        if ($examenId <= 0) {
            set_flash('error', 'Examen no válido');
            redirect('/examenes');
        }

        $preguntas = Examen::obtenerPreguntas($examenId);

        if (empty($preguntas)) {
            set_flash('error', 'Este examen no tiene preguntas registradas');
            redirect('/examenes');
        }

        view('examenes.rendir', [
            'titulo' => 'Rendir Examen - SIGIE',
            'examenId' => $examenId,
            'preguntas' => $preguntas,
            'postulanteId' => $postulanteId
        ]);
    }

    public function storeRespuestas()
    {
        require_login();

        $postulanteId = (int)($_POST['postulante_id'] ?? 0);
        $examenId = (int)($_POST['examen_id'] ?? 0);
        $respuestas = $_POST['respuestas'] ?? [];

        if ($postulanteId <= 0 || $examenId <= 0 || empty($respuestas)) {
            set_flash('error', 'Datos incompletos');
            redirect('/examenes');
        }

        try {
            Examen::guardarRespuestas($postulanteId, $examenId, $respuestas);
            set_flash('success', 'Examen rendido correctamente');
        } catch (\Exception $e) {
            set_flash('error', 'Error al guardar: ' . $e->getMessage());
        }

        redirect('/examenes');
    }

    public function asentar()
    {
        require_login();

        $grupoId = (int)($_GET['grupo_id'] ?? 0);

        if ($grupoId <= 0) {
            set_flash('error', 'Grupo no válido');
            redirect('/examenes');
        }

        $examenes = Nota::obtenerExamenesPorGrupo($grupoId);
        $notas = Nota::obtenerNotasPorGrupo($grupoId);

        view('examenes.asentar', [
            'titulo' => 'Asentar Notas - SIGIE',
            'grupoId' => $grupoId,
            'examenes' => $examenes,
            'notas' => $notas
        ]);
    }

    public function storeNotas()
    {
        require_login();

        $grupoId = (int)($_POST['grupo_id'] ?? 0);
        $notas = $_POST['notas'] ?? [];

        if ($grupoId <= 0 || empty($notas)) {
            set_flash('error', 'Datos incompletos');
            redirect('/examenes');
        }

        $resultado = Nota::guardarNotas($grupoId, $notas);

        if ($resultado['success']) {
            set_flash('success', 'Notas registradas correctamente');
        } else {
            set_flash('error', 'Error: ' . $resultado['error']);
        }

        redirect('/examenes/asentar?grupo_id=' . $grupoId);
    }

    public function finalizarExamen()
    {
        require_login();

        $usuarioId = (int)($_SESSION['usuario']['id'] ?? 0);
        $postulante = Postulante::obtenerPorUsuarioId($usuarioId);

        if (!$postulante) {
            set_flash('error', 'Acceso no autorizado');
            redirect('/examenes');
        }

        $postulanteId = $postulante['id'];
        $resultado = Examen::calcularPromedioFinalPostulante($postulanteId);

        if ($resultado['estado'] == 'Aprobado') {
            set_flash('success', 'Promedio final: ' . $resultado['promedio'] . ' - APROBADO');
        } elseif ($resultado['estado'] == 'Reprobado') {
            set_flash('error', 'Promedio final: ' . $resultado['promedio'] . ' - REPROBADO');
        } else {
            set_flash('error', 'Debes rendir los 3 exámenes antes de finalizar');
        }

        redirect('/dashboard');
    }
}