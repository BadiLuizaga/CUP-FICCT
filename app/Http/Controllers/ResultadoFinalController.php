<?php

namespace App\Http\Controllers;

use App\Models\ResultadoFinal;
use App\Models\PeriodoAcademico;

class ResultadoFinalController
{
    public function index()
    {
        \require_login();

        try {
            $periodos = PeriodoAcademico::obtenerTodos();
            $periodoId = (int)($_GET['periodo_id'] ?? 0);
            $estado = trim($_GET['estado'] ?? '');

            if ($periodoId <= 0 && !empty($periodos)) {
                $periodoId = (int)$periodos[0]['id'];
            }

            $periodoSeleccionado = $periodoId > 0 ? PeriodoAcademico::buscarPorId($periodoId) : null;
            $resultados = $periodoId > 0 ? ResultadoFinal::obtenerPorPeriodo($periodoId, $estado) : [];
            $resumen = $periodoId > 0 ? ResultadoFinal::obtenerResumenPorPeriodo($periodoId) : [];
            $admitidosPorCarrera = $periodoId > 0 ? ResultadoFinal::obtenerAdmitidosPorCarrera($periodoId) : [];

            \view('resultados.index', [
                'titulo' => 'Resultados Finales - SIGIE',
                'periodos' => $periodos,
                'periodoId' => $periodoId,
                'estado' => $estado,
                'periodoSeleccionado' => $periodoSeleccionado,
                'resultados' => $resultados,
                'resumen' => $resumen,
                'admitidosPorCarrera' => $admitidosPorCarrera,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar resultados: ' . $e->getMessage());

            \view('resultados.index', [
                'titulo' => 'Resultados Finales - SIGIE',
                'periodos' => [],
                'periodoId' => 0,
                'estado' => '',
                'periodoSeleccionado' => null,
                'resultados' => [],
                'resumen' => [],
                'admitidosPorCarrera' => [],
            ]);
        }
    }

    public function show()
    {
        \require_login();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            \set_flash('error', 'Resultado no válido.');
            \redirect('/resultados');
        }

        $resultado = ResultadoFinal::buscarPorId($id);

        if (!$resultado) {
            \set_flash('error', 'El resultado final no existe.');
            \redirect('/resultados');
        }

        \view('resultados.show', [
            'titulo' => 'Detalle de Resultado Final - SIGIE',
            'resultado' => $resultado,
        ]);
    }

    public function generar()
    {
        \require_login();

        $periodoId = (int)($_POST['periodo_id'] ?? 0);

        if ($periodoId <= 0) {
            \set_flash('error', 'Debes seleccionar una gestión válida.');
            \redirect('/resultados');
        }

        try {
            $resumen = ResultadoFinal::generarPorPeriodo($periodoId);

            \set_flash(
                'success',
                'Resultados generados correctamente. Procesados: ' . $resumen['total_procesados'] .
                ', admitidos: ' . $resumen['total_admitidos'] .
                ', no admitidos: ' . $resumen['total_no_admitidos'] .
                ', reprobados: ' . $resumen['total_reprobados'] . '.'
            );

            \redirect('/resultados&periodo_id=' . $periodoId);
        } catch (\Throwable $e) {
            \set_flash('error', 'No se pudieron generar los resultados: ' . $e->getMessage());
            \redirect('/resultados&periodo_id=' . $periodoId);
        }
    }
}