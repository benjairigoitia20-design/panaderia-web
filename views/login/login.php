<?php
require_once __DIR__ . '/../../includes/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Panadería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .login-container { max-width: 400px; margin: 100px auto; }
        .card { border-radius: 15px; }
    </style>
</head>
<body>
<div class="container">
    <div class="login-container">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">🍞 Panadería</h3>
                <h5 class="text-center mb-4">Iniciar Sesión</h5>

                <?php if ($mensaje = getMensaje()): ?>
                    <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
                <?php endif; ?>

                <form action="../../procesar_login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>