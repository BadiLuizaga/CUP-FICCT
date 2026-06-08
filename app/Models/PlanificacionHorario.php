<?php

namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/Conexion.php';

class PlanificacionHorario
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    public static function obtenerTodos()
    {
        $sql = "
            SELECT
                gm.id,

                g.id AS grupo_id,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre,
                g.capacidad AS grupo_capacidad,
                g.cantidad_estudiantes,
                g.estado AS grupo_estado,

                pa.codigo AS periodo_codigo,
                pa.gestion,
                pa.semestre,

                m.id AS materia_id,
                m.codigo AS materia_codigo,
                m.nombre AS materia_nombre,
                m.sigla AS materia_sigla,
                m.area AS materia_area,

                d.id AS docente_id,
                d.codigo AS docente_codigo,
                d.estado_contrato,
                p.ci AS docente_ci,
                p.nombres AS docente_nombres,
                p.apellidos AS docente_apellidos,

                h.id AS horario_id,
                h.codigo AS horario_codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.modalidad,
                h.turno,

                a.id AS aula_id,
                a.codigo AS aula_codigo,
                a.bloque,
                a.numero,
                a.capacidad AS aula_capacidad,
                a.tipo AS aula_tipo

            FROM grupo_materia gm
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN docente d ON d.id = gm.docente_id
            INNER JOIN persona p ON p.id = d.persona_id
            INNER JOIN horario h ON h.id = gm.horario_id
            INNER JOIN aula a ON a.id = gm.aula_id
            LEFT JOIN periodo_academico pa ON pa.id = g.periodo_id
            ORDER BY
                CASE
                    WHEN g.codigo LIKE 'M%' THEN 1
                    WHEN g.codigo LIKE 'T%' THEN 2
                    WHEN g.codigo LIKE 'N%' THEN 3
                    ELSE 4
                END,
                g.codigo ASC,
                h.hora_inicio ASC,
                m.nombre ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function buscarPorId($id)
    {
        $sql = "
            SELECT
                gm.id,

                g.id AS grupo_id,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre,
                g.capacidad AS grupo_capacidad,
                g.cantidad_estudiantes,
                g.estado AS grupo_estado,

                pa.codigo AS periodo_codigo,
                pa.gestion,
                pa.semestre,

                m.id AS materia_id,
                m.codigo AS materia_codigo,
                m.nombre AS materia_nombre,
                m.sigla AS materia_sigla,
                m.area AS materia_area,

                d.id AS docente_id,
                d.codigo AS docente_codigo,
                d.estado_contrato,
                p.ci AS docente_ci,
                p.nombres AS docente_nombres,
                p.apellidos AS docente_apellidos,
                p.email AS docente_email,
                p.telefono AS docente_telefono,

                h.id AS horario_id,
                h.codigo AS horario_codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.modalidad,
                h.turno,

                a.id AS aula_id,
                a.codigo AS aula_codigo,
                a.bloque,
                a.numero,
                a.capacidad AS aula_capacidad,
                a.tipo AS aula_tipo

            FROM grupo_materia gm
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN materia m ON m.id = gm.materia_id
            INNER JOIN docente d ON d.id = gm.docente_id
            INNER JOIN persona p ON p.id = d.persona_id
            INNER JOIN horario h ON h.id = gm.horario_id
            INNER JOIN aula a ON a.id = gm.aula_id
            LEFT JOIN periodo_academico pa ON pa.id = g.periodo_id
            WHERE gm.id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $planificacion = $stmt->fetch();

        return $planificacion ?: null;
    }

    public static function obtenerIndicadores()
    {
        $sql = "
            SELECT
                COUNT(gm.id) AS total_planificaciones,
                COUNT(DISTINCT gm.grupo_id) AS total_grupos_planificados,
                COUNT(DISTINCT gm.docente_id) AS total_docentes_asignados,
                COUNT(DISTINCT gm.aula_id) AS total_aulas_usadas,
                COUNT(DISTINCT gm.horario_id) AS total_horarios_usados
            FROM grupo_materia gm
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetch();
    }

    public static function obtenerCatalogos()
    {
        return [
            'grupos' => self::obtenerGrupos(),
            'materias' => self::obtenerMaterias(),
            'docentes' => self::obtenerDocentes(),
            'horarios' => self::obtenerHorarios(),
            'aulas' => self::obtenerAulas(),
        ];
    }

    public static function obtenerGrupos()
    {
        $sql = "
            SELECT
                g.id,
                g.codigo,
                g.nombre,
                g.capacidad,
                g.cantidad_estudiantes,
                g.estado,
                pa.codigo AS periodo_codigo,
                pa.gestion,
                pa.semestre
            FROM grupo g
            LEFT JOIN periodo_academico pa ON pa.id = g.periodo_id
            ORDER BY
                CASE
                    WHEN g.codigo LIKE 'M%' THEN 1
                    WHEN g.codigo LIKE 'T%' THEN 2
                    WHEN g.codigo LIKE 'N%' THEN 3
                    ELSE 4
                END,
                g.codigo ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function obtenerMaterias()
    {
        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                sigla,
                area
            FROM materia
            ORDER BY nombre ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function obtenerDocentes()
    {
        $sql = "
            SELECT
                d.id,
                d.codigo,
                d.estado_contrato,
                p.ci,
                p.nombres,
                p.apellidos
            FROM docente d
            INNER JOIN persona p ON p.id = d.persona_id
            ORDER BY p.apellidos ASC, p.nombres ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function obtenerHorarios()
    {
        $sql = "
            SELECT
                id,
                codigo,
                dia,
                hora_inicio,
                hora_fin,
                modalidad,
                turno
            FROM horario
            ORDER BY
                CASE
                    WHEN LOWER(turno) = LOWER('Mañana') THEN 1
                    WHEN LOWER(turno) = LOWER('Tarde') THEN 2
                    WHEN LOWER(turno) = LOWER('Noche') THEN 3
                    ELSE 4
                END,
                hora_inicio ASC,
                codigo ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function obtenerAulas()
    {
        $sql = "
            SELECT
                id,
                codigo,
                bloque,
                numero,
                capacidad,
                tipo
            FROM aula
            ORDER BY codigo ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function crear($datos)
    {
        self::validarDatos($datos);

        $conflictos = self::obtenerConflictos($datos);

        if (!empty($conflictos)) {
            throw new Exception(implode(' ', $conflictos));
        }

        if (self::existeGrupoMateria($datos['grupo_id'], $datos['materia_id'])) {
            throw new Exception('El grupo seleccionado ya tiene esa materia asignada.');
        }

        $sql = "
            INSERT INTO grupo_materia (
                grupo_id,
                materia_id,
                docente_id,
                horario_id,
                aula_id
            ) VALUES (
                :grupo_id,
                :materia_id,
                :docente_id,
                :horario_id,
                :aula_id
            )
        ";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            ':grupo_id' => (int)$datos['grupo_id'],
            ':materia_id' => (int)$datos['materia_id'],
            ':docente_id' => (int)$datos['docente_id'],
            ':horario_id' => (int)$datos['horario_id'],
            ':aula_id' => (int)$datos['aula_id'],
        ]);
    }

    public static function actualizar($id, $datos)
    {
        $id = (int)$id;

        if ($id <= 0) {
            throw new Exception('Planificación no válida.');
        }

        if (!self::buscarPorId($id)) {
            throw new Exception('La planificación no existe.');
        }

        self::validarDatos($datos);

        $conflictos = self::obtenerConflictos($datos, $id);

        if (!empty($conflictos)) {
            throw new Exception(implode(' ', $conflictos));
        }

        if (self::existeGrupoMateria($datos['grupo_id'], $datos['materia_id'], $id)) {
            throw new Exception('El grupo seleccionado ya tiene esa materia asignada.');
        }

        $sql = "
            UPDATE grupo_materia
            SET
                grupo_id = :grupo_id,
                materia_id = :materia_id,
                docente_id = :docente_id,
                horario_id = :horario_id,
                aula_id = :aula_id
            WHERE id = :id
        ";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            ':grupo_id' => (int)$datos['grupo_id'],
            ':materia_id' => (int)$datos['materia_id'],
            ':docente_id' => (int)$datos['docente_id'],
            ':horario_id' => (int)$datos['horario_id'],
            ':aula_id' => (int)$datos['aula_id'],
            ':id' => $id,
        ]);
    }

    public static function obtenerConflictos($datos, $exceptoId = null)
    {
        $conflictos = [];

        if (self::existeCruceAulaHorario($datos['aula_id'], $datos['horario_id'], $exceptoId)) {
            $conflictos[] = 'Conflicto: el aula seleccionada ya está ocupada en ese horario.';
        }

        if (self::existeCruceDocenteHorario($datos['docente_id'], $datos['horario_id'], $exceptoId)) {
            $conflictos[] = 'Conflicto: el docente seleccionado ya tiene una materia asignada en ese horario.';
        }

        if (self::existeCruceGrupoHorario($datos['grupo_id'], $datos['horario_id'], $exceptoId)) {
            $conflictos[] = 'Conflicto: el grupo seleccionado ya tiene una materia en ese horario.';
        }

        return $conflictos;
    }

    private static function validarDatos($datos)
    {
        self::validarExiste('grupo', $datos['grupo_id'] ?? 0, 'El grupo seleccionado no existe.');
        self::validarExiste('materia', $datos['materia_id'] ?? 0, 'La materia seleccionada no existe.');
        self::validarExiste('docente', $datos['docente_id'] ?? 0, 'El docente seleccionado no existe.');
        self::validarExiste('horario', $datos['horario_id'] ?? 0, 'El horario seleccionado no existe.');
        self::validarExiste('aula', $datos['aula_id'] ?? 0, 'El aula seleccionada no existe.');
    }

    private static function validarExiste($tabla, $id, $mensaje)
    {
        $id = (int)$id;

        if ($id <= 0) {
            throw new Exception($mensaje);
        }

        $tablasPermitidas = ['grupo', 'materia', 'docente', 'horario', 'aula'];

        if (!in_array($tabla, $tablasPermitidas, true)) {
            throw new Exception('Tabla no permitida para validación.');
        }

        $sql = "SELECT COUNT(*) FROM {$tabla} WHERE id = :id";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ((int)$stmt->fetchColumn() <= 0) {
            throw new Exception($mensaje);
        }
    }

    private static function existeGrupoMateria($grupoId, $materiaId, $exceptoId = null)
    {
        $sql = "
            SELECT COUNT(*)
            FROM grupo_materia
            WHERE grupo_id = :grupo_id
            AND materia_id = :materia_id
        ";

        $params = [
            ':grupo_id' => (int)$grupoId,
            ':materia_id' => (int)$materiaId,
        ];

        if ($exceptoId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    private static function existeCruceAulaHorario($aulaId, $horarioId, $exceptoId = null)
    {
        $sql = "
            SELECT COUNT(*)
            FROM grupo_materia
            WHERE aula_id = :aula_id
            AND horario_id = :horario_id
        ";

        $params = [
            ':aula_id' => (int)$aulaId,
            ':horario_id' => (int)$horarioId,
        ];

        if ($exceptoId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    private static function existeCruceDocenteHorario($docenteId, $horarioId, $exceptoId = null)
    {
        $sql = "
            SELECT COUNT(*)
            FROM grupo_materia
            WHERE docente_id = :docente_id
            AND horario_id = :horario_id
        ";

        $params = [
            ':docente_id' => (int)$docenteId,
            ':horario_id' => (int)$horarioId,
        ];

        if ($exceptoId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    private static function existeCruceGrupoHorario($grupoId, $horarioId, $exceptoId = null)
    {
        $sql = "
            SELECT COUNT(*)
            FROM grupo_materia
            WHERE grupo_id = :grupo_id
            AND horario_id = :horario_id
        ";

        $params = [
            ':grupo_id' => (int)$grupoId,
            ':horario_id' => (int)$horarioId,
        ];

        if ($exceptoId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }
}