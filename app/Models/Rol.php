<?php

namespace App\Models;

use PDO;

require_once __DIR__ . '/Conexion.php';

class Rol
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    public static function obtenerTodos()
    {
        $sql = "
            SELECT 
                r.id,
                r.nombre,
                r.descripcion,
                COUNT(ur.usuario_id) AS total_usuarios
            FROM rol r
            LEFT JOIN usuario_rol ur ON ur.rol_id = r.id
            GROUP BY r.id, r.nombre, r.descripcion
            ORDER BY r.id ASC
        ";

        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function buscarPorId($id)
    {
        $sql = "
            SELECT id, nombre, descripcion
            FROM rol
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $rol = $stmt->fetch();

        return $rol ?: null;
    }

    public static function crear($datos)
    {
        $sql = "
            INSERT INTO rol (nombre, descripcion)
            VALUES (:nombre, :descripcion)
        ";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':descripcion' => self::nullSiVacio($datos['descripcion'] ?? null),
        ]);
    }

    public static function actualizar($id, $datos)
    {
        $sql = "
            UPDATE rol
            SET 
                nombre = :nombre,
                descripcion = :descripcion
            WHERE id = :id
        ";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':descripcion' => self::nullSiVacio($datos['descripcion'] ?? null),
            ':id' => (int)$id,
        ]);
    }

    public static function existeNombre($nombre, $exceptoId = null)
    {
        $sql = "SELECT COUNT(*) FROM rol WHERE LOWER(nombre) = LOWER(:nombre)";
        $params = [':nombre' => $nombre];

        if ($exceptoId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    public static function contar()
    {
        $stmt = self::db()->query("SELECT COUNT(*) FROM rol");
        return (int)$stmt->fetchColumn();
    }

    private static function nullSiVacio($valor)
    {
        $valor = trim((string)($valor ?? ''));

        return $valor === '' ? null : $valor;
    }
}