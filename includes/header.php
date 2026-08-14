<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panadería - Sistema de Gestión</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>
<body>

<div class="sidebar-overlay"></div>

<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon">🍞</span>
        <div>
            <span class="brand-text">Panadería</span>
            <span class="brand-sub">Sistema de Gestión</span>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-label">Dashboard</li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? 'dashboard') == 'dashboard' ? 'active' : '' ?>" href="index.php?modulo=dashboard">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>

        <li class="menu-label">Catálogo</li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'productos' ? 'active' : '' ?>" href="index.php?modulo=productos&accion=index">
                <i class="bi bi-box-seam"></i> Productos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'categorias' ? 'active' : '' ?>" href="index.php?modulo=categorias&accion=index">
                <i class="bi bi-tags"></i> Categorías
            </a>
        </li>

        <li class="menu-label">Inventario</li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'ingredientes' ? 'active' : '' ?>" href="index.php?modulo=ingredientes&accion=index">
                <i class="bi bi-box"></i> Ingredientes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'recetas' ? 'active' : '' ?>" href="index.php?modulo=recetas&accion=index">
                <i class="bi bi-journal-bookmark-fill"></i> Recetas
            </a>
        </li>

        <li class="menu-label">Operaciones</li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'produccion' ? 'active' : '' ?>" href="index.php?modulo=produccion&accion=index">
                <i class="bi bi-gear-wide-connected"></i> Producción
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'ventas' ? 'active' : '' ?>" href="index.php?modulo=ventas&accion=index">
                <i class="bi bi-cash"></i> Ventas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'caja' ? 'active' : '' ?>" href="index.php?modulo=caja&accion=index">
                <i class="bi bi-wallet2"></i> Caja
            </a>
        </li>
        <?php if (esAdmin() || tieneRol('encargado') || tieneRol('vendedor')): ?>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'pedidos' ? 'active' : '' ?>" href="index.php?modulo=pedidos&accion=index">
                <i class="bi bi-clipboard"></i> Pedidos
            </a>
        </li>
        <?php endif; ?>

        <li class="menu-label">Compras</li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'proveedores' ? 'active' : '' ?>" href="index.php?modulo=proveedores&accion=index">
                <i class="bi bi-building"></i> Proveedores
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'compras' ? 'active' : '' ?>" href="index.php?modulo=compras&accion=index">
                <i class="bi bi-cart3"></i> Compras
            </a>
        </li>

        <li class="menu-label">Calidad</li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'mermas' ? 'active' : '' ?>" href="index.php?modulo=mermas&accion=index">
                <i class="bi bi-exclamation-triangle"></i> Mermas
            </a>
        </li>

        <li class="menu-label">Reportes</li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'reportes' && ($accion ?? '') == 'index' ? 'active' : '' ?>" href="index.php?modulo=reportes&accion=index">
                <i class="bi bi-graph-up-arrow"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'reportes' && ($accion ?? '') == 'ventas' ? 'active' : '' ?>" href="index.php?modulo=reportes&accion=ventas">
                <i class="bi bi-bar-chart"></i> Ventas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'reportes' && ($accion ?? '') == 'inventario' ? 'active' : '' ?>" href="index.php?modulo=reportes&accion=inventario">
                <i class="bi bi-boxes"></i> Inventario
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'reportes' && ($accion ?? '') == 'rentabilidad' ? 'active' : '' ?>" href="index.php?modulo=reportes&accion=rentabilidad">
                <i class="bi bi-pie-chart"></i> Rentabilidad
            </a>
        </li>

        <?php if (esAdmin()): ?>
        <li class="menu-label">Administración</li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'usuarios' ? 'active' : '' ?>" href="index.php?modulo=usuarios&accion=index">
                <i class="bi bi-people"></i> Usuarios
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($modulo ?? '') == 'roles' ? 'active' : '' ?>" href="index.php?modulo=roles&accion=index">
                <i class="bi bi-shield-lock"></i> Roles y Permisos
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['usuario_nombre'] ?? 'U', 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></div>
                <div class="user-role"><?= htmlspecialchars($_SESSION['rol'] ?? 'Sin rol') ?></div>
            </div>
        </div>
    </div>
</aside>

<div class="main-content">
    <div class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <div class="page-title">
                <h4>
                    <?php
                    $titulos = [
                        'dashboard' => 'Dashboard',
                        'productos' => 'Productos',
                        'categorias' => 'Categorías',
                        'ingredientes' => 'Ingredientes',
                        'recetas' => 'Recetas',
                        'produccion' => 'Producción',
                        'ventas' => 'Ventas',
                        'caja' => 'Caja',
                        'pedidos' => 'Pedidos',
                        'proveedores' => 'Proveedores',
                        'compras' => 'Compras',
                        'mermas' => 'Mermas',
                        'reportes' => 'Reportes',
                        'usuarios' => 'Usuarios',
                        'roles' => 'Roles y Permisos'
                    ];
                    $modulo_actual = $modulo ?? 'dashboard';
                    echo $titulos[$modulo_actual] ?? ucfirst($modulo_actual);
                    ?>
                </h4>
                <small><?= date('d/m/Y H:i') ?></small>
            </div>
        </div>
        <div class="top-actions">
            <a href="index.php?modulo=usuarios&accion=perfil" class="btn-perfil">
                <i class="bi bi-person"></i> Perfil
            </a>
            <a href="logout.php" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
        </div>
    </div>

    <div class="content-wrapper">