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

        $datos = $this->construirDatosReporte($grupoId, $fechaInicio, $fechaFin);
        $grupo = $datos['grupo'];
        $postulantes = $datos['postulantes'];
        $resumen = $datos['resumen'];
        $estadisticasEstudiantes = $datos['estadisticas'];

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

    /**
     * Recibe un audio dictado, lo transcribe con Whisper (Groq) e interpreta
     * el grupo y rango de fechas para rellenar el formulario de reporte.
     * Responde SIEMPRE en JSON.
     */
    public function transcribir()
    {
        require_login();
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['ok' => false, 'error' => 'No se recibió el audio. Intenta grabar de nuevo.']);
                return;
            }

            $apiKey = env_get('OPENAI_AUDIO_API_KEY');
            $audioUrl = env_get('OPENAI_AUDIO_URL', 'https://api.groq.com/openai/v1/audio/transcriptions');
            $model = env_get('OPENAI_AUDIO_MODEL', 'whisper-large-v3-turbo');

            if (!$apiKey) {
                echo json_encode(['ok' => false, 'error' => 'Falta configurar OPENAI_AUDIO_API_KEY en el archivo .env']);
                return;
            }

            $mime = $_FILES['audio']['type'] ?: 'audio/webm';
            $cfile = new \CURLFile($_FILES['audio']['tmp_name'], $mime, 'audio.webm');

            $post = [
                'file' => $cfile,
                'model' => $model,
                'language' => 'es',
                'response_format' => 'json',
                'temperature' => '0',
            ];

            $ch = curl_init($audioUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $apiKey],
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_TIMEOUT => 60,
            ]);
            $resp = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($resp === false) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo conectar al servicio de voz: ' . $curlErr]);
                return;
            }

            $data = json_decode($resp, true);
            if ($httpCode !== 200 || !isset($data['text'])) {
                $msg = $data['error']['message'] ?? ('Respuesta inesperada del servicio de voz (HTTP ' . $httpCode . ').');
                echo json_encode(['ok' => false, 'error' => $msg]);
                return;
            }

            $texto = trim($data['text']);

            $gruposDisponibles = [];
            $decoded = json_decode($_POST['grupos'] ?? '[]', true);
            if (is_array($decoded)) {
                foreach ($decoded as $g) {
                    $g = trim((string)$g);
                    if ($g !== '') {
                        $gruposDisponibles[] = $g;
                    }
                }
            }

            $filtros = $this->extraerFiltrosDesdeTexto($texto, $gruposDisponibles);

            echo json_encode([
                'ok' => true,
                'text' => $texto,
                'grupo_codigo' => $filtros['grupo_codigo'],
                'fecha_inicio' => $filtros['fecha_inicio'],
                'fecha_fin' => $filtros['fecha_fin'],
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    /**
     * Usa el chat de Groq para convertir la transcripción en filtros
     * estructurados (grupo + fechas). Si algo falla, devuelve nulos para
     * que el usuario complete manualmente.
     */
    private function extraerFiltrosDesdeTexto($texto, $gruposDisponibles)
    {
        $vacio = ['grupo_codigo' => null, 'fecha_inicio' => null, 'fecha_fin' => null];

        $apiKey = env_get('OPENAI_AUDIO_API_KEY');
        $chatUrl = env_get('OPENAI_CHAT_URL', 'https://api.groq.com/openai/v1/chat/completions');
        $chatModel = env_get('OPENAI_CHAT_MODEL', 'llama-3.3-70b-versatile');

        if (!$apiKey || $texto === '') {
            return $vacio;
        }

        $hoy = date('Y-m-d');
        $listaGrupos = empty($gruposDisponibles) ? '(ninguno)' : implode(', ', $gruposDisponibles);

        $system = "Eres un asistente que extrae los filtros de un reporte de asistencia a partir de un texto dictado en español. "
            . "La fecha de hoy es {$hoy}. "
            . "Devuelve EXCLUSIVAMENTE un objeto JSON con las claves: grupo_codigo, fecha_inicio, fecha_fin. "
            . "grupo_codigo debe ser EXACTAMENTE uno de esta lista, o null si no se menciona: {$listaGrupos}. "
            . "fecha_inicio y fecha_fin en formato YYYY-MM-DD, o null si no se mencionan. "
            . "Interpreta meses en español. Si no se dice el año, usa el del contexto de hoy. No agregues texto fuera del JSON.";

        $payload = [
            'model' => $chatModel,
            'temperature' => 0,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $texto],
            ],
        ];

        $ch = curl_init($chatUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30,
        ]);
        $resp = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false || $httpCode !== 200) {
            return $vacio;
        }

        $data = json_decode($resp, true);
        $content = $data['choices'][0]['message']['content'] ?? '';
        $parsed = json_decode($content, true);
        if (!is_array($parsed)) {
            return $vacio;
        }

        return [
            'grupo_codigo' => $this->normalizarGrupo($parsed['grupo_codigo'] ?? null, $gruposDisponibles),
            'fecha_inicio' => $this->normalizarFecha($parsed['fecha_inicio'] ?? null),
            'fecha_fin' => $this->normalizarFecha($parsed['fecha_fin'] ?? null),
        ];
    }

    private function normalizarFecha($fecha)
    {
        if (!is_string($fecha)) {
            return null;
        }
        $fecha = trim($fecha);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) ? $fecha : null;
    }

    private function normalizarGrupo($codigo, $gruposDisponibles)
    {
        if (!is_string($codigo) || trim($codigo) === '') {
            return null;
        }
        $codigo = trim($codigo);

        foreach ($gruposDisponibles as $g) {
            if (strcasecmp($g, $codigo) === 0) {
                return $g;
            }
        }

        $norm = strtoupper(preg_replace('/\s+/', '', $codigo));
        foreach ($gruposDisponibles as $g) {
            if (strtoupper(preg_replace('/\s+/', '', $g)) === $norm) {
                return $g;
            }
        }

        return null;
    }

    /**
     * Obtiene todos los datos del reporte (grupo, estudiantes, resumen y
     * estadísticas) para una combinación de grupo + rango de fechas.
     * Reutilizado por reporte(), exportarExcel() y exportarHtml().
     */
    private function construirDatosReporte($grupoId, $fechaInicio, $fechaFin)
    {
        $datos = ['grupo' => null, 'postulantes' => [], 'resumen' => [], 'estadisticas' => []];

        if ($grupoId > 0) {
            $datos['grupo'] = GrupoAcademico::buscarPorId($grupoId);
            $datos['postulantes'] = Asistencia::obtenerPostulantesPorGrupo($grupoId);
            $datos['resumen'] = Asistencia::obtenerResumenAsistenciaGrupo($grupoId, $fechaInicio, $fechaFin);
            $datos['estadisticas'] = $this->obtenerEstadisticasPorEstudiante($grupoId, $fechaInicio, $fechaFin, $datos['postulantes']);
        }

        return $datos;
    }

    /**
     * Lee y valida los parámetros del reporte desde la query string.
     * Devuelve null y redirige si no hay grupo seleccionado.
     */
    private function parametrosReporteOExit()
    {
        require_login();

        $grupoId = (int)($_GET['grupo_id'] ?? 0);
        $fechaInicio = trim($_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days')));
        $fechaFin = trim($_GET['fecha_fin'] ?? date('Y-m-d'));

        if ($grupoId <= 0) {
            set_flash('error', 'Selecciona un grupo antes de exportar el reporte.');
            redirect('/asistencia/reporte');
        }

        return [$grupoId, $fechaInicio, $fechaFin];
    }

    /**
     * Exporta el reporte a un archivo abrible en Excel (.xls vía tabla HTML).
     */
    public function exportarExcel()
    {
        [$grupoId, $fechaInicio, $fechaFin] = $this->parametrosReporteOExit();

        $datos = $this->construirDatosReporte($grupoId, $fechaInicio, $fechaFin);
        $grupo = $datos['grupo'];
        $postulantes = $datos['postulantes'];
        $resumen = $datos['resumen'];
        $estadisticas = $datos['estadisticas'];

        $codigo = preg_replace('/[^A-Za-z0-9_\-]/', '', (string)($grupo['codigo'] ?? 'grupo'));
        $nombreArchivo = 'reporte_asistencia_' . $codigo . '_' . $fechaInicio . '_a_' . $fechaFin . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $periodo = date('d/m/Y', strtotime($fechaInicio)) . ' - ' . date('d/m/Y', strtotime($fechaFin));
        $estados = ['Presente', 'Ausente', 'Justificado', 'Tarde', 'Licencia'];

        echo "\xEF\xBB\xBF"; // BOM para que Excel interprete UTF-8
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"></head><body>';
        echo '<table border="1" cellspacing="0" cellpadding="4">';

        echo '<tr><th colspan="9" style="background:#1f2937;color:#ffffff;font-size:14px;">Reporte de Asistencia - SIGAUCP</th></tr>';
        echo '<tr><td><b>Grupo</b></td><td colspan="8">' . e(($grupo['codigo'] ?? '-') . ' - ' . ($grupo['nombre'] ?? '-')) . '</td></tr>';
        echo '<tr><td><b>Período</b></td><td colspan="8">' . e($periodo) . '</td></tr>';
        echo '<tr><td><b>Total estudiantes</b></td><td colspan="8">' . count($postulantes) . '</td></tr>';
        echo '<tr><td><b>Total registros</b></td><td colspan="8">' . array_sum($resumen) . '</td></tr>';
        echo '<tr><td colspan="9"></td></tr>';

        echo '<tr><th colspan="9" style="background:#e9ecef;">Resumen por estado</th></tr>';
        echo '<tr>';
        foreach ($estados as $estado) {
            echo '<th style="background:#343a40;color:#ffffff;">' . e($estado) . '</th>';
        }
        echo '</tr><tr>';
        foreach ($estados as $estado) {
            echo '<td>' . (int)($resumen[$estado] ?? 0) . '</td>';
        }
        echo '</tr><tr><td colspan="9"></td></tr>';

        echo '<tr><th colspan="9" style="background:#e9ecef;">Detalle por estudiante</th></tr>';
        echo '<tr style="background:#1f2937;color:#ffffff;">'
            . '<th>CI</th><th>Estudiante</th><th>Presente</th><th>Ausente</th><th>Justificado</th>'
            . '<th>Tarde</th><th>Licencia</th><th>Total</th><th>% Asist.</th></tr>';

        foreach ($postulantes as $p) {
            $st = $estadisticas[$p['postulante_id']] ?? [
                'total' => 0, 'presente' => 0, 'ausente' => 0,
                'justificado' => 0, 'tarde' => 0, 'licencia' => 0, 'porcentaje' => 0,
            ];
            echo '<tr>'
                . '<td>' . e($p['ci']) . '</td>'
                . '<td>' . e(trim($p['nombres'] . ' ' . $p['apellidos'])) . '</td>'
                . '<td>' . (int)$st['presente'] . '</td>'
                . '<td>' . (int)$st['ausente'] . '</td>'
                . '<td>' . (int)$st['justificado'] . '</td>'
                . '<td>' . (int)$st['tarde'] . '</td>'
                . '<td>' . (int)$st['licencia'] . '</td>'
                . '<td>' . (int)$st['total'] . '</td>'
                . '<td>' . e($st['porcentaje']) . '%</td>'
                . '</tr>';
        }

        echo '</table></body></html>';
        exit;
    }

    /**
     * Exporta el reporte a un archivo HTML autónomo (con gráfica via Chart.js).
     */
    public function exportarHtml()
    {
        [$grupoId, $fechaInicio, $fechaFin] = $this->parametrosReporteOExit();

        $datos = $this->construirDatosReporte($grupoId, $fechaInicio, $fechaFin);
        $grupo = $datos['grupo'];
        $postulantes = $datos['postulantes'];
        $resumen = $datos['resumen'];
        $estadisticas = $datos['estadisticas'];

        $codigo = preg_replace('/[^A-Za-z0-9_\-]/', '', (string)($grupo['codigo'] ?? 'grupo'));
        $nombreArchivo = 'reporte_asistencia_' . $codigo . '_' . $fechaInicio . '_a_' . $fechaFin . '.html';

        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $estados = ['Presente', 'Ausente', 'Justificado', 'Tarde', 'Licencia'];
        $valores = [];
        foreach ($estados as $estado) {
            $valores[] = (int)($resumen[$estado] ?? 0);
        }

        $filas = '';
        foreach ($postulantes as $p) {
            $st = $estadisticas[$p['postulante_id']] ?? [
                'total' => 0, 'presente' => 0, 'ausente' => 0,
                'justificado' => 0, 'tarde' => 0, 'licencia' => 0, 'porcentaje' => 0,
            ];
            $color = $st['porcentaje'] >= 80 ? '#198754' : ($st['porcentaje'] >= 60 ? '#fd7e14' : '#dc3545');
            $filas .= '<tr>'
                . '<td>' . e($p['ci']) . '</td>'
                . '<td>' . e(trim($p['nombres'] . ' ' . $p['apellidos'])) . '</td>'
                . '<td class="c">' . (int)$st['presente'] . '</td>'
                . '<td class="c">' . (int)$st['ausente'] . '</td>'
                . '<td class="c">' . (int)$st['justificado'] . '</td>'
                . '<td class="c">' . (int)$st['tarde'] . '</td>'
                . '<td class="c">' . (int)$st['licencia'] . '</td>'
                . '<td class="c">' . (int)$st['total'] . '</td>'
                . '<td class="c" style="color:' . $color . ';font-weight:bold;">' . e($st['porcentaje']) . '%</td>'
                . '</tr>';
        }

        $grupoTexto = e(($grupo['codigo'] ?? '-') . ' - ' . ($grupo['nombre'] ?? '-'));
        $periodo = e(date('d/m/Y', strtotime($fechaInicio)) . ' - ' . date('d/m/Y', strtotime($fechaFin)));
        $totalEstudiantes = count($postulantes);
        $totalRegistros = array_sum($resumen);
        $estadosJson = json_encode($estados);
        $valoresJson = json_encode($valores);
        $generado = date('d/m/Y H:i');

        echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Asistencia - {$grupoTexto}</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
  body { font-family: Arial, Helvetica, sans-serif; margin: 30px; color: #212529; }
  h1 { margin: 0 0 4px; }
  .muted { color: #6c757d; }
  .info { display: flex; flex-wrap: wrap; gap: 24px; margin: 20px 0; }
  table { border-collapse: collapse; width: 100%; margin-top: 16px; }
  th, td { border: 1px solid #dee2e6; padding: 8px 10px; font-size: 14px; }
  thead th { background: #1f2937; color: #fff; }
  tbody tr:nth-child(even) { background: #f8f9fa; }
  .c { text-align: center; }
  .cards { display: flex; flex-wrap: wrap; gap: 12px; margin: 20px 0; }
  .card { border: 1px solid #dee2e6; border-radius: 8px; padding: 12px 18px; text-align: center; min-width: 110px; }
  .card .n { font-size: 24px; font-weight: bold; }
  .chart-box { max-width: 420px; margin: 24px 0; }
  footer { margin-top: 30px; font-size: 12px; color: #6c757d; }
</style>
</head>
<body>
  <h1>Reporte de asistencia</h1>
  <p class="muted">Resumen de asistencia por grupo y período &middot; SIGAUCP — FICCT · UAGRM</p>

  <div class="info">
    <div><b>Grupo:</b> {$grupoTexto}</div>
    <div><b>Período:</b> {$periodo}</div>
    <div><b>Total estudiantes:</b> {$totalEstudiantes}</div>
    <div><b>Total registros:</b> {$totalRegistros}</div>
  </div>

  <div class="cards" id="cards"></div>
  <div class="chart-box"><canvas id="grafico"></canvas></div>

  <table>
    <thead>
      <tr>
        <th>CI</th><th>Estudiante</th><th>Presente</th><th>Ausente</th><th>Justificado</th>
        <th>Tarde</th><th>Licencia</th><th>Total</th><th>% Asist.</th>
      </tr>
    </thead>
    <tbody>
      {$filas}
    </tbody>
  </table>

  <footer>Generado el {$generado} desde SIGAUCP.</footer>

<script>
  const estados = {$estadosJson};
  const valores = {$valoresJson};
  const colores = ['#198754', '#dc3545', '#ffc107', '#0d6efd', '#6c757d'];

  const cards = document.getElementById('cards');
  estados.forEach(function (et, i) {
    cards.innerHTML += '<div class="card"><div style="color:' + colores[i] + '">' + et + '</div><div class="n">' + valores[i] + '</div></div>';
  });

  new Chart(document.getElementById('grafico'), {
    type: 'pie',
    data: { labels: estados, datasets: [{ data: valores, backgroundColor: colores }] },
    options: { plugins: { legend: { position: 'bottom' } } }
  });
</script>
</body>
</html>
HTML;
        exit;
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
