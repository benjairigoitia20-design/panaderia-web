<?php
require_once 'config/database.php';

$pdo = conectarDB();

// Lista de usuarios a actualizar
$usuarios = [
    'admin@panaderia.com',
    'encargado@panaderia.com',
    'panadero@panaderia.com',
    'vendedor@panaderia.com',
    'produccion@panaderia.com'
];

foreach ($usuarios as $email) {
    // Verificar si existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        // Importante: cada hash debe generarse con un salt aleatorio.
        $hash = password_hash('123456', PASSWORD_DEFAULT);

        $update = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
        $update->execute([$hash, $email]);
        echo "✅ Contraseña actualizada para: <strong>$email</strong><br>";
    } else {
        echo "❌ Usuario no encontrado: <strong>$email</strong><br>";
    }
}

echo "<br><hr>";
echo "<h3>✅ Proceso completado</h3>";
echo "<p><strong>Todos los usuarios tienen contraseña: 123456</strong></p>";
echo "<p><a href='index.php'>Ir al inicio</a></p>";
?>