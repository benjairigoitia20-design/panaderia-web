<?php

define('DB_HOST', 'localhost');
define('DB_PORT', '3000');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'panaderia_db');

function conectarDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST .
            ";port=" . DB_PORT .
            ";dbname=" . DB_NAME .
            ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("SET time_zone = '-03:00'");

        return $pdo;

    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

?>