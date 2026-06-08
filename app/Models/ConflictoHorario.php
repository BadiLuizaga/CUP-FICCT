<?php

namespace App\Models;

use PDO;

require_once __DIR__ . '/Conexion.php';

class ConflictoHorario
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    public static function obtenerIndicadores()
    {
        $totalPlanificaciones = self::contarPlanificaciones();
        $conflictosAula = count(self::obtenerConflictosAulaHorario());
        $conflictosDocente = count(self::obtenerConflictosDocenteHorario());
        $conflictosGrupoHorario = count(self::obtenerConflictosGrupoHorario());
        $conflictosGrupoMateria = count(self::obtenerConflictosGrupoMateria());

        $totalConflictos = $conflictosAula + $conflictosDocente + $conflictosGrupoHorario + $conflictosGrupoMateria;

        return [
            'total_planificaciones' => $totalPlanificaciones,
            'conflictos_aula_horario' => $conflictosAula,
            'conflictos_docente_horario' => $conflictosDocente,
            'conflictos_grupo_horario' => $conflictosGrupoHorario,
            'conflictos_grupo_materia' => $conflictosGrupoMateria,
            'total_conflictos' => $totalConflictos,
            'estado_general' => $totalConflictos > 0 ? 'Con conflictos' : 'Sin conflictos',
        ];
    }

    public static function obtenerConflictosAulaHorario()
    {
        $sql = "
            SELECT
                gm.aula_id,
                gm.horario_id,
                a.codigo AS aula_codigo,
                a.bloque,
                a.numero,
                a.capacidad AS aula_capacidad,
                h.codigo AS horario_codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.turno,
                h.modalidad,
                COUNT(gm.id) AS cantidad_conflictos,
                string_agg(gm.id::text, ', ' ORDER BY gm.id) AS planificacion_ids,
                string_agg(
                    'ID ' || gm.id::text || ': ' ||
                    COALESCE(g.codigo, '-') || ' / ' ||
                    COALESCE(m.nombre, '-') || ' / Docente: ' ||
                    COALESCE(p.nombres, '') || ' ' || COALESCE(p.apellidos, ''),
                    ' | ' ORDER BY gm.id
                ) AS detalle_asignaciones
            FROM grupo_materia gm
            INNER JOIN aula a ON a.id = gm.aula_id
            INNER JOIN horario h ON h.id = gm.horario_id
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN docente d ON d.id = gm.docente_id
            INNER JOIN persona p ON p.id = d.persona_id
            GROUP BY
                gm.aula_id,
                gm.horario_id,
                a.codigo,
                a.bloque,
                a.numero,
                a.capacidad,
                h.codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.turno,
                h.modalidad
            HAVING COUNT(gm.id) > 1
            ORDER BY h.dia ASC, h.hora_inicio ASC, a.codigo ASC
        ";

        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function obtenerConflictosDocenteHorario()
    {
        $sql = "
            SELECT
                gm.docente_id,
                gm.horario_id,
                d.codigo AS docente_codigo,
                p.ci AS docente_ci,
                p.nombres AS docente_nombres,
                p.apellidos AS docente_apellidos,
                h.codigo AS horario_codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.turno,
                h.modalidad,
                COUNT(gm.id) AS cantidad_conflictos,
                string_agg(gm.id::text, ', ' ORDER BY gm.id) AS planificacion_ids,
                string_agg(
                    'ID ' || gm.id::text || ': Grupo ' ||
                    COALESCE(g.codigo, '-') || ' / ' ||
                    COALESCE(m.nombre, '-') || ' / Aula ' ||
                    COALESCE(a.codigo, '-'),
                    ' | ' ORDER BY gm.id
                ) AS detalle_asignaciones
            FROM grupo_materia gm
            INNER JOIN docente d ON d.id = gm.docente_id
            INNER JOIN persona p ON p.id = d.persona_id
            INNER JOIN horario h ON h.id = gm.horario_id
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN aula a ON a.id = gm.aula_id
            GROUP BY
                gm.docente_id,
                gm.horario_id,
                d.codigo,
                p.ci,
                p.nombres,
                p.apellidos,
                h.codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.turno,
                h.modalidad
            HAVING COUNT(gm.id) > 1
            ORDER BY p.apellidos ASC, p.nombres ASC, h.dia ASC, h.hora_inicio ASC
        ";

        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function obtenerConflictosGrupoHorario()
    {
        $sql = "
            SELECT
                gm.grupo_id,
                gm.horario_id,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre,
                g.capacidad AS grupo_capacidad,
                g.estado AS grupo_estado,
                h.codigo AS horario_codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.turno,
                h.modalidad,
                COUNT(gm.id) AS cantidad_conflictos,
                string_agg(gm.id::text, ', ' ORDER BY gm.id) AS planificacion_ids,
                string_agg(
                    'ID ' || gm.id::text || ': ' ||
                    COALESCE(m.nombre, '-') || ' / Docente: ' ||
                    COALESCE(p.nombres, '') || ' ' || COALESCE(p.apellidos, '') ||
                    ' / Aula ' || COALESCE(a.codigo, '-'),
                    ' | ' ORDER BY gm.id
                ) AS detalle_asignaciones
            FROM grupo_materia gm
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN horario h ON h.id = gm.horario_id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN docente d ON d.id = gm.docente_id
            INNER JOIN persona p ON p.id = d.persona_id
            INNER JOIN aula a ON a.id = gm.aula_id
            GROUP BY
                gm.grupo_id,
                gm.horario_id,
                g.codigo,
                g.nombre,
                g.capacidad,
                g.estado,
                h.codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.turno,
                h.modalidad
            HAVING COUNT(gm.id) > 1
            ORDER BY g.codigo ASC, h.dia ASC, h.hora_inicio ASC
        ";

        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function obtenerConflictosGrupoMateria()
    {
        $sql = "
            SELECT
                gm.grupo_id,
                gm.materia_id,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre,
                m.codigo AS materia_codigo,
                m.nombre AS materia_nombre,
                m.sigla AS materia_sigla,
                COUNT(gm.id) AS cantidad_conflictos,
                string_agg(gm.id::text, ', ' ORDER BY gm.id) AS planificacion_ids,
                string_agg(
                    'ID ' || gm.id::text || ': Horario ' ||
                    COALESCE(h.codigo, '-') || ' ' ||
                    COALESCE(h.dia, '-') || ' ' ||
                    COALESCE(h.hora_inicio::text, '-') || '-' || COALESCE(h.hora_fin::text, '-') ||
                    ' / Docente: ' || COALESCE(p.nombres, '') || ' ' || COALESCE(p.apellidos, '') ||
                    ' / Aula ' || COALESCE(a.codigo, '-'),
                    ' | ' ORDER BY gm.id
                ) AS detalle_asignaciones
            FROM grupo_materia gm
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN horario h ON h.id = gm.horario_id
            INNER JOIN docente d ON d.id = gm.docente_id
            INNER JOIN persona p ON p.id = d.persona_id
            INNER JOIN aula a ON a.id = gm.aula_id
            GROUP BY
                gm.grupo_id,
                gm.materia_id,
                g.codigo,
                g.nombre,
                m.codigo,
                m.nombre,
                m.sigla
            HAVING COUNT(gm.id) > 1
            ORDER BY g.codigo ASC, m.nombre ASC
        ";

        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function obtenerPlanificacionesConflictivas()
    {
        $sql = "
            SELECT DISTINCT
                gm.id,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre,
                m.codigo AS materia_codigo,
                m.nombre AS materia_nombre,
                d.codigo AS docente_codigo,
                p.ci AS docente_ci,
                p.nombres AS docente_nombres,
                p.apellidos AS docente_apellidos,
                h.codigo AS horario_codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.turno,
                h.modalidad,
                a.codigo AS aula_codigo,
                a.bloque,
                a.numero
            FROM grupo_materia gm
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN docente d ON d.id = gm.docente_id
            INNER JOIN persona p ON p.id = d.persona_id
            INNER JOIN horario h ON h.id = gm.horario_id
            INNER JOIN aula a ON a.id = gm.aula_id
            WHERE gm.id IN (
                SELECT gm2.id
                FROM grupo_materia gm2
                INNER JOIN (
                    SELECT aula_id, horario_id
                    FROM grupo_materia
                    GROUP BY aula_id, horario_id
                    HAVING COUNT(id) > 1
                ) x ON x.aula_id = gm2.aula_id AND x.horario_id = gm2.horario_id

                UNION

                SELECT gm2.id
                FROM grupo_materia gm2
                INNER JOIN (
                    SELECT docente_id, horario_id
                    FROM grupo_materia
                    GROUP BY docente_id, horario_id
                    HAVING COUNT(id) > 1
                ) x ON x.docente_id = gm2.docente_id AND x.horario_id = gm2.horario_id

                UNION

                SELECT gm2.id
                FROM grupo_materia gm2
                INNER JOIN (
                    SELECT grupo_id, horario_id
                    FROM grupo_materia
                    GROUP BY grupo_id, horario_id
                    HAVING COUNT(id) > 1
                ) x ON x.grupo_id = gm2.grupo_id AND x.horario_id = gm2.horario_id

                UNION

                SELECT gm2.id
                FROM grupo_materia gm2
                INNER JOIN (
                    SELECT grupo_id, materia_id
                    FROM grupo_materia
                    GROUP BY grupo_id, materia_id
                    HAVING COUNT(id) > 1
                ) x ON x.grupo_id = gm2.grupo_id AND x.materia_id = gm2.materia_id
            )
            ORDER BY g.codigo ASC, h.dia ASC, h.hora_inicio ASC, gm.id ASC
        ";

        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    private static function contarPlanificaciones()
    {
        $stmt = self::db()->query("SELECT COUNT(*) FROM grupo_materia");
        return (int)$stmt->fetchColumn();
    }
}