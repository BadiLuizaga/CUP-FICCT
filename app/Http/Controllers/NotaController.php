<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\GrupoAcademico;
use App\Models\Inscripcion;
use App\Models\Carrera;
use App\Models\Conexion;

class NotaController
{
    public function index()
    {
        require_login();

        $roles = $_SESSION['usuario']['roles'] ?? [];
        $esAdmin = in_array('Administrador', $roles, true);

        if ($esAdmin) {
            $grupos = GrupoAcademico::obtenerGrupos();
        } else {
            $grupos = GrupoAcademico::obtenerGrupos();
        }

        view('notas.index', [
            'titulo' => 'Asentar Calificaciones - SIGIE',
            'grupos' => $grupos,
            'esAdmin' => $esAdmin
        ]);
    }

    public function edit()
    {
        require_login();

        $grupoId = (int)($_GET['grupo_id'] ?? 0);

        if ($grupoId <= 0) {
            set_flash('error', 'Grupo no válido');
            redirect('/notas');
        }

        // Obtener los estudiantes inscritos en el grupo
        $estudiantes = Inscripcion::obtenerPostulantesPorGrupo($grupoId);
        
        // Obtener los exámenes del grupo
        $examenes = Nota::obtenerExamenesPorGrupo($grupoId);
        
        // Obtener notas existentes
        $notasExistentes = Nota::obtenerNotasPorGrupo($grupoId);
        
        // Organizar notas por postulante y examen
        $mapaNotas = [];
        foreach ($notasExistentes as $n) {
            $mapaNotas[$n['postulante_id']][$n['examen_id']] = $n['valor'];
        }

        if (empty($estudiantes)) {
            set_flash('error', 'Este grupo no tiene estudiantes inscritos');
            redirect('/notas');
        }

        if (empty($examenes)) {
            set_flash('error', 'Este grupo no tiene exámenes registrados');
            redirect('/notas');
        }

        view('notas.edit', [
            'titulo' => 'Registrar Notas - SIGIE',
            'grupoId' => $grupoId,
            'estudiantes' => $estudiantes,
            'examenes' => $examenes,
            'mapaNotas' => $mapaNotas
        ]);
    }

    public function store()
    {
        require_login();

        $grupoId = (int)($_POST['grupo_id'] ?? 0);
        $notas = $_POST['notas'] ?? [];

        if ($grupoId <= 0 || empty($notas)) {
            set_flash('error', 'Datos incompletos');
            redirect('/notas');
        }

        // Obtener el periodo_id del grupo
        $db = Conexion::getConexion();
        $sqlPeriodo = "SELECT periodo_id FROM grupo WHERE id = :grupo_id";
        $stmtPeriodo = $db->prepare($sqlPeriodo);
        $stmtPeriodo->execute([':grupo_id' => $grupoId]);
        $periodo = $stmtPeriodo->fetch();
        $periodoId = $periodo ? $periodo['periodo_id'] : null;

        $resultado = Nota::guardarNotas($grupoId, $notas);

        if ($resultado['success']) {
            $mensajes = [];
            
            // Para cada postulante, recalcular su promedio y asignar cupo si corresponde
            foreach ($notas as $postulanteId => $examenesNotas) {
                $resultadoPromedio = Nota::calcularPromedioFinalPostulante($postulanteId, $grupoId);
                
                if ($resultadoPromedio['estado'] == 'Aprobado' && $periodoId) {
                    // Verificar si ya tiene admisión
                    $sqlCheck = "SELECT id, estado_final FROM resultado_final WHERE postulante_id = :postulante_id";
                    $stmtCheck = $db->prepare($sqlCheck);
                    $stmtCheck->execute([':postulante_id' => $postulanteId]);
                    $existente = $stmtCheck->fetch();
                    
                    // Solo asignar cupo si no está ya admitido
                    if (!$existente || $existente['estado_final'] != 'ADMITIDO') {
                        $asignacion = Carrera::asignarCupoSiDisponible($postulanteId, $periodoId);
                        if ($asignacion['success']) {
                            $mensajes[] = $asignacion['mensaje'];
                        }
                    }
                } elseif ($resultadoPromedio['estado'] == 'Reprobado' && $periodoId) {
                    // Registrar como reprobado si no existe resultado
                    $sqlCheck = "SELECT id FROM resultado_final WHERE postulante_id = :postulante_id";
                    $stmtCheck = $db->prepare($sqlCheck);
                    $stmtCheck->execute([':postulante_id' => $postulanteId]);
                    $existente = $stmtCheck->fetch();
                    
                    if (!$existente) {
                        $sql = "
                            INSERT INTO resultado_final (
                                postulante_id, periodo_id, promedio_general, 
                                estado_final, observacion
                            ) VALUES (
                                :postulante_id, :periodo_id, :promedio,
                                'REPROBADO', :observacion
                            )
                        ";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([
                            ':postulante_id' => $postulanteId,
                            ':periodo_id' => $periodoId,
                            ':promedio' => $resultadoPromedio['promedio'],
                            ':observacion' => 'Reprobado por promedio menor a 60'
                        ]);
                        
                        // Actualizar estado del postulante
                        $sqlUpd = "UPDATE postulante SET estado_postulacion = 'Reprobado' WHERE id = :id";
                        $stmtUpd = $db->prepare($sqlUpd);
                        $stmtUpd->execute([':id' => $postulanteId]);
                        
                        $mensajes[] = "Postulante ID $postulanteId: Reprobado (Promedio: {$resultadoPromedio['promedio']})";
                    }
                }
            }
            
            if (!empty($mensajes)) {
                set_flash('success', 'Notas registradas correctamente. ' . implode(' ', $mensajes));
            } else {
                set_flash('success', 'Notas registradas correctamente');
            }
        } else {
            set_flash('error', 'Error: ' . $resultado['error']);
        }

        redirect('/notas/edit?grupo_id=' . $grupoId);
    }
}