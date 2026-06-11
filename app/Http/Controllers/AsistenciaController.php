<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\GrupoAcademico;
use App\Models\Conexion;

class AsistenciaController
{
    public function index()
    {
        require_login();

        $usuarioId = (int)($_SESSION['usuario']['id'] ?? 0);
        $rolesUsuario = $_SESSION['usuario']['roles'] ?? [];
        $esAdmin = in_array('Administrador', $rolesUsuario, true);

        try {
            if ($esAdmin) {
                $grupos = $this->obtenerTodosLosGrupos();
            } else {
                $gruposRaw = Asistencia::obtenerGruposPorDocente($usuarioId);
                $grupos = $this->agruparPorGrupo($gruposRaw);
            }

            view('asistencia.index', [
                'titulo' => 'Registro de Asistencia - SIGIE',
                'grupos' => $grupos,
                'esAdmin' => $esAdmin,
            ]);
        } catch (\Throwable $e) {
            set_flash('error', 'Error al cargar grupos: ' . $e->getMessage());
            view('asistencia.index', [
                'titulo' => 'Registro de Asistencia - SIGIE',
                'grupos' => [],
                'esAdmin' => $esAdmin,
            ]);
        }
    }

    private function obtenerTodosLosGrupos()
    {
        $sql = "
            SELECT DISTINCT
                g.id AS grupo_id,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre,
                m.id AS materia_id,
                m.codigo AS materia_codigo,
                m.nombre AS materia_nombre,
                pa.codigo AS periodo_codigo
            FROM grupo g
            INNER JOIN grupo_materia gm ON gm.grupo_id = g.id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN periodo_academico pa ON pa.id = g.periodo_id
            WHERE LOWER(g.estado) = 'activo'
            ORDER BY g.codigo ASC, m.nombre ASC
        ";

        $db = Conexion::getConexion();
        $stmt = $db->query($sql);
        $resultados = $stmt->fetchAll();
        return $this->agruparPorGrupo($resultados);
    }

    private function agruparPorGrupo($resultados)
    {
        $grupos = [];
        foreach ($resultados as $row) {
            $grupoId = $row['grupo_id'];
            if (!isset($grupos[$grupoId])) {
                $grupos[$grupoId] = [
                    'grupo_id' => $row['grupo_id'],
                    'grupo_codigo' => $row['grupo_codigo'],
                    'grupo_nombre' => $row['grupo_nombre'],
                    'periodo_codigo' => $row['periodo_codigo'] ?? 'Sin periodo',
                    'materias' => []
                ];
            }
            $grupos[$grupoId]['materias'][] = [
                'id' => $row['materia_id'],
                'codigo' => $row['materia_codigo'],
                'nombre' => $row['materia_nombre']
            ];
        }
        return array_values($grupos);
    }

    public function registrar()
    {
        require_login();

        $grupoId = (int)($_GET['grupo_id'] ?? 0);
        $materiaId = (int)($_GET['materia_id'] ?? 0);
        $fecha = trim($_GET['fecha'] ?? date('Y-m-d'));

        if ($grupoId <= 0 || $materiaId <= 0) {
            set_flash('error', 'Debes seleccionar un grupo y una materia válida.');
            redirect('/asistencia');
        }

        try {
            $grupo = GrupoAcademico::buscarPorId($grupoId);
            if (!$grupo) {
                set_flash('error', 'El grupo no existe.');
                redirect('/asistencia');
            }

            $postulantes = Asistencia::obtenerPostulantesPorGrupo($grupoId);
            $asistenciasExistentes = Asistencia::obtenerAsistenciaPorFecha($grupoId, $fecha, $materiaId);

            $mapaAsistencias = [];
            foreach ($asistenciasExistentes as $a) {
                $mapaAsistencias[$a['postulante_id']] = $a;
            }

            $materiasGrupo = $this->obtenerMateriasPorGrupo($grupoId);

            view('asistencia.registrar', [
                'titulo' => 'Registrar Asistencia - SIGIE',
                'grupo' => $grupo,
                'grupoId' => $grupoId,
                'materiaId' => $materiaId,
                'fecha' => $fecha,
                'postulantes' => $postulantes,
                'mapaAsistencias' => $mapaAsistencias,
                'materiasGrupo' => $materiasGrupo,
                'estadosAsistencia' => Asistencia::estadosValidos(),
            ]);
        } catch (\Throwable $e) {
            set_flash('error', 'Error al cargar registro: ' . $e->getMessage());
            redirect('/asistencia');
        }
    }

