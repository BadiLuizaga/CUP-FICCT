<?php

namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/Conexion.php';

class Nota
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    public static function obtenerExamenesPorGrupo($grupoId)
    {
        $sql = "
            SELECT e.id, e.nombre, e.ponderacion
            FROM examen e
            WHERE e.grupo_id = :grupo_id
            ORDER BY e.id ASC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->execute([':grupo_id' => $grupoId]);
        return $stmt->fetchAll();
    }

    public static function obtenerNotasPorGrupo($grupoId)
    {
        $sql = "
            SELECT 
                n.id AS nota_id,
                n.postulante_id,
                n.examen_id,
                n.nota AS valor
            FROM nota n
            WHERE n.examen_id IN (
                SELECT e.id 
                FROM examen e 
                WHERE e.grupo_id = :grupo_id
            )
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->execute([':grupo_id' => $grupoId]);
        return $stmt->fetchAll();
    }

    public static function guardarNotas($grupoId, $notas)
    {
        $db = self::db();
        try {
            $db->beginTransaction();

            foreach ($notas as $postulanteId => $examenes) {
                foreach ($examenes as $examenId => $valor) {
                    $valor = floatval($valor);
                    if ($valor < 0 || $valor > 100) {
                        throw new Exception("Nota inválida ($valor)");
                    }

                    // Verificar si ya existe
                    $sqlCheck = "
                        SELECT id FROM nota
                        WHERE postulante_id = :postulante_id
                        AND examen_id = :examen_id
                        LIMIT 1
                    ";
                    $stmtCheck = $db->prepare($sqlCheck);
                    $stmtCheck->execute([
                        ':postulante_id' => $postulanteId,
                        ':examen_id' => $examenId
                    ]);
                    $existente = $stmtCheck->fetch();

                    if ($existente) {
                        // Actualizar
                        $sqlUpd = "UPDATE nota SET nota = :valor WHERE id = :id";
                        $stmtUpd = $db->prepare($sqlUpd);
                        $stmtUpd->execute([':valor' => $valor, ':id' => $existente['id']]);
                    } else {
                        // Insertar nueva
                        $sqlIns = "
                            INSERT INTO nota (postulante_id, examen_id, nota)
                            VALUES (:postulante_id, :examen_id, :valor)
                        ";
                        $stmtIns = $db->prepare($sqlIns);
                        $stmtIns->execute([
                            ':postulante_id' => $postulanteId,
                            ':examen_id' => $examenId,
                            ':valor' => $valor
                        ]);
                    }
                }
            }

            $db->commit();
            return ['success' => true];
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public static function calcularPromedioFinalPostulante($postulanteId, $grupoId)
    {
        $db = self::db();
        
        // Obtener los 3 exámenes del grupo
        $sql = "
            SELECT e.id, e.ponderacion
            FROM examen e
            WHERE e.grupo_id = :grupo_id
            ORDER BY e.id ASC
            LIMIT 3
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':grupo_id' => $grupoId]);
        $examenes = $stmt->fetchAll();
        
        if (count($examenes) < 3) {
            return ['promedio' => 0, 'estado' => 'Incompleto'];
        }
        
        $ponderaciones = [30, 30, 40];
        $sumaPonderada = 0;
        $sumaPonderadores = 0;
        
        foreach ($examenes as $idx => $ex) {
            $sqlNota = "
                SELECT nota FROM nota
                WHERE postulante_id = :postulante_id
                AND examen_id = :examen_id
                LIMIT 1
            ";
            $stmtNota = $db->prepare($sqlNota);
            $stmtNota->execute([
                ':postulante_id' => $postulanteId,
                ':examen_id' => $ex['id']
            ]);
            $nota = $stmtNota->fetch();
            
            if ($nota) {
                $sumaPonderada += $nota['nota'] * $ponderaciones[$idx];
                $sumaPonderadores += $ponderaciones[$idx];
            }
        }
        
        if ($sumaPonderadores == 0) {
            return ['promedio' => 0, 'estado' => 'Sin notas'];
        }
        
        $promedio = round($sumaPonderada / $sumaPonderadores, 2);
        $estado = $promedio >= 60 ? 'Aprobado' : 'Reprobado';
        
        return [
            'promedio' => $promedio,
            'estado' => $estado
        ];
    }
}