<?php
require_once __DIR__ . '/../../includes/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panadería - Iniciar Sesión</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ============================================
           VARIABLES
           ============================================ */
        :root {
            --primary: #6C3A2A;
            --primary-dark: #4A2518;
            --primary-light: #8B5A4A;
            --secondary: #D4A574;
            --accent: #E8C99B;
            --bg-light: #F8F6F3;
            --text-dark: #2C2C2C;
            --text-gray: #6B6B6B;
            --shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            --radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-light);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* ============================================
           FONDO DECORATIVO
           ============================================ */
        body::before {
            content: '🍞';
            position: fixed;
            font-size: 400px;
            opacity: 0.04;
            bottom: -100px;
            right: -100px;
            transform: rotate(-15deg);
            pointer-events: none;
        }

        body::after {
            content: '🥖';
            position: fixed;
            font-size: 250px;
            opacity: 0.04;
            top: -60px;
            left: -80px;
            transform: rotate(20deg);
            pointer-events: none;
        }

        /* ============================================
           CONTAINER PRINCIPAL
           ============================================ */
        .login-container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            min-height: 600px;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease forwards;
        }

        /* ============================================
           LADO IZQUIERDO - IMAGEN/BANNER
           ============================================ */
        .login-banner {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary), var(--primary-light));
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-banner::before {
            content: '🍞';
            position: absolute;
            font-size: 300px;
            opacity: 0.08;
            bottom: -80px;
            right: -80px;
            transform: rotate(-10deg);
        }

        .login-banner::after {
            content: '🥐';
            position: absolute;
            font-size: 180px;
            opacity: 0.06;
            top: -40px;
            left: -40px;
            transform: rotate(15deg);
        }

        .login-banner .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .login-banner .brand-icon {
            font-size: 40px;
        }

        .login-banner .brand-text {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .login-banner .brand-sub {
            font-size: 13px;
            opacity: 0.7;
            font-weight: 300;
            display: block;
        }

        .login-banner .banner-content {
            position: relative;
            z-index: 1;
        }

        .login-banner .banner-content h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .login-banner .banner-content p {
            font-size: 16px;
            opacity: 0.8;
            line-height: 1.6;
            max-width: 380px;
        }

        .login-banner .banner-features {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 24px;
        }

        .login-banner .banner-features .feature {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            opacity: 0.85;
        }

        .login-banner .banner-features .feature i {
            font-size: 18px;
            width: 28px;
            height: 28px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .login-banner .banner-footer {
            font-size: 12px;
            opacity: 0.5;
            position: relative;
            z-index: 1;
        }

        /* ============================================
           LADO DERECHO - FORMULARIO
           ============================================ */
        .login-form {
            flex: 1;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .login-form .form-header {
            margin-bottom: 32px;
        }

        .login-form .form-header h3 {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .login-form .form-header p {
            color: var(--text-gray);
            font-size: 14px;
        }

        .login-form .form-group {
            margin-bottom: 20px;
        }

        .login-form .form-group label {
            font-weight: 600;
            font-size: 13px;
            color: var(--text-dark);
            margin-bottom: 6px;
            display: block;
        }

        .login-form .form-group .input-group-custom {
            position: relative;
        }

        .login-form .form-group .input-group-custom .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
            font-size: 18px;
            transition: var(--transition);
        }

        .login-form .form-group .form-control {
            width: 100%;
            padding: 12px 14px 12px 46px;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
            background: var(--bg-light);
        }

        .login-form .form-group .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(108, 58, 42, 0.1);
            outline: none;
            background: white;
        }

        .login-form .form-group .form-control:focus + .input-icon,
        .login-form .form-group .form-control:focus ~ .input-icon {
            color: var(--primary);
        }

        .login-form .form-group .form-control::placeholder {
            color: #b0b0b0;
            font-size: 13px;
        }

        .login-form .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .login-form .form-options .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-gray);
        }

        .login-form .form-options .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
        }

        .login-form .form-options .forgot-link {
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .login-form .form-options .forgot-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .login-form .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-form .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 58, 42, 0.3);
        }

        .login-form .btn-login:active {
            transform: translateY(0);
        }

        .login-form .btn-login i {
            font-size: 20px;
        }

        .login-form .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: var(--text-gray);
        }

        .login-form .login-footer .version {
            font-size: 11px;
            opacity: 0.5;
            margin-top: 4px;
        }

        /* ============================================
           ALERTAS
           ============================================ */
        .alert-custom {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: none;
        }

        .alert-custom i {
            font-size: 18px;
        }

        .alert-custom.alert-danger {
            background: #fde8e8;
            color: #c0392b;
        }

        .alert-custom.alert-success {
            background: #e8f5e9;
            color: #2d8b46;
        }

        .alert-custom.alert-warning {
            background: #fff3e0;
            color: #e8a838;
        }

        /* ============================================
           ANIMACIONES
           ============================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 4s ease-in-out infinite;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                max-width: 480px;
                min-height: auto;
            }

            .login-banner {
                padding: 32px 28px;
                min-height: 200px;
            }

            .login-banner .banner-content h2 {
                font-size: 24px;
            }

            .login-banner .banner-content p {
                font-size: 14px;
                max-width: 100%;
            }

            .login-banner .banner-features {
                display: none;
            }

            .login-form {
                padding: 32px 28px;
            }

            .login-form .form-header h3 {
                font-size: 22px;
            }

            body::before,
            body::after {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                border-radius: 16px;
            }

            .login-banner {
                padding: 24px 20px;
                min-height: 160px;
            }

            .login-banner .brand-text {
                font-size: 20px;
            }

            .login-banner .banner-content h2 {
                font-size: 20px;
            }

            .login-form {
                padding: 24px 20px;
            }

            .login-form .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .login-form .btn-login {
                font-size: 15px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- ============================================
         LADO IZQUIERDO - BANNER
         ============================================ -->
    <div class="login-banner">
        <div class="brand">
            <span class="brand-icon">🍞</span>
            <div>
                <span class="brand-text">Panadería</span>
                <span class="brand-sub">Sistema de Gestión</span>
            </div>
        </div>

        <div class="banner-content">
            <h2 class="float-animation">Bienvenido de vuelta 👋</h2>
            <p>Gestiona tu panadería de forma inteligente. Controla inventario, ventas, producción y más desde un solo lugar.</p>
            
            <div class="banner-features">
                <div class="feature">
                    <i class="bi bi-box-seam"></i>
                    <span>Inventario en tiempo real</span>
                </div>
                <div class="feature">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Reportes y estadísticas</span>
                </div>
                <div class="feature">
                    <i class="bi bi-cash-stack"></i>
                    <span>Control de ventas y caja</span>
                </div>
            </div>
        </div>

        <div class="banner-footer">
            &copy; <?= date('Y') ?> Panadería - Todos los derechos reservados
        </div>
    </div>

    <!-- ============================================
         LADO DERECHO - FORMULARIO
         ============================================ -->
    <div class="login-form">
        <div class="form-header">
            <h3>Iniciar Sesión</h3>
            <p>Ingresa tus credenciales para acceder al sistema</p>
        </div>

        <?php if ($mensaje = getMensaje()): ?>
            <div class="alert-custom alert-<?= $mensaje['tipo'] ?>">
                <i class="bi bi-<?= $mensaje['tipo'] == 'danger' ? 'exclamation-circle' : ($mensaje['tipo'] == 'success' ? 'check-circle' : 'info-circle') ?>"></i>
                <?= $mensaje['texto'] ?>
            </div>
        <?php endif; ?>

        <form action="../../procesar_login.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email"><i class="bi bi-envelope me-1"></i> Correo Electrónico</label>
                <div class="input-group-custom">
                    <input type="email" name="email" id="email" class="form-control" placeholder="admin@panaderia.com" required autofocus>
                    <i class="bi bi-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password"><i class="bi bi-lock me-1"></i> Contraseña</label>
                <div class="input-group-custom">
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    <i class="bi bi-lock input-icon"></i>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    Recordarme
                </label>
                <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                Ingresar al Sistema
            </button>

            <div class="login-footer">
                <p>Sistema de Gestión para Panaderías</p>
                <div class="version">Versión 2.0</div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const email = document.getElementById('email');
    const password = document.getElementById('password');

    // Animación de entrada para el formulario
    document.querySelector('.login-container').style.animation = 'fadeInUp 0.6s ease forwards';

    // Validación en tiempo real
    form.addEventListener('submit', function(e) {
        let valid = true;
        let firstError = null;

        // Validar email
        if (!email.value.trim() || !email.value.includes('@')) {
            email.classList.add('is-invalid');
            valid = false;
            if (!firstError) firstError = email;
        } else {
            email.classList.remove('is-invalid');
        }

        // Validar contraseña
        if (!password.value.trim() || password.value.length < 4) {
            password.classList.add('is-invalid');
            valid = false;
            if (!firstError) firstError = password;
        } else {
            password.classList.remove('is-invalid');
        }

        if (!valid && firstError) {
            e.preventDefault();
            firstError.focus();
            firstError.style.borderColor = '#c0392b';
            setTimeout(() => {
                firstError.style.borderColor = '';
            }, 3000);
        }
    });

    // Efecto de enfoque en los inputs
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.querySelector('.input-icon').style.color = '#6C3A2A';
        });
        input.addEventListener('blur', function() {
            this.parentElement.querySelector('.input-icon').style.color = '';
        });
    });

    // Auto cerrar alertas después de 5 segundos
    const alerts = document.querySelectorAll('.alert-custom');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });

    console.log('🍞 Panadería - Login v2.0');
});
</script>

</body>
</html>