    public function store()
    {
        require_login();

        $grupoId = (int)($_POST['grupo_id'] ?? 0);
        $materiaId = (int)($_POST['materia_id'] ?? 0);
        $fecha = trim($_POST['fecha'] ?? date('Y-m-d'));
        $asistencias = $_POST['asistencia'] ?? [];

        if ($grupoId <= 0 || $materiaId <= 0) {
            set_flash('error', 'Datos de grupo o materia no válidos.');
            redirect('/asistencia');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            set_flash('error', 'La fecha no es válida.');
            redirect('/asistencia/registrar?grupo_id=' . $grupoId . '&materia_id=' . $materiaId);
        }

        $grupoMateriaId = Asistencia::obtenerGrupoMateriaId($grupoId, $materiaId);
        if (!$grupoMateriaId) {
            set_flash('error', 'No se encontró la relación grupo-materia.');
            redirect('/asistencia/registrar?grupo_id=' . $grupoId . '&materia_id=' . $materiaId);
        }

        $datosAsistencias = [];
        foreach ($asistencias as $postulanteId => $estado) {
            $observacion = trim($_POST['observacion'][$postulanteId] ?? '');
            $datosAsistencias[] = [
                'postulante_id' => (int)$postulanteId,
                'estado' => $estado,
                'observacion' => $observacion
            ];
        }

        $resultado = Asistencia::guardarAsistenciasLote($datosAsistencias, $grupoMateriaId, $fecha);

        if ($resultado['success']) {
            set_flash('success', 'Asistencia registrada para el ' . date('d/m/Y', strtotime($fecha)));
        } else {
            set_flash('error', 'Error: ' . implode(', ', $resultado['errores']));
        }

        redirect('/asistencia/registrar?grupo_id=' . $grupoId . '&materia_id=' . $materiaId . '&fecha=' . $fecha);
    }

    public function reporte()
    {
        require_login();

        $usuarioId = (int)($_SESSION['usuario']['id'] ?? 0);
        $rolesUsuario = $_SESSION['usuario']['roles'] ?? [];
        $esAdmin = in_array('Administrador', $rolesUsuario, true);

        $grupoId = (int)($_GET['grupo_id'] ?? 0);
        $fechaInicio = trim($_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days')));
        $fechaFin = trim($_GET['fecha_fin'] ?? date('Y-m-d'));

        if ($esAdmin) {
            $grupos = $this->obtenerTodosLosGrupos();
        } else {
            $gruposRaw = Asistencia::obtenerGruposPorDocente($usuarioId);
            $grupos = $this->agruparPorGrupo($gruposRaw);
        }

        $grupo = null;
        $postulantes = [];
        $resumen = [];
        $estadisticasEstudiantes = [];

        if ($grupoId > 0) {
            $grupo = GrupoAcademico::buscarPorId($grupoId);
            $postulantes = Asistencia::obtenerPostulantesPorGrupo($grupoId);
            $resumen = Asistencia::obtenerResumenAsistenciaGrupo($grupoId, $fechaInicio, $fechaFin);
            $estadisticasEstudiantes = $this->obtenerEstadisticasPorEstudiante($grupoId, $fechaInicio, $fechaFin, $postulantes);
        }

        view('asistencia.reporte', [
            'titulo' => 'Reporte de Asistencia - SIGIE',
            'grupos' => $grupos,
            'grupoId' => $grupoId,
            'grupo' => $grupo,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'postulantes' => $postulantes,
            'resumen' => $resumen,
            'estadisticasEstudiantes' => $estadisticasEstudiantes,
        ]);
    }

    private function obtenerMateriasPorGrupo($grupoId)
    {
        $sql = "
            SELECT DISTINCT
                m.id,
                m.codigo,
                m.nombre,
                m.sigla
            FROM grupo_materia gm
            INNER JOIN materia m ON m.id = gm.materia_id
            WHERE gm.grupo_id = :grupo_id
            ORDER BY m.nombre ASC
        ";

        $db = Conexion::getConexion();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':grupo_id', (int)$grupoId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function obtenerEstadisticasPorEstudiante($grupoId, $fechaInicio, $fechaFin, $postulantes)
    {
        $estadisticas = [];
        
        foreach ($postulantes as $p) {
            $pid = $p['postulante_id'];
            
            $sql = "
                SELECT 
                    COUNT(*) AS total_registros,
                    SUM(CASE WHEN a.estado = 'Presente' THEN 1 ELSE 0 END) AS presente,
                    SUM(CASE WHEN a.estado = 'Ausente' THEN 1 ELSE 0 END) AS ausente,
                    SUM(CASE WHEN a.estado = 'Justificado' THEN 1 ELSE 0 END) AS justificado,
                    SUM(CASE WHEN a.estado = 'Tarde' THEN 1 ELSE 0 END) AS tarde,
                    SUM(CASE WHEN a.estado = 'Licencia' THEN 1 ELSE 0 END) AS licencia
                FROM asistencia a
                WHERE a.postulante_id = :postulante_id
                  AND a.grupo_materia_id IN (SELECT id FROM grupo_materia WHERE grupo_id = :grupo_id)
                  AND a.fecha BETWEEN :fecha_inicio AND :fecha_fin
            ";
            
            $db = Conexion::getConexion();
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':postulante_id' => $pid,
                ':grupo_id' => $grupoId,
                ':fecha_inicio' => $fechaInicio,
                ':fecha_fin' => $fechaFin
            ]);
            
            $stats = $stmt->fetch();
            $total = (int)($stats['total_registros'] ?? 0);
            $presente = (int)($stats['presente'] ?? 0);
            $porcentaje = $total > 0 ? round(($presente / $total) * 100, 1) : 0;
            
            $estadisticas[$pid] = [
                'total' => $total,
                'presente' => $presente,
                'ausente' => (int)($stats['ausente'] ?? 0),
                'justificado' => (int)($stats['justificado'] ?? 0),
                'tarde' => (int)($stats['tarde'] ?? 0),
                'licencia' => (int)($stats['licencia'] ?? 0),
                'porcentaje' => $porcentaje,
            ];
        }
        
        return $estadisticas;
    }
}