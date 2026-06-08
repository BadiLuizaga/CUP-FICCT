<?php

namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/PeriodoAcademico.php';

class Postulante
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    public static function obtenerTodos($periodoId = null)
    {
        $where = '';
        $params = [];

        if (!empty($periodoId)) {
            $where = 'WHERE po.periodo_id = :periodo_id';
            $params[':periodo_id'] = (int)$periodoId;
        }

        $sql = "
            SELECT
                po.id,
                po.codigo,
                po.periodo_id,
                po.colegio,
                po.ciudad,
                po.titulo_bachiller,
                po.fecha_registro,
                po.estado_postulacion,
                pe.ci,
                pe.nombres,
                pe.apellidos,
                pe.email,
                pe.telefono,
                c1.nombre AS carrera_principal,
                c2.nombre AS carrera_secundaria,
                pa.codigo AS periodo_codigo,
                pa.gestion AS periodo_gestion,
                pa.semestre AS periodo_semestre,
                COUNT(d.id) AS total_requisitos,
                COALESCE(SUM(CASE WHEN d.estado_validacion = 'Aceptado' THEN 1 ELSE 0 END), 0) AS requisitos_aceptados
            FROM postulante po
            INNER JOIN persona pe ON pe.id = po.persona_id
            LEFT JOIN carrera c1 ON c1.id = po.carrera_principal_id
            LEFT JOIN carrera c2 ON c2.id = po.carrera_secundaria_id
            LEFT JOIN periodo_academico pa ON pa.id = po.periodo_id
            LEFT JOIN documento d ON d.postulante_id = po.id
            {$where}
            GROUP BY
                po.id,
                po.codigo,
                po.periodo_id,
                po.colegio,
                po.ciudad,
                po.titulo_bachiller,
                po.fecha_registro,
                po.estado_postulacion,
                pe.ci,
                pe.nombres,
                pe.apellidos,
                pe.email,
                pe.telefono,
                c1.nombre,
                c2.nombre,
                pa.codigo,
                pa.gestion,
                pa.semestre
            ORDER BY po.id ASC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function buscarPorId($id)
    {
        $sql = "
            SELECT
                po.id,
                po.codigo,
                po.persona_id,
                po.periodo_id,
                po.colegio,
                po.ciudad,
                po.titulo_bachiller,
                po.carrera_principal_id,
                po.carrera_secundaria_id,
                po.fecha_registro,
                po.estado_postulacion,
                po.created_at,
                po.updated_at,
                pe.codigo AS persona_codigo,
                pe.ci,
                pe.nombres,
                pe.apellidos,
                pe.fecha_nacimiento,
                pe.sexo,
                pe.email,
                pe.telefono,
                pe.direccion,
                c1.nombre AS carrera_principal,
                c2.nombre AS carrera_secundaria,
                pa.codigo AS periodo_codigo,
                pa.gestion AS periodo_gestion,
                pa.semestre AS periodo_semestre,
                pa.fecha_inicio AS periodo_fecha_inicio,
                pa.fecha_fin AS periodo_fecha_fin,
                pa.estado AS periodo_estado
            FROM postulante po
            INNER JOIN persona pe ON pe.id = po.persona_id
            LEFT JOIN carrera c1 ON c1.id = po.carrera_principal_id
            LEFT JOIN carrera c2 ON c2.id = po.carrera_secundaria_id
            LEFT JOIN periodo_academico pa ON pa.id = po.periodo_id
            WHERE po.id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $postulante = $stmt->fetch();
        return $postulante ?: null;
    }

    public static function crear($datos)
    {
        $db = self::db();
        $periodoId = self::resolverPeriodoId($datos['periodo_id'] ?? null);

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
            $codigoPostulante = self::generarCodigo('postulante', 'POST');

            $sqlPostulante = "
                INSERT INTO postulante (
                    codigo,
                    persona_id,
                    periodo_id,
                    colegio,
                    ciudad,
                    titulo_bachiller,
                    carrera_principal_id,
                    carrera_secundaria_id,
                    fecha_registro,
                    estado_postulacion,
                    created_at,
                    updated_at
                ) VALUES (
                    :codigo,
                    :persona_id,
                    :periodo_id,
                    :colegio,
                    :ciudad,
                    :titulo_bachiller,
                    :carrera_principal_id,
                    :carrera_secundaria_id,
                    CURRENT_DATE,
                    'Registrado',
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                )
                RETURNING id
            ";

            $stmtPostulante = $db->prepare($sqlPostulante);
            $stmtPostulante->execute([
                ':codigo' => $codigoPostulante,
                ':persona_id' => (int)$personaId,
                ':periodo_id' => (int)$periodoId,
                ':colegio' => self::nullSiVacio($datos['colegio'] ?? null),
                ':ciudad' => self::nullSiVacio($datos['ciudad'] ?? null),
                ':titulo_bachiller' => self::nullSiVacio($datos['titulo_bachiller'] ?? null),
                ':carrera_principal_id' => (int)$datos['carrera_principal_id'],
                ':carrera_secundaria_id' => (int)$datos['carrera_secundaria_id'],
            ]);

            $postulanteId = $stmtPostulante->fetchColumn();

            self::generarRequisitosPendientes($postulanteId);

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
        $postulante = self::buscarPorId($id);
        $periodoId = self::resolverPeriodoId($datos['periodo_id'] ?? null);

        if (!$postulante) {
            throw new Exception('El postulante no existe.');
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
                ':persona_id' => (int)$postulante['persona_id'],
            ]);

            $sqlPostulante = "
                UPDATE postulante
                SET
                    periodo_id = :periodo_id,
                    colegio = :colegio,
                    ciudad = :ciudad,
                    titulo_bachiller = :titulo_bachiller,
                    carrera_principal_id = :carrera_principal_id,
                    carrera_secundaria_id = :carrera_secundaria_id,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ";

            $stmtPostulante = $db->prepare($sqlPostulante);
            $stmtPostulante->execute([
                ':periodo_id' => (int)$periodoId,
                ':colegio' => self::nullSiVacio($datos['colegio'] ?? null),
                ':ciudad' => self::nullSiVacio($datos['ciudad'] ?? null),
                ':titulo_bachiller' => self::nullSiVacio($datos['titulo_bachiller'] ?? null),
                ':carrera_principal_id' => (int)$datos['carrera_principal_id'],
                ':carrera_secundaria_id' => (int)$datos['carrera_secundaria_id'],
                ':id' => (int)$id,
            ]);

            self::asegurarRequisitosPendientes($id);

            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $e;
        }
    }

    public static function obtenerRequisitos($postulanteId)
    {
        $sql = "
            SELECT
                id,
                postulante_id,
                tipo_documento,
                archivo,
                estado_validacion
            FROM documento
            WHERE postulante_id = :postulante_id
            ORDER BY id ASC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':postulante_id', (int)$postulanteId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function contar($periodoId = null)
    {
        if (!empty($periodoId)) {
            $stmt = self::db()->prepare("SELECT COUNT(*) FROM postulante WHERE periodo_id = :periodo_id");
            $stmt->bindValue(':periodo_id', (int)$periodoId, PDO::PARAM_INT);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        }

        $stmt = self::db()->query("SELECT COUNT(*) FROM postulante");
        return (int)$stmt->fetchColumn();
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
        if (empty($email)) {
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

    private static function resolverPeriodoId($periodoId = null)
    {
        $periodoId = (int)($periodoId ?? 0);

        if ($periodoId > 0 && PeriodoAcademico::buscarPorId($periodoId)) {
            return $periodoId;
        }

        $activo = PeriodoAcademico::obtenerIdActivo();

        if (!$activo) {
            throw new Exception('No existe un periodo académico activo o registrado.');
        }

        return $activo;
    }

    private static function generarRequisitosPendientes($postulanteId)
    {
        $requisitos = self::requisitosBase();

        $sql = "
            INSERT INTO documento (
                postulante_id,
                tipo_documento,
                archivo,
                estado_validacion
            ) VALUES (
                :postulante_id,
                :tipo_documento,
                NULL,
                'Pendiente'
            )
        ";

        $stmt = self::db()->prepare($sql);

        foreach ($requisitos as $requisito) {
            $stmt->execute([
                ':postulante_id' => (int)$postulanteId,
                ':tipo_documento' => $requisito,
            ]);
        }

        return true;
    }

    private static function asegurarRequisitosPendientes($postulanteId)
    {
        $requisitos = self::requisitosBase();

        foreach ($requisitos as $requisito) {
            $sqlExiste = "
                SELECT COUNT(*)
                FROM documento
                WHERE postulante_id = :postulante_id
                AND LOWER(tipo_documento) = LOWER(:tipo_documento)
            ";

            $stmtExiste = self::db()->prepare($sqlExiste);
            $stmtExiste->execute([
                ':postulante_id' => (int)$postulanteId,
                ':tipo_documento' => $requisito,
            ]);

            if ((int)$stmtExiste->fetchColumn() === 0) {
                $sqlInsertar = "
                    INSERT INTO documento (
                        postulante_id,
                        tipo_documento,
                        archivo,
                        estado_validacion
                    ) VALUES (
                        :postulante_id,
                        :tipo_documento,
                        NULL,
                        'Pendiente'
                    )
                ";

                $stmtInsertar = self::db()->prepare($sqlInsertar);
                $stmtInsertar->execute([
                    ':postulante_id' => (int)$postulanteId,
                    ':tipo_documento' => $requisito,
                ]);
            }
        }

        return true;
    }

    private static function requisitosBase()
    {
        return [
            'Original y fotocopia del título de bachiller',
            'Fotocopia de carnet de identidad',
            'Formulario de preinscripción',
            'Comprobante de pago',
            'Libreta o certificado de último año de secundaria',
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