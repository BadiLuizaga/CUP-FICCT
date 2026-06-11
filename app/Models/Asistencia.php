<?php

namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/Inscripcion.php';
require_once __DIR__ . '/GrupoAcademico.php';

class Asistencia
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    /**
     * Obtiene los grupos asignados al docente logueado
     * (basado en grupo_materia -> docente -> usuario)
     */
    public static function obtenerGruposPorDocente($usuarioId)
    {
        $sql = "
            SELECT DISTINCT
                g.id AS grupo_id,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre,
                m.id AS materia_id,
                m.codigo AS materia_codigo,
                m.nombre AS materia_nombre,
                pa.codigo AS periodo_codigo,
                pa.gestion,
                pa.semestre
            FROM usuario u
            INNER JOIN persona p ON p.id = u.persona_id
            INNER JOIN docente d ON d.persona_id = p.id
            INNER JOIN grupo_materia gm ON gm.docente_id = d.id
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN periodo_academico pa ON pa.id = g.periodo_id
            WHERE u.id = :usuario_id
            ORDER BY g.codigo ASC, m.nombre ASC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':usuario_id', (int)$usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene los postulantes inscritos a un grupo específico
     */
    public static function obtenerPostulantesPorGrupo($grupoId)
    {
        $sql = "
            SELECT
                i.id AS inscripcion_id,
                i.codigo AS codigo_inscripcion,
                i.postulante_id,
                i.grupo_id,
                po.codigo AS codigo_postulante,
                po.estado_postulacion,
                pe.ci,
                pe.nombres,
                pe.apellidos,
                pe.email,
                pe.telefono,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre
            FROM inscripcion i
            INNER JOIN postulante po ON po.id = i.postulante_id
            INNER JOIN persona pe ON pe.id = po.persona_id
            INNER JOIN grupo g ON g.id = i.grupo_id
            WHERE i.grupo_id = :grupo_id
              AND i.estado = 'Activa'
            ORDER BY pe.apellidos ASC, pe.nombres ASC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':grupo_id', (int)$grupoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Verifica si un docente tiene acceso a un grupo específico
     */
    public static function docentePuedeAccederAGrupo($usuarioId, $grupoId)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM usuario u
            INNER JOIN persona p ON p.id = u.persona_id
            INNER JOIN docente d ON d.persona_id = p.id
            INNER JOIN grupo_materia gm ON gm.docente_id = d.id
            WHERE u.id = :usuario_id
              AND gm.grupo_id = :grupo_id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':usuario_id', (int)$usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':grupo_id', (int)$grupoId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return (int)($resultado['total'] ?? 0) > 0;
    }

    /**
     * Obtiene la asistencia registrada para una fecha, grupo y materia específicos
     */
    public static function obtenerAsistenciaPorFecha($grupoId, $fecha, $materiaId = null)
    {
        $sql = "
            SELECT
                a.id,
                a.postulante_id,
                a.grupo_materia_id,
                a.fecha,
                a.estado,
                a.observacion,
                pe.ci,
                pe.nombres,
                pe.apellidos,
                po.codigo AS codigo_postulante
            FROM asistencia a
            INNER JOIN postulante po ON po.id = a.postulante_id
            INNER JOIN persona pe ON pe.id = po.persona_id
            WHERE a.grupo_materia_id IN (
                SELECT id FROM grupo_materia WHERE grupo_id = :grupo_id
                " . ($materiaId ? " AND materia_id = :materia_id" : "") . "
            )
            AND a.fecha = :fecha
            ORDER BY pe.apellidos ASC, pe.nombres ASC
        ";

        $params = [
            ':grupo_id' => (int)$grupoId,
            ':fecha' => $fecha
        ];

        if ($materiaId) {
            $params[':materia_id'] = (int)$materiaId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene el grupo_materia_id para un grupo y materia específicos
     */
    public static function obtenerGrupoMateriaId($grupoId, $materiaId)
    {
        $sql = "
            SELECT id
            FROM grupo_materia
            WHERE grupo_id = :grupo_id AND materia_id = :materia_id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':grupo_id', (int)$grupoId, PDO::PARAM_INT);
        $stmt->bindValue(':materia_id', (int)$materiaId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado ? (int)$resultado['id'] : null;
    }

    /**
     * Obtiene la materia asociada a un grupo_materia
     */
    public static function obtenerMateriaPorGrupoMateria($grupoMateriaId)
    {
        $sql = "
            SELECT m.id, m.codigo, m.nombre, m.sigla
            FROM grupo_materia gm
            INNER JOIN materia m ON m.id = gm.materia_id
            WHERE gm.id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$grupoMateriaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Guarda o actualiza la asistencia de un postulante
     */
    public static function guardarAsistencia($postulanteId, $grupoMateriaId, $fecha, $estado, $observacion = null)
    {
        $estadosValidos = ['Presente', 'Ausente', 'Justificado', 'Tarde', 'Licencia'];
        if (!in_array($estado, $estadosValidos, true)) {
            throw new Exception('El estado de asistencia no es válido.');
        }

        $db = self::db();

        // Verificar si ya existe registro para este postulante, grupo_materia y fecha
        $sqlCheck = "
            SELECT id FROM asistencia
            WHERE postulante_id = :postulante_id
              AND grupo_materia_id = :grupo_materia_id
              AND fecha = :fecha
            LIMIT 1
        ";

        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute([
            ':postulante_id' => (int)$postulanteId,
            ':grupo_materia_id' => (int)$grupoMateriaId,
            ':fecha' => $fecha
        ]);

        $existente = $stmtCheck->fetch();

        if ($existente) {
            // Actualizar existente
            $sql = "
                UPDATE asistencia
                SET estado = :estado,
                    observacion = :observacion
                WHERE id = :id
            ";
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':estado' => $estado,
                ':observacion' => $observacion ?: null,
                ':id' => (int)$existente['id']
            ]);
        } else {
            // Insertar nuevo
            $sql = "
                INSERT INTO asistencia (
                    postulante_id,
                    grupo_materia_id,
                    fecha,
                    estado,
                    observacion
                ) VALUES (
                    :postulante_id,
                    :grupo_materia_id,
                    :fecha,
                    :estado,
                    :observacion
                )
            ";
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':postulante_id' => (int)$postulanteId,
                ':grupo_materia_id' => (int)$grupoMateriaId,
                ':fecha' => $fecha,
                ':estado' => $estado,
                ':observacion' => $observacion ?: null
            ]);
        }
    }

    /**
     * Guarda múltiples asistencias en lote
     */
    public static function guardarAsistenciasLote($asistencias, $grupoMateriaId, $fecha)
    {
        $db = self::db();
        $errores = [];

        try {
            $db->beginTransaction();

            foreach ($asistencias as $asistencia) {
                $postulanteId = (int)($asistencia['postulante_id'] ?? 0);
                $estado = trim($asistencia['estado'] ?? 'Ausente');
                $observacion = trim($asistencia['observacion'] ?? '');

                if ($postulanteId <= 0) {
                    continue;
                }

                self::guardarAsistencia($postulanteId, $grupoMateriaId, $fecha, $estado, $observacion ?: null);
            }

            $db->commit();
            return ['success' => true, 'errores' => []];
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return ['success' => false, 'errores' => [$e->getMessage()]];
        }
    }

    /**
     * Obtiene resumen de asistencia para un grupo en un rango de fechas
     */
    public static function obtenerResumenAsistenciaGrupo($grupoId, $fechaInicio, $fechaFin)
    {
        $sql = "
            SELECT
                a.estado,
                COUNT(*) AS total
            FROM asistencia a
            WHERE a.grupo_materia_id IN (
                SELECT id FROM grupo_materia WHERE grupo_id = :grupo_id
            )
            AND a.fecha BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY a.estado
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->execute([
            ':grupo_id' => (int)$grupoId,
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);

        $resumen = [];
        foreach ($stmt->fetchAll() as $row) {
            $resumen[$row['estado']] = (int)$row['total'];
        }

        return $resumen;
    }

    /**
     * Estados válidos para asistencia
     */
    public static function estadosValidos()
    {
        return [
            'Presente' => 'Presente',
            'Ausente' => 'Ausente',
            'Justificado' => 'Justificado',
            'Tarde' => 'Tarde',
            'Licencia' => 'Licencia'
        ];
    }
}