<?php
require_once 'config/database.php';
require_once 'includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizar($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        setMensaje('danger', 'Todos los campos son obligatorios.');
        redirigir('views/login/login.php');
    }

    $pdo = conectarDB();
    $stmt = $pdo->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.email = ? AND u.activo = 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol_nombre'];
        $_SESSION['rol_id'] = $usuario['rol_id'];

        // Redirigir al dashboard
        redirigir('index.php');
    } else {
        setMensaje('danger', 'Credenciales incorrectas o usuario inactivo.');
        redirigir('views/login/login.php');
    }
} else {
    redirigir('views/login/login.php');
}