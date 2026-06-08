<?php

namespace App\Models;

use PDO;

require_once __DIR__ . '/Conexion.php';

class PeriodoAcademico
{
    private static function db()
    {
        return Conexion::getConexion();
    }

    public static function obtenerTodos()
    {
        $sql = "
            SELECT
                id,
                codigo,
                gestion,
                semestre,
                fecha_inicio,
                fecha_fin,
                estado
            FROM periodo_academico
            ORDER BY gestion DESC, semestre DESC, id DESC
        ";

        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function obtenerActivo()
    {
        $sql = "
            SELECT
                id,
                codigo,
                gestion,
                semestre,
                fecha_inicio,
                fecha_fin,
                estado
            FROM periodo_academico
            WHERE LOWER(estado) = LOWER('Activo')
            ORDER BY gestion DESC, semestre DESC, id DESC
            LIMIT 1
        ";

        $stmt = self::db()->query($sql);
        $periodo = $stmt->fetch();

        return $periodo ?: null;
    }

    public static function obtenerUltimo()
    {
        $sql = "
            SELECT
                id,
                codigo,
                gestion,
                semestre,
                fecha_inicio,
                fecha_fin,
                estado
            FROM periodo_academico
            ORDER BY gestion DESC, semestre DESC, id DESC
            LIMIT 1
        ";

        $stmt = self::db()->query($sql);
        $periodo = $stmt->fetch();

        return $periodo ?: null;
    }

    public static function buscarPorId($id)
    {
        $sql = "
            SELECT
                id,
                codigo,
                gestion,
                semestre,
                fecha_inicio,
                fecha_fin,
                estado
            FROM periodo_academico
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $periodo = $stmt->fetch();
        return $periodo ?: null;
    }

    public static function obtenerIdActivo()
    {
        $activo = self::obtenerActivo();

        if ($activo) {
            return (int)$activo['id'];
        }

        $ultimo = self::obtenerUltimo();
        return $ultimo ? (int)$ultimo['id'] : null;
    }

    public static function esActivo($periodoId)
    {
        $periodo = self::buscarPorId($periodoId);

        if (!$periodo) {
            return false;
        }

        return strtolower((string)$periodo['estado']) === 'activo';
    }
}