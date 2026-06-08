<?php

require_once __DIR__ . '/app/Models/Conexion.php';

try {
    $db = Conexion::conectar();

    $consulta = $db->query("
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = 'public'
        ORDER BY table_name
    ");

    $tablas = $consulta->fetchAll();

    echo "<h2>Conexión exitosa a PostgreSQL</h2>";
    echo "<p>Base de datos conectada correctamente.</p>";

    echo "<h3>Tablas encontradas:</h3>";
    echo "<ul>";

    foreach ($tablas as $tabla) {
        echo "<li>" . htmlspecialchars($tabla['table_name']) . "</li>";
    }

    echo "</ul>";

} catch (Exception $e) {
    echo "<h2>Error de conexión</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}