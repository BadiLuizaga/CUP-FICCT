<?php

namespace App\Models;

use PDO;

require_once __DIR__ . '/Conexion.php';
require_once __DIR__ . '/PeriodoAcademico.php';
require_once __DIR__ . '/Nota.php';

class Carrera
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
                nombre,
                cupo_maximo,
                descripcion
            FROM carrera
            ORDER BY id ASC
        ";

        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function obtenerCuposPorPeriodo($periodoId)
    {
        $sql = "
            SELECT
                c.id,
                c.codigo,
                c.nombre,
                c.descripcion,
                c.cupo_maximo AS cupo_general,
                COALESCE(cpc.cupo_maximo, c.cupo_maximo, 0) AS cupo_maximo,
                COALESCE(admitidos.total_admitidos, 0) AS total_admitidos,
                GREATEST(COALESCE(cpc.cupo_maximo, c.cupo_maximo, 0) - COALESCE(admitidos.total_admitidos, 0), 0) AS cupos_disponibles
            FROM carrera c
            LEFT JOIN carrera_periodo_cupo cpc
                ON cpc.carrera_id = c.id
               AND cpc.periodo_id = :periodo_id
            LEFT JOIN (
                SELECT
                    carrera_admitida_id,
                    COUNT(*) AS total_admitidos
                FROM resultado_final
                WHERE periodo_id = :periodo_id
                  AND estado_final = 'ADMITIDO'
                  AND carrera_admitida_id IS NOT NULL
                GROUP BY carrera_admitida_id
            ) admitidos ON admitidos.carrera_admitida_id = c.id
            ORDER BY c.id ASC
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':periodo_id', (int)$periodoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function buscarPorId($id)
    {
        $sql = "
            SELECT
                id,
                codigo,
                nombre,
                cupo_maximo,
                descripcion
            FROM carrera
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();

        $carrera = $stmt->fetch();
        return $carrera ?: null;
    }

    public static function crear($datos)
    {
        $sql = "
            INSERT INTO carrera (
                codigo,
                nombre,
                cupo_maximo,
                descripcion
            ) VALUES (
                :codigo,
                :nombre,
                :cupo_maximo,
                :descripcion
            )
        ";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            ':codigo' => $datos['codigo'],
            ':nombre' => $datos['nombre'],
            ':cupo_maximo' => (int)$datos['cupo_maximo'],
            ':descripcion' => self::nullSiVacio($datos['descripcion'] ?? null),
        ]);
    }

    public static function actualizar($id, $datos)
    {
        $sql = "
            UPDATE carrera
            SET
                codigo = :codigo,
                nombre = :nombre,
                cupo_maximo = :cupo_maximo,
                descripcion = :descripcion
            WHERE id = :id
        ";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            ':codigo' => $datos['codigo'],
            ':nombre' => $datos['nombre'],
            ':cupo_maximo' => (int)$datos['cupo_maximo'],
            ':descripcion' => self::nullSiVacio($datos['descripcion'] ?? null),
            ':id' => (int)$id,
        ]);
    }

    public static function actualizarCupo($id, $cupoMaximo)
    {
        $sql = "
            UPDATE carrera
            SET cupo_maximo = :cupo_maximo
            WHERE id = :id
        ";

        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':cupo_maximo', (int)$cupoMaximo, PDO::PARAM_INT);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function actualizarCupoPorPeriodo($periodoId, $carreraId, $cupoMaximo)
    {
        $sql = "
            INSERT INTO carrera_periodo_cupo (
                periodo_id,
                carrera_id,
                cupo_maximo
            ) VALUES (
                :periodo_id,
                :carrera_id,
                :cupo_maximo
            )
            ON CONFLICT (periodo_id, carrera_id) DO UPDATE SET
                cupo_maximo = EXCLUDED.cupo_maximo
        ";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            ':periodo_id' => (int)$periodoId,
            ':carrera_id' => (int)$carreraId,
            ':cupo_maximo' => (int)$cupoMaximo,
        ]);
    }

    public static function existeCodigo($codigo, $exceptoId = null)
    {
        $sql = "SELECT COUNT(*) FROM carrera WHERE LOWER(codigo) = LOWER(:codigo)";
        $params = [':codigo' => $codigo];

        if ($exceptoId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = (int)$exceptoId;
        }

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    public static function existeNombre($nombre, $exceptoId = null)
    {
        $sql = "SELECT COUNT(*) FROM carrera WHERE LOWER(nombre) = LOWER(:nombre)";
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
        $stmt = self::db()->query("SELECT COUNT(*) FROM carrera");
        return (int)$stmt->fetchColumn();
    }

    public static function sumarCupos($periodoId = null)
    {
        if ($periodoId === null) {
            $periodoId = PeriodoAcademico::obtenerIdActivo();
        }

        if ($periodoId) {
            $sql = "
                SELECT COALESCE(SUM(cupo_maximo), 0)
                FROM carrera_periodo_cupo
                WHERE periodo_id = :periodo_id
            ";

            $stmt = self::db()->prepare($sql);
            $stmt->bindValue(':periodo_id', (int)$periodoId, PDO::PARAM_INT);
            $stmt->execute();

            $total = (int)$stmt->fetchColumn();

            if ($total > 0) {
                return $total;
            }
        }

        $stmt = self::db()->query("SELECT COALESCE(SUM(cupo_maximo), 0) FROM carrera");
        return (int)$stmt->fetchColumn();
    }

    public static function asignarCupoSiDisponible($postulanteId, $periodoId)
    {
        $db = self::db();
        
        // Obtener carreras del postulante
        $sql = "
            SELECT po.carrera_principal_id, po.carrera_secundaria_id
            FROM postulante po
            WHERE po.id = :postulante_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':postulante_id' => $postulanteId]);
        $postulante = $stmt->fetch();
        
        if (!$postulante) {
            return ['success' => false, 'mensaje' => 'Postulante no encontrado'];
        }
        
        // Verificar cupo en carrera principal
        $sqlCupo = "
            SELECT 
                COALESCE(cpc.cupo_maximo, c.cupo_maximo, 0) AS cupo_maximo,
                COUNT(rf.id) AS admitidos
            FROM carrera c
            LEFT JOIN carrera_periodo_cupo cpc 
                ON cpc.carrera_id = c.id AND cpc.periodo_id = :periodo_id
            LEFT JOIN resultado_final rf 
                ON rf.carrera_admitida_id = c.id 
                AND rf.periodo_id = :periodo_id 
                AND rf.estado_final = 'ADMITIDO'
            WHERE c.id = :carrera_id
            GROUP BY c.id, cpc.cupo_maximo, c.cupo_maximo
        ";
        
        // Probar carrera principal
        $stmt = $db->prepare($sqlCupo);
        $stmt->execute([
            ':periodo_id' => $periodoId,
            ':carrera_id' => $postulante['carrera_principal_id']
        ]);
        $cupoPrincipal = $stmt->fetch();
        
        $cupoDisponiblePrincipal = ($cupoPrincipal['cupo_maximo'] - $cupoPrincipal['admitidos']) > 0;
        
        if ($cupoDisponiblePrincipal) {
            return self::registrarAdmision($postulanteId, $periodoId, $postulante['carrera_principal_id'], 1);
        }
        
        // Probar carrera secundaria
        $stmt = $db->prepare($sqlCupo);
        $stmt->execute([
            ':periodo_id' => $periodoId,
            ':carrera_id' => $postulante['carrera_secundaria_id']
        ]);
        $cupoSecundario = $stmt->fetch();
        
        $cupoDisponibleSecundario = ($cupoSecundario['cupo_maximo'] - $cupoSecundario['admitidos']) > 0;
        
        if ($cupoDisponibleSecundario) {
            return self::registrarAdmision($postulanteId, $periodoId, $postulante['carrera_secundaria_id'], 2);
        }
        
        return [
            'success' => false, 
            'mensaje' => 'No hay cupo disponible en ninguna de las carreras seleccionadas'
        ];
    }

    public static function registrarAdmision($postulanteId, $periodoId, $carreraId, $opcion)
    {
        $db = self::db();
        
        // Calcular promedio del postulante
        $promedioData = Nota::calcularPromedioFinalPostulante($postulanteId, null);
        $promedio = $promedioData['promedio'] ?? 0;
        
        // Verificar si ya tiene resultado
        $sqlCheck = "SELECT id FROM resultado_final WHERE postulante_id = :postulante_id";
        $stmt = $db->prepare($sqlCheck);
        $stmt->execute([':postulante_id' => $postulanteId]);
        $existente = $stmt->fetch();
        
        if ($existente) {
            $sql = "
                UPDATE resultado_final
                SET 
                    estado_final = 'ADMITIDO',
                    carrera_admitida_id = :carrera_id,
                    opcion_admitida = :opcion,
                    observacion = :observacion,
                    periodo_id = :periodo_id,
                    promedio_general = :promedio
                WHERE postulante_id = :postulante_id
            ";
        } else {
            $sql = "
                INSERT INTO resultado_final (
                    postulante_id, periodo_id, promedio_general, 
                    estado_final, carrera_admitida_id, opcion_admitida, observacion
                ) VALUES (
                    :postulante_id, :periodo_id, :promedio,
                    'ADMITIDO', :carrera_id, :opcion, :observacion
                )
            ";
        }
        
        $observacion = 'Admitido por cumplir con nota mínima y disponibilidad de cupo en ' . ($opcion == 1 ? 'primera' : 'segunda') . ' opción';
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':postulante_id' => $postulanteId,
            ':periodo_id' => $periodoId,
            ':promedio' => $promedio,
            ':carrera_id' => $carreraId,
            ':opcion' => $opcion,
            ':observacion' => $observacion
        ]);
        
        // Actualizar estado del postulante
        $sqlUpd = "UPDATE postulante SET estado_postulacion = 'Admitido' WHERE id = :id";
        $stmtUpd = $db->prepare($sqlUpd);
        $stmtUpd->execute([':id' => $postulanteId]);
        
        return [
            'success' => true,
            'mensaje' => 'Postulante admitido en ' . ($opcion == 1 ? 'primera' : 'segunda') . ' opción'
        ];
    }

    private static function nullSiVacio($valor)
    {
        $valor = trim((string)($valor ?? ''));
        return $valor === '' ? null : $valor;
    }
}