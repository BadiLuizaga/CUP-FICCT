<?php

namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/Conexion.php';

class Usuario
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    public static function obtenerTodos()
    {
        $sql = "
            SELECT 
                u.id,
                u.codigo,
                u.username,
                u.estado,
                u.ultimo_acceso,
                u.created_at,
                p.ci,
                p.nombres,
                p.apellidos,
                p.email,
                p.telefono,
                COALESCE(string_agg(r.nombre, ', ' ORDER BY r.nombre), 'Sin rol') AS roles
            FROM usuario u
            LEFT JOIN persona p ON p.id = u.persona_id
            LEFT JOIN usuario_rol ur ON ur.usuario_id = u.id
            LEFT JOIN rol r ON r.id = ur.rol_id
            GROUP BY 
                u.id,
                u.codigo,
                u.username,
                u.estado,
                u.ultimo_acceso,
                u.created_at,
                p.ci,
                p.nombres,
                p.apellidos,
                p.email,
                p.telefono
            ORDER BY u.id ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function buscarPorUsername($username)
    {
        $sql = "
            SELECT 
                u.id,
                u.codigo,
                u.persona_id,
                u.username,
                u.password,
                u.estado,
                u.ultimo_acceso,
                p.ci,
                p.nombres,
                p.apellidos,
                p.email,
                p.telefono,
                p.direccion
            FROM usuario u
            LEFT JOIN persona p ON p.id = u.persona_id
            WHERE LOWER(u.username) = LOWER(:username)
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public static function buscarPorId($id)
    {
        $sql = "
            SELECT 
                u.id,
                u.codigo,
                u.persona_id,
                u.username,
                u.estado,
                u.ultimo_acceso,
                u.created_at,
                u.updated_at,
                p.codigo AS persona_codigo,
                p.ci,
                p.nombres,
                p.apellidos,
                p.fecha_nacimiento,
                p.sexo,
                p.email,
                p.telefono,
                p.direccion,
                (
                    SELECT ur.rol_id 
                    FROM usuario_rol ur 
                    WHERE ur.usuario_id = u.id 
                    ORDER BY ur.id ASC
                    LIMIT 1
                ) AS rol_id
            FROM usuario u
            LEFT JOIN persona p ON p.id = u.persona_id
            WHERE u.id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public static function obtenerRolesUsuario($usuarioId)
    {
        $sql = "
            SELECT r.nombre
            FROM rol r
            INNER JOIN usuario_rol ur ON ur.rol_id = r.id
            WHERE ur.usuario_id = :usuario_id
            ORDER BY r.nombre ASC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':usuario_id', (int)$usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        return array_column($stmt->fetchAll(), 'nombre');
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

            $codigoUsuario = self::generarCodigo('usuario', 'USU');

            /*
             * CONTRASEÑA NORMAL:
             * Aquí ya no usamos password_hash.
             * Se guarda exactamente como el usuario la escribe.
             */
            $passwordNormal = $datos['password'];

            $sqlUsuario = "
                INSERT INTO usuario (
                    codigo,
                    persona_id,
                    username,
                    password,
                    estado,
                    created_at,
                    updated_at
                ) VALUES (
                    :codigo,
                    :persona_id,
                    :username,
                    :password,
                    :estado,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                )
                RETURNING id
            ";

            $stmtUsuario = $db->prepare($sqlUsuario);
            $stmtUsuario->bindValue(':codigo', $codigoUsuario);
            $stmtUsuario->bindValue(':persona_id', (int)$personaId, PDO::PARAM_INT);
            $stmtUsuario->bindValue(':username', $datos['username']);
            $stmtUsuario->bindValue(':password', $passwordNormal);
            $stmtUsuario->bindValue(':estado', (bool)$datos['estado'], PDO::PARAM_BOOL);
            $stmtUsuario->execute();

            $usuarioId = $stmtUsuario->fetchColumn();

            if (!empty($datos['rol_id'])) {
                self::asignarRol($usuarioId, $datos['rol_id']);
            }

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
        $usuario = self::buscarPorId($id);

        if (!$usuario) {
            throw new Exception('El usuario no existe.');
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
                ':persona_id' => (int)$usuario['persona_id'],
            ]);

            $sqlUsuario = "
                UPDATE usuario
                SET 
                    username = :username,
                    estado = :estado,
                    updated_at = CURRENT_TIMESTAMP
            ";

            if (!empty($datos['password'])) {
                $sqlUsuario .= ", password = :password";
            }

            $sqlUsuario .= " WHERE id = :id";

            $stmtUsuario = $db->prepare($sqlUsuario);
            $stmtUsuario->bindValue(':username', $datos['username']);
            $stmtUsuario->bindValue(':estado', (bool)$datos['estado'], PDO::PARAM_BOOL);
            $stmtUsuario->bindValue(':id', (int)$id, PDO::PARAM_INT);

            /*
             * CONTRASEÑA NORMAL:
             * Si se escribe una nueva contraseña al editar,
             * se guarda tal cual, sin encriptar.
             */
            if (!empty($datos['password'])) {
                $stmtUsuario->bindValue(':password', $datos['password']);
            }

            $stmtUsuario->execute();

            self::actualizarRolUsuario($id, $datos['rol_id'] ?? null);

            $db->commit();

            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $e;
        }
    }

    public static function cambiarEstado($id)
    {
        $usuario = self::buscarPorId($id);

        if (!$usuario) {
            throw new Exception('El usuario no existe.');
        }

        $estadoActual = self::valorActivo($usuario['estado']);
        $nuevoEstado = !$estadoActual;

        $sql = "
            UPDATE usuario
            SET estado = :estado,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':estado', $nuevoEstado, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function actualizarUltimoAcceso($id)
    {
        $sql = "
            UPDATE usuario
            SET ultimo_acceso = CURRENT_TIMESTAMP
            WHERE id = :id
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function existeUsername($username, $exceptoId = null)
    {
        $sql = "SELECT COUNT(*) FROM usuario WHERE LOWER(username) = LOWER(:username)";
        $params = [':username' => $username];

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

    public static function contar()
    {
        $stmt = self::db()->query("SELECT COUNT(*) FROM usuario");
        return (int)$stmt->fetchColumn();
    }

    public static function contarActivos()
    {
        $stmt = self::db()->query("SELECT COUNT(*) FROM usuario WHERE estado = TRUE");
        return (int)$stmt->fetchColumn();
    }

    private static function asignarRol($usuarioId, $rolId)
    {
        $sql = "
            INSERT INTO usuario_rol (usuario_id, rol_id)
            VALUES (:usuario_id, :rol_id)
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':usuario_id', (int)$usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':rol_id', (int)$rolId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private static function actualizarRolUsuario($usuarioId, $rolId)
    {
        $db = self::db();

        $stmtEliminar = $db->prepare("DELETE FROM usuario_rol WHERE usuario_id = :usuario_id");
        $stmtEliminar->bindValue(':usuario_id', (int)$usuarioId, PDO::PARAM_INT);
        $stmtEliminar->execute();

        if (!empty($rolId)) {
            self::asignarRol($usuarioId, $rolId);
        }

        return true;
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

    private static function valorActivo($valor)
    {
        if (is_bool($valor)) {
            return $valor;
        }

        $valor = strtolower((string)$valor);

        return in_array($valor, ['1', 't', 'true', 'activo', 'active', 'si', 'sí'], true);
    }
}