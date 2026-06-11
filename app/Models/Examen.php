<?php

namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/Conexion.php';

class Examen
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    // Obtener exámenes por grupo (desde la tabla `examen` con grupo_id)
    public static function obtenerExamenesPorGrupo($grupoId)
    {
        $sql = "SELECT * FROM examen WHERE grupo_id = :grupo_id ORDER BY id ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':grupo_id' => $grupoId]);
        return $stmt->fetchAll();
    }

    // Obtener preguntas de un examen
    public static function obtenerPreguntas($examenId)
    {
        $sql = "SELECT * FROM pregunta WHERE examen_id = :examen_id ORDER BY id ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':examen_id' => $examenId]);
        return $stmt->fetchAll();
    }

    // Guardar respuestas de un postulante
    public static function guardarRespuestas($postulanteId, $examenId, $respuestas)
    {
        $db = self::db();
        $db->beginTransaction();
        try {
            foreach ($respuestas as $preguntaId => $opcionSeleccionada) {
                $sql = "
                    INSERT INTO examen_respuesta (postulante_id, examen_id, pregunta_id, opcion_seleccionada)
                    VALUES (:postulante_id, :examen_id, :pregunta_id, :opcion_seleccionada)
                    ON CONFLICT (postulante_id, examen_id, pregunta_id) 
                    DO UPDATE SET opcion_seleccionada = EXCLUDED.opcion_seleccionada
                ";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':postulante_id' => $postulanteId,
                    ':examen_id' => $examenId,
                    ':pregunta_id' => $preguntaId,
                    ':opcion_seleccionada' => $opcionSeleccionada
                ]);
            }
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    // Calcular nota por materia
    public static function calcularNotaPorMateria($postulanteId, $examenId, $materiaId)
    {
        $sql = "
            SELECT COUNT(*) AS total,
                   SUM(CASE WHEN er.opcion_seleccionada = p.correcto THEN 1 ELSE 0 END) AS correctas
            FROM pregunta p
            INNER JOIN examen_respuesta er ON er.pregunta_id = p.id
            WHERE er.postulante_id = :postulante_id
              AND er.examen_id = :examen_id
              AND p.materia_id = :materia_id
        ";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([
            ':postulante_id' => $postulanteId,
            ':examen_id' => $examenId,
            ':materia_id' => $materiaId
        ]);
        $row = $stmt->fetch();
        if (!$row || $row['total'] == 0) {
            return 0;
        }
        return round(($row['correctas'] / $row['total']) * 100, 2);
    }

    // Calcular promedio general del postulante en un examen (todas sus materias)
    public static function calcularPromedioExamen($postulanteId, $examenId)
    {
        $sql = "SELECT DISTINCT materia_id FROM pregunta WHERE examen_id = :examen_id";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':examen_id' => $examenId]);
        $materias = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $suma = 0;
        $cont = 0;
        foreach ($materias as $materiaId) {
            $suma += self::calcularNotaPorMateria($postulanteId, $examenId, $materiaId);
            $cont++;
        }
        return $cont > 0 ? round($suma / $cont, 2) : 0;
    }

    // Calcular promedio final (3 exámenes) y actualizar postulante
    public static function calcularPromedioFinalPostulante($postulanteId)
    {
        $db = self::db();

        // Obtener los 3 exámenes del grupo al que pertenece el postulante
        $sql = "
            SELECT e.id, e.nombre, e.ponderacion
            FROM examen e
            INNER JOIN grupo g ON g.id = e.grupo_id
            INNER JOIN inscripcion i ON i.grupo_id = g.id
            WHERE i.postulante_id = :postulante_id
            ORDER BY e.id ASC
            LIMIT 3
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':postulante_id' => $postulanteId]);
        $examenes = $stmt->fetchAll();

        if (count($examenes) < 3) {
            return ['promedio' => 0, 'estado' => 'Sin completar'];
        }

        $ponderaciones = [0.30, 0.30, 0.40];
        $promedioFinal = 0;

        foreach ($examenes as $idx => $ex) {
            $promedioExamen = self::calcularPromedioExamen($postulanteId, $ex['id']);
            $promedioFinal += $promedioExamen * $ponderaciones[$idx];
        }

        $promedioFinal = round($promedioFinal, 2);
        $estado = $promedioFinal >= 60 ? 'Aprobado' : 'Reprobado';

        // Actualizar estado del postulante
        $sqlUpd = "UPDATE postulante SET estado_postulacion = :estado WHERE id = :postulante_id";
        $stmtUpd = $db->prepare($sqlUpd);
        $stmtUpd->execute([
            ':estado' => $estado,
            ':postulante_id' => $postulanteId
        ]);

        return ['promedio' => $promedioFinal, 'estado' => $estado];
    }
}