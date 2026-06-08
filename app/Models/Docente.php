<?php

namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/Conexion.php';

class Docente
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    public static function obtenerTodos()
    {
        $sql = "
            SELECT
                d.id,
                d.codigo,
                d.persona_id,
                d.profesion,
                d.maestria,
                d.diplomado_educacion,
                d.experiencia,
                d.estado_contrato,
                p.ci,
                p.nombres,
                p.apellidos,
                p.fecha_nacimiento,
                p.sexo,
                p.email,
                p.telefono,
                p.direccion,
                COUNT(DISTINCT gm.id) AS total_asignaciones,
                COALESCE(string_agg(DISTINCT m.nombre, ', ' ORDER BY m.nombre), 'Sin materias') AS materias,
                COALESCE(string_agg(DISTINCT g.codigo, ', ' ORDER BY g.codigo), 'Sin grupos') AS grupos
            FROM docente d
            INNER JOIN persona p ON p.id = d.persona_id
            LEFT JOIN grupo_materia gm ON gm.docente_id = d.id
            LEFT JOIN materia m ON m.id = gm.materia_id
            LEFT JOIN grupo g ON g.id = gm.grupo_id
            GROUP BY
                d.id,
                d.codigo,
                d.persona_id,
                d.profesion,
                d.maestria,
                d.diplomado_educacion,
                d.experiencia,
                d.estado_contrato,
                p.ci,
                p.nombres,
                p.apellidos,
                p.fecha_nacimiento,
                p.sexo,
                p.email,
                p.telefono,
                p.direccion
            ORDER BY d.id ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function buscarPorId($id)
    {
        $sql = "
            SELECT
                d.id,
                d.codigo,
                d.persona_id,
                d.profesion,
                d.maestria,
                d.diplomado_educacion,
                d.experiencia,
                d.estado_contrato,
                p.codigo AS persona_codigo,
                p.ci,
                p.nombres,
                p.apellidos,
                p.fecha_nacimiento,
                p.sexo,
                p.email,
                p.telefono,
                p.direccion,
                p.created_at,
                p.updated_at
            FROM docente d
            INNER JOIN persona p ON p.id = d.persona_id
            WHERE d.id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $docente = $stmt->fetch();

        return $docente ?: null;
    }

    public static function obtenerAsignaciones($docenteId)
    {
        $sql = "
            SELECT
                gm.id,
                g.codigo AS grupo_codigo,
                g.nombre AS grupo_nombre,
                g.capacidad AS grupo_capacidad,
                g.estado AS grupo_estado,
                m.codigo AS materia_codigo,
                m.nombre AS materia_nombre,
                m.sigla AS materia_sigla,
                h.codigo AS horario_codigo,
                h.dia,
                h.hora_inicio,
                h.hora_fin,
                h.modalidad,
                h.turno,
                a.codigo AS aula_codigo,
                a.bloque,
                a.numero,
                a.capacidad AS aula_capacidad,
                a.tipo AS aula_tipo
            FROM grupo_materia gm
            INNER JOIN grupo g ON g.id = gm.grupo_id
            INNER JOIN materia m ON m.id = gm.materia_id
            LEFT JOIN horario h ON h.id = gm.horario_id
            LEFT JOIN aula a ON a.id = gm.aula_id
            WHERE gm.docente_id = :docente_id
            ORDER BY
                g.codigo ASC,
                m.nombre ASC,
                h.hora_inicio ASC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':docente_id', (int)$docenteId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function crear($datos)
    {
        $db = self::db();

        try {
            $db->beginTransaction();

            $codigoPersona = self::generarCodigo('persona', 'PERS');

            $sqlPersona = "
                INSERT INTO persona (
                    codigo,
                    ci,
                    nombres,
                    apellidos,
                    fecha_nacimiento,
                    sexo,
                    email,
                    telefono,
                    direccion,
                    created_at,
                    updated_at
                ) VALUES (
                    :codigo,
                    :ci,
                    :nombres,
                    :apellidos,
                    :fecha_nacimiento,
                    :sexo,
                    :email,
                    :telefono,
                    :direccion,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                )
                RETURNING id
            ";

            $stmtPersona = $db->prepare($sqlPersona);
            $stmtPersona->execute([
                ':codigo' => $codigoPersona,
                ':ci' => $datos['ci'],
                ':nombres' => $datos['nombres'],
                ':apellidos' => $datos['apellidos'],
                ':fecha_nacimiento' => self::nullSiVacio($datos['fecha_nacimiento'] ?? null),
                ':sexo' => self::nullSiVacio($datos['sexo'] ?? null),
                ':email' => self::nullSiVacio($datos['email'] ?? null),
                ':telefono' => self::nullSiVacio($datos['telefono'] ?? null),
                ':direccion' => self::nullSiVacio($datos['direccion'] ?? null),
            ]);

            $personaId = $stmtPersona->fetchColumn();

            $sqlDocente = "
                INSERT INTO docente (
                    codigo,
                    persona_id,
                    profesion,
                    maestria,
                    diplomado_educacion,
                    experiencia,
                    estado_contrato
                ) VALUES (
                    :codigo,
                    :persona_id,
                    :profesion,
                    :maestria,
                    :diplomado_educacion,
                    :experiencia,
                    :estado_contrato
                )
            ";

            $stmtDocente = $db->prepare($sqlDocente);
            $stmtDocente->bindValue(':codigo', $datos['codigo']);
            $stmtDocente->bindValue(':persona_id', (int)$personaId, PDO::PARAM_INT);
            $stmtDocente->bindValue(':profesion', self::nullSiVacio($datos['profesion'] ?? null));
            $stmtDocente->bindValue(':maestria', self::nullSiVacio($datos['maestria'] ?? null));
            $stmtDocente->bindValue(':diplomado_educacion', (bool)$datos['diplomado_educacion'], PDO::PARAM_BOOL);
            $stmtDocente->bindValue(':experiencia', self::nullSiVacio($datos['experiencia'] ?? null));
            $stmtDocente->bindValue(':estado_contrato', $datos['estado_contrato']);
            $stmtDocente->execute();

            $db->commit();

            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $e;
        }
    }

    public static function actualizar($id, $datos)
    {
        $db = self::db();
        $docente = self::buscarPorId($id);

        if (!$docente) {
            throw new Exception('El docente no existe.');
        }

        try {
            $db->beginTransaction();

            $sqlPersona = "
                UPDATE persona
                SET
                    ci = :ci,
                    nombres = :nombres,
                    apellidos = :apellidos,
                    fecha_nacimiento = :fecha_nacimiento,
                    sexo = :sexo,
                    email = :email,
                    telefono = :telefono,
                    direccion = :direccion,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :persona_id
            ";

            $stmtPersona = $db->prepare($sqlPersona);
            $stmtPersona->execute([
                ':ci' => $datos['ci'],
                ':nombres' => $datos['nombres'],
                ':apellidos' => $datos['apellidos'],
                ':fecha_nacimiento' => self::nullSiVacio($datos['fecha_nacimiento'] ?? null),
                ':sexo' => self::nullSiVacio($datos['sexo'] ?? null),
                ':email' => self::nullSiVacio($datos['email'] ?? null),
                ':telefono' => self::nullSiVacio($datos['telefono'] ?? null),
                ':direccion' => self::nullSiVacio($datos['direccion'] ?? null),
                ':persona_id' => (int)$docente['persona_id'],
            ]);

            $sqlDocente = "
                UPDATE docente
                SET
                    codigo = :codigo,
                    profesion = :profesion,
                    maestria = :maestria,
                    diplomado_educacion = :diplomado_educacion,
                    experiencia = :experiencia,
                    estado_contrato = :estado_contrato
                WHERE id = :id
            ";

            $stmtDocente = $db->prepare($sqlDocente);
            $stmtDocente->bindValue(':codigo', $datos['codigo']);
            $stmtDocente->bindValue(':profesion', self::nullSiVacio($datos['profesion'] ?? null));
            $stmtDocente->bindValue(':maestria', self::nullSiVacio($datos['maestria'] ?? null));
            $stmtDocente->bindValue(':diplomado_educacion', (bool)$datos['diplomado_educacion'], PDO::PARAM_BOOL);
            $stmtDocente->bindValue(':experiencia', self::nullSiVacio($datos['experiencia'] ?? null));
            $stmtDocente->bindValue(':estado_contrato', $datos['estado_contrato']);
            $stmtDocente->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmtDocente->execute();

            $db->commit();

            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $e;
        }
    }

    public static function existeCodigo($codigo, $exceptoId = null)
    {
        $sql = "SELECT COUNT(*) FROM docente WHERE LOWER(codigo) = LOWER(:codigo)";
        $params = [':codigo' => $codigo];

        if ($exceptoId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    public static function existeCI($ci, $exceptoPersonaId = null)
    {
        $sql = "SELECT COUNT(*) FROM persona WHERE ci = :ci";
        $params = [':ci' => $ci];

        if ($exceptoPersonaId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoPersonaId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    public static function existeEmail($email, $exceptoPersonaId = null)
    {
        $email = trim((string)($email ?? ''));

        if ($email === '') {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM persona WHERE LOWER(email) = LOWER(:email)";
        $params = [':email' => $email];

        if ($exceptoPersonaId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoPersonaId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    public static function estadosContratoValidos()
    {
        return [
            'Activo',
            'Inactivo',
            'Suspendido',
            'Retirado',
        ];
    }

    private static function generarCodigo($tabla, $prefijo)
    {
        $stmt = self::db()->query("SELECT COALESCE(MAX(id), 0) + 1 FROM {$tabla}");
        $numero = (int)$stmt->fetchColumn();

        return $prefijo . '-' . str_pad((string)$numero, 3, '0', STR_PAD_LEFT);
    }

    private static function nullSiVacio($valor)
    {
        $valor = trim((string)($valor ?? ''));

        return $valor === '' ? null : $valor;
    }
}