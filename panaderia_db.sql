-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-10-2026 a las 21:17:27
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `panaderia_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `datos_anteriores` text DEFAULT NULL,
  `datos_nuevos` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `id` int(11) NOT NULL,
  `usuario_apertura_id` int(11) NOT NULL,
  `usuario_cierre_id` int(11) DEFAULT NULL,
  `fecha_apertura` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `monto_esperado` decimal(10,2) DEFAULT 0.00,
  `monto_contado` decimal(10,2) DEFAULT 0.00,
  `diferencia` decimal(10,2) DEFAULT 0.00,
  `estado` enum('abierta','cerrada') DEFAULT 'abierta',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `caja`
--

INSERT INTO `caja` (`id`, `usuario_apertura_id`, `usuario_cierre_id`, `fecha_apertura`, `fecha_cierre`, `monto_inicial`, `monto_esperado`, `monto_contado`, `diferencia`, `estado`, `observaciones`) VALUES
(1, 1, 1, '2026-08-14 01:30:01', '2026-08-14 01:30:19', 0.06, 0.06, 0.06, 0.00, 'cerrada', 'dyfgtdfuygdfuygdfuygdfyug - Cierre: asdasdasdasd'),
(2, 1, 1, '2026-08-17 01:31:40', '2026-08-17 01:32:13', 2.00, 2.00, 2.00, 0.00, 'cerrada', 'prueba - Cierre: Prueba2'),
(3, 1, 1, '2026-09-17 20:08:05', '2026-09-17 20:08:40', 0.00, 0.00, 0.00, 0.00, 'cerrada', ' - Cierre: '),
(4, 1, NULL, '2026-09-17 20:13:50', NULL, 0.00, 0.00, 0.00, 0.00, 'abierta', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_movimientos`
--

CREATE TABLE `caja_movimientos` (
  `id` int(11) NOT NULL,
  `caja_id` int(11) NOT NULL,
  `tipo` enum('ingreso','egreso','venta','retiro','ajuste') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `caja_movimientos`
--

INSERT INTO `caja_movimientos` (`id`, `caja_id`, `tipo`, `monto`, `descripcion`, `venta_id`, `usuario_id`, `created_at`) VALUES
(1, 4, 'venta', 2.50, 'Venta VTA-2026090002', 12, 5, '2026-09-27 16:20:17'),
(2, 4, 'venta', 123.00, 'Venta VTA-2026090003', 13, 5, '2026-09-27 16:21:37'),
(3, 4, 'venta', 21651843.00, 'Venta VTA-2026100001', 15, 1, '2026-10-29 11:17:12'),
(4, 4, 'venta', 1500.00, 'Venta VTA-2026100002', 16, 1, '2026-10-29 11:18:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activo`, `created_at`) VALUES
(1, 'Panes', 'Todo tipo de panes artesanales', 1, '2026-08-06 15:53:43'),
(2, 'Pastelería', 'Dulces, tartas y pasteles', 1, '2026-08-06 15:53:43'),
(3, 'Facturas', 'Medialunas, criollos y facturas', 1, '2026-08-06 15:53:43'),
(4, 'Galletas', 'Galletas y cookies artesanales', 1, '2026-08-06 15:53:43'),
(5, 'Otro', 'A especificar en la descripción de carga del producto.', 1, '2026-08-24 03:40:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT 0.00,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_pedidos`
--

CREATE TABLE `config_pedidos` (
  `id` int(11) NOT NULL,
  `dias_anticipacion` int(11) DEFAULT 2,
  `hora_limite` time DEFAULT '18:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `config_pedidos`
--

INSERT INTO `config_pedidos` (`id`, `dias_anticipacion`, `hora_limite`, `created_at`) VALUES
(1, 2, '18:00:00', '2026-08-14 03:43:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_reportes`
--

CREATE TABLE `config_reportes` (
  `id` int(11) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `config_reportes`
--

INSERT INTO `config_reportes` (`id`, `clave`, `valor`, `created_at`) VALUES
(1, 'dias_historial_ventas', '30', '2026-08-14 05:28:06'),
(2, 'stock_minimo_global', '10', '2026-08-14 05:28:06'),
(3, 'margen_rentabilidad_minimo', '30', '2026-08-14 05:28:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_precios`
--

CREATE TABLE `historial_precios` (
  `id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `precio_anterior` decimal(10,4) NOT NULL,
  `precio_nuevo` decimal(10,4) NOT NULL,
  `orden_compra_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes`
--

CREATE TABLE `ingredientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `unidad_medida_id` int(11) NOT NULL,
  `stock_actual` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` decimal(10,2) DEFAULT 0.00,
  `costo_unitario` decimal(10,4) DEFAULT 0.0000,
  `proveedor_principal` varchar(100) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ingredientes`
--

INSERT INTO `ingredientes` (`id`, `nombre`, `codigo`, `categoria`, `unidad_medida_id`, `stock_actual`, `stock_minimo`, `costo_unitario`, `proveedor_principal`, `fecha_vencimiento`, `estado`, `created_at`) VALUES
(1, 'Harina Integral', 'HAR-001', 'Harina', 2, 15.00, 10.00, 1000.0000, 'Harinas S.A', '2028-10-11', 1, '2026-08-24 03:55:56'),
(2, 'Levadura', 'LEV-001', 'Levaduras', 1, 25.00, 10.00, 100.0000, 'Levaduras S.A', '2026-09-24', 1, '2026-08-24 19:37:06'),
(3, 'Harina Blanca', 'HAR-000', 'Harinas', 2, 50.00, 25.00, 500.0000, 'Harinas S.A', '2026-09-16', 1, '2026-08-24 19:40:14'),
(4, 'Azucar', 'AZC-001', 'Azucares', 2, 50.00, 25.00, 100.0000, 'Azucares S.A', '2026-11-13', 1, '2026-08-24 19:43:36'),
(5, 'Sal', 'SAL-001', 'Sales', 2, 75.00, 10.00, 250.0000, 'Sales S.A', '2026-11-05', 1, '2026-08-24 19:44:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medios_pago`
--

CREATE TABLE `medios_pago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `medios_pago`
--

INSERT INTO `medios_pago` (`id`, `nombre`, `activo`, `created_at`) VALUES
(1, 'efectivo', 1, '2026-08-14 03:22:40'),
(2, 'debito', 1, '2026-08-14 03:22:40'),
(3, 'credito', 1, '2026-08-14 03:22:40'),
(4, 'transferencia', 1, '2026-08-14 03:22:40'),
(5, 'qr', 1, '2026-08-14 03:22:40'),
(6, 'otro', 1, '2026-08-14 03:22:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mermas`
--

CREATE TABLE `mermas` (
  `id` int(11) NOT NULL,
  `tipo` enum('produccion','vencimiento','rotura','exceso','no_vendido','error','otro') NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `ingrediente_id` int(11) DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_medida_id` int(11) NOT NULL,
  `costo_estimado` decimal(10,2) DEFAULT 0.00,
  `fecha` date NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `orden_produccion_id` int(11) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `mermas`
--

INSERT INTO `mermas` (`id`, `tipo`, `producto_id`, `ingrediente_id`, `cantidad`, `unidad_medida_id`, `costo_estimado`, `fecha`, `usuario_id`, `orden_produccion_id`, `motivo`, `observacion`, `created_at`) VALUES
(1, 'produccion', 5, NULL, 12.00, 6, 1200.00, '2026-08-24', 1, NULL, 'Producto quemado.', 'Se quemaron esta tarde al descuidarlas.', '2026-08-24 04:31:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada_compra','salida_produccion','entrada_produccion','salida_venta','ajuste_positivo','ajuste_negativo','merma','devolucion') NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `ingrediente_id` int(11) DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `stock_anterior` decimal(10,2) NOT NULL,
  `stock_nuevo` decimal(10,2) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos_inventario`
--

INSERT INTO `movimientos_inventario` (`id`, `tipo_movimiento`, `producto_id`, `ingrediente_id`, `cantidad`, `stock_anterior`, `stock_nuevo`, `motivo`, `usuario_id`, `referencia_id`, `observacion`, `created_at`) VALUES
(4, 'entrada_compra', NULL, 1, 50.00, 200.00, 250.00, 'Compra - OC-2026080002', 1, 2, 'Recepción de orden de compra', '2026-08-24 04:27:43'),
(5, 'entrada_compra', NULL, 1, 5.00, 10.00, 15.00, 'Compra - OC-2026080003', 1, 3, 'Recepción de orden de compra', '2026-08-24 04:29:24'),
(12, 'salida_venta', 1, NULL, 1.00, 10.00, 9.00, 'debug', 5, 10, 'debug', '2026-09-27 19:20:03'),
(13, 'salida_venta', 2, NULL, 1.00, 10.00, 9.00, 'Venta - VTA-2026090002', 5, 12, 'Venta de 1 unidades de Pan Francés', '2026-09-27 19:20:17'),
(14, 'salida_venta', 14, NULL, 1.00, 123.00, 122.00, 'Venta - VTA-2026090003', 5, 13, 'Venta de 1 unidades de Pan Tostado', '2026-09-27 19:21:37'),
(15, 'salida_venta', 7, NULL, 3.00, 1000000.00, 999997.00, 'Venta - VTA-2026100001', 1, 15, 'Venta de 3 unidades de Pan Blanco', '2026-10-29 14:17:12'),
(16, 'salida_venta', 21, NULL, 1.00, 1.00, 0.00, 'Venta - VTA-2026100002', 1, 16, 'Venta de 1 unidades de Pan Integral Caja Blanca.', '2026-10-29 14:18:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_compra`
--

CREATE TABLE `ordenes_compra` (
  `id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_orden` date NOT NULL,
  `fecha_recepcion` date DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('borrador','pendiente','recibida','parcial','cancelada') DEFAULT 'borrador',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ordenes_compra`
--

INSERT INTO `ordenes_compra` (`id`, `numero`, `proveedor_id`, `usuario_id`, `fecha_orden`, `fecha_recepcion`, `subtotal`, `descuento`, `total`, `estado`, `observaciones`, `created_at`) VALUES
(1, 'OC-2026080001', 1, 1, '2026-08-24', NULL, 1000.00, 0.00, 1000.00, 'cancelada', ' - Cancelada: Cancelada por usuario', '2026-08-24 04:25:04'),
(2, 'OC-2026080002', 1, 1, '2026-08-24', '2026-08-24', 50000.00, 20000.00, 30000.00, 'recibida', 'Descuento del 40%', '2026-08-24 04:27:25'),
(3, 'OC-2026080003', 1, 1, '2026-08-24', '2026-08-24', 5000.00, 0.00, 5000.00, 'recibida', 'Harina Integral.', '2026-08-24 04:29:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_produccion`
--

CREATE TABLE `ordenes_produccion` (
  `id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `receta_id` int(11) NOT NULL,
  `cantidad_planificada` decimal(10,2) NOT NULL,
  `cantidad_producida` decimal(10,2) DEFAULT 0.00,
  `fecha_produccion` date NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `responsable_id` int(11) NOT NULL,
  `estado` enum('planificada','en_preparacion','en_produccion','terminada','cancelada') DEFAULT 'planificada',
  `observaciones` text DEFAULT NULL,
  `costo_total` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_compra_detalles`
--

CREATE TABLE `orden_compra_detalles` (
  `id` int(11) NOT NULL,
  `orden_compra_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,4) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `cantidad_recibida` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `orden_compra_detalles`
--

INSERT INTO `orden_compra_detalles` (`id`, `orden_compra_id`, `ingrediente_id`, `cantidad`, `precio_unitario`, `subtotal`, `cantidad_recibida`, `created_at`) VALUES
(1, 1, 1, 1.00, 1000.0000, 1000.00, 0.00, '2026-08-24 04:25:04'),
(2, 2, 1, 50.00, 1000.0000, 50000.00, 50.00, '2026-08-24 04:27:25'),
(3, 3, 1, 5.00, 1000.0000, 5000.00, 5.00, '2026-08-24 04:29:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `fecha_entrega` date NOT NULL,
  `hora_entrega` time DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `senia` decimal(10,2) DEFAULT 0.00,
  `saldo` decimal(10,2) DEFAULT 0.00,
  `estado` enum('pendiente','confirmado','en_produccion','listo','entregado','cancelado') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_seguimiento`
--

CREATE TABLE `pedido_seguimiento` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `modulo` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `descripcion`, `modulo`, `created_at`) VALUES
(1, 'productos_ver', 'Ver productos', 'productos', '2026-08-14 05:47:57'),
(2, 'productos_crear', 'Crear productos', 'productos', '2026-08-14 05:47:57'),
(3, 'productos_editar', 'Editar productos', 'productos', '2026-08-14 05:47:57'),
(4, 'productos_eliminar', 'Eliminar productos', 'productos', '2026-08-14 05:47:57'),
(5, 'categorias_ver', 'Ver categorías', 'categorias', '2026-08-14 05:47:57'),
(6, 'categorias_crear', 'Crear categorías', 'categorias', '2026-08-14 05:47:57'),
(7, 'categorias_editar', 'Editar categorías', 'categorias', '2026-08-14 05:47:57'),
(8, 'categorias_eliminar', 'Eliminar categorías', 'categorias', '2026-08-14 05:47:57'),
(9, 'ingredientes_ver', 'Ver ingredientes', 'ingredientes', '2026-08-14 05:47:57'),
(10, 'ingredientes_crear', 'Crear ingredientes', 'ingredientes', '2026-08-14 05:47:57'),
(11, 'ingredientes_editar', 'Editar ingredientes', 'ingredientes', '2026-08-14 05:47:57'),
(12, 'ingredientes_eliminar', 'Eliminar ingredientes', 'ingredientes', '2026-08-14 05:47:57'),
(13, 'recetas_ver', 'Ver recetas', 'recetas', '2026-08-14 05:47:57'),
(14, 'recetas_crear', 'Crear recetas', 'recetas', '2026-08-14 05:47:57'),
(15, 'recetas_editar', 'Editar recetas', 'recetas', '2026-08-14 05:47:57'),
(16, 'recetas_eliminar', 'Eliminar recetas', 'recetas', '2026-08-14 05:47:57'),
(17, 'produccion_ver', 'Ver producción', 'produccion', '2026-08-14 05:47:57'),
(18, 'produccion_crear', 'Crear órdenes de producción', 'produccion', '2026-08-14 05:47:57'),
(19, 'produccion_iniciar', 'Iniciar producción', 'produccion', '2026-08-14 05:47:57'),
(20, 'produccion_finalizar', 'Finalizar producción', 'produccion', '2026-08-14 05:47:57'),
(21, 'ventas_ver', 'Ver ventas', 'ventas', '2026-08-14 05:47:57'),
(22, 'ventas_crear', 'Realizar ventas', 'ventas', '2026-08-14 05:47:57'),
(23, 'ventas_cancelar', 'Cancelar ventas', 'ventas', '2026-08-14 05:47:57'),
(24, 'caja_ver', 'Ver caja', 'caja', '2026-08-14 05:47:57'),
(25, 'caja_abrir', 'Abrir caja', 'caja', '2026-08-14 05:47:57'),
(26, 'caja_cerrar', 'Cerrar caja', 'caja', '2026-08-14 05:47:57'),
(27, 'pedidos_ver', 'Ver pedidos', 'pedidos', '2026-08-14 05:47:57'),
(28, 'pedidos_crear', 'Crear pedidos', 'pedidos', '2026-08-14 05:47:57'),
(29, 'pedidos_editar', 'Editar pedidos', 'pedidos', '2026-08-14 05:47:57'),
(30, 'pedidos_cancelar', 'Cancelar pedidos', 'pedidos', '2026-08-14 05:47:57'),
(31, 'compras_ver', 'Ver compras', 'compras', '2026-08-14 05:47:57'),
(32, 'compras_crear', 'Crear órdenes de compra', 'compras', '2026-08-14 05:47:57'),
(33, 'compras_recibir', 'Recibir compras', 'compras', '2026-08-14 05:47:57'),
(34, 'compras_cancelar', 'Cancelar compras', 'compras', '2026-08-14 05:47:57'),
(35, 'proveedores_ver', 'Ver proveedores', 'proveedores', '2026-08-14 05:47:57'),
(36, 'proveedores_crear', 'Crear proveedores', 'proveedores', '2026-08-14 05:47:57'),
(37, 'proveedores_editar', 'Editar proveedores', 'proveedores', '2026-08-14 05:47:57'),
(38, 'proveedores_eliminar', 'Eliminar proveedores', 'proveedores', '2026-08-14 05:47:57'),
(39, 'mermas_ver', 'Ver mermas', 'mermas', '2026-08-14 05:47:57'),
(40, 'mermas_crear', 'Registrar mermas', 'mermas', '2026-08-14 05:47:57'),
(41, 'reportes_ver', 'Ver reportes', 'reportes', '2026-08-14 05:47:57'),
(42, 'usuarios_ver', 'Ver usuarios', 'usuarios', '2026-08-14 05:47:57'),
(43, 'usuarios_crear', 'Crear usuarios', 'usuarios', '2026-08-14 05:47:57'),
(44, 'usuarios_editar', 'Editar usuarios', 'usuarios', '2026-08-14 05:47:57'),
(45, 'usuarios_eliminar', 'Eliminar usuarios', 'usuarios', '2026-08-14 05:47:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `produccion_ingredientes`
--

CREATE TABLE `produccion_ingredientes` (
  `id` int(11) NOT NULL,
  `orden_produccion_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `cantidad_teorica` decimal(10,2) NOT NULL,
  `cantidad_real` decimal(10,2) NOT NULL,
  `cantidad_merma` decimal(10,2) DEFAULT 0.00,
  `unidad_medida_id` int(11) NOT NULL,
  `costo_parcial` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `categoria` varchar(100) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `destacado` tinyint(1) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `categoria_id` int(11) DEFAULT NULL,
  `stock_minimo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `categoria`, `imagen`, `destacado`, `estado`, `created_at`, `categoria_id`, `stock_minimo`) VALUES
(1, 'asd', 'asdasdasdasdasd', 1500.00, 0, 'asd', '', 0, 0, '2026-08-06 14:51:59', NULL, 0),
(2, 'Pan Francés', '', 2.50, 9, 'Panes', '', 1, 0, '2026-08-06 14:54:00', NULL, 0),
(3, 'asdasdasdasd', '', 1.20, 5, NULL, '6a7bb42ac7dbb.jpeg', 0, 0, '2026-08-11 23:45:46', 3, 0),
(4, 'Pan Francés', '', 1.20, 10, NULL, '6a828927e8984.jpg', 0, 0, '2026-08-17 04:08:07', 1, 0),
(5, 'Medialuna', 'Medialunas dulces y saladas.', 100.00, 36, NULL, '6a8bbee70a94a.jpg', 0, 1, '2026-08-24 03:47:51', 3, 0),
(6, 'Alfajores de maicena', 'Alfajores de maicena con Almidón de Maíz', 700.00, 200, NULL, '6a8ca050b8294.jpg', 0, 1, '2026-08-24 19:49:36', 2, 0),
(7, 'Pan Blanco', 'Pan Blanco con Harina 0000', 7217281.00, 999997, NULL, '6a8ca0e6db1ec.jpg', 0, 1, '2026-08-24 19:52:06', 1, 0),
(8, 'Pan Negro', 'Pan Negro con Harina Integral', 450.00, 450, NULL, '6a8ca19e7e0cb.jpg', 0, 1, '2026-08-24 19:54:45', 1, 0),
(9, 'Bizcochos de grasa', 'Bizcochitos con grasa.', 140.00, 190, NULL, '6a8ca200e21d2.jpg', 0, 1, '2026-08-24 19:56:48', 1, 0),
(10, 'Pan2', 'joncinrcnoicniewnfiewnfiniwefnoewnfoiwfoifmwio', 321321.00, 414234, NULL, '', 1, 0, '2026-09-09 01:38:47', 1, 0),
(11, 'dasdas', 'vererrfr3f24ffr3f333', 212.00, 2121, NULL, '', 0, 0, '2026-09-09 01:39:28', 1, 0),
(12, 'Pan2', 'wdcfwecdcrevcrwfewfewfe', 312312.00, 331231, NULL, '', 0, 0, '2026-09-09 01:40:32', 1, 0),
(13, 'uiercvuirejuieuinfcuiernuicfnruifncuirefuruiefncuiernfuierfnruifnciuercfnuerfcuiernfuiernfuiernferiufnernfnrfuierfnuiernruiefnfuirneifnreinfnfuiernfui', 'hola', 121212.00, 213321, NULL, '', 0, 0, '2026-09-09 02:27:26', 1, 0),
(14, 'Pan Tostado', 'Pan tostado', 123.00, 122, NULL, '', 0, 0, '2026-09-09 02:36:25', 1, 0),
(15, 'pan', '', 1.00, -11111, NULL, '', 0, 0, '2026-10-16 23:44:31', 3, 0),
(16, 'Pan Integral Prueba', '', 1500.00, 1, NULL, '', 0, 0, '2026-10-29 12:40:33', NULL, 0),
(17, 'Pan Blanco2121', '', 212121.00, -2000, NULL, '', 0, 0, '2026-10-29 12:48:12', NULL, 0),
(18, 'Pan de Prueba Cero', '', 0.00, 1, NULL, '', 0, 0, '2026-10-29 12:51:27', NULL, 0),
(19, 'Pan de Prueba Stock Negativo.', '', 1500.00, -5, NULL, '', 0, 0, '2026-10-29 12:53:13', NULL, 0),
(20, 'Pan de Prueba Decimal', '', 1500.50, 2, NULL, '', 0, 1, '2026-10-29 12:54:41', NULL, 0),
(21, 'Pan Integral Caja Blanca.', '', 1500.00, 0, NULL, '', 0, 1, '2026-10-29 13:18:13', NULL, 0),
(22, 'nombre válido', '', 1500.00, 2, NULL, '', 0, 1, '2026-10-29 13:23:01', NULL, 0),
(23, 'Pan Integral Caja Blanca 2', '', 1500.00, 2, NULL, '', 0, 1, '2026-10-29 19:11:08', NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `cuit` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `contacto_nombre` varchar(100) DEFAULT NULL,
  `contacto_telefono` varchar(20) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `razon_social`, `cuit`, `telefono`, `email`, `direccion`, `contacto_nombre`, `contacto_telefono`, `observaciones`, `estado`, `created_at`) VALUES
(1, 'Harinas S.A', '20103040780', '1294712658', 'Ejemplo@Ejemplo.com', 'Avenida Suiza 120', 'Luis Perez', '1287461278', 'Proveedor de Harinas S.A', 1, '2026-08-24 04:24:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

CREATE TABLE `recetas` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rendimiento` decimal(10,2) NOT NULL,
  `unidad_rendimiento` varchar(20) NOT NULL,
  `tiempo_preparacion` int(11) DEFAULT 0,
  `tiempo_coccion` int(11) DEFAULT 0,
  `instrucciones` text DEFAULT NULL,
  `costo_total` decimal(10,2) DEFAULT 0.00,
  `costo_por_unidad` decimal(10,2) DEFAULT 0.00,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`id`, `producto_id`, `nombre`, `rendimiento`, `unidad_rendimiento`, `tiempo_preparacion`, `tiempo_coccion`, `instrucciones`, `costo_total`, `costo_por_unidad`, `estado`, `created_at`) VALUES
(1, 5, 'Receta de Medialunas dulces.', 12.00, 'Docena', 20, 30, 'Preparación paso a paso:\r\n\r\n1- Activar la levadura: Mezcla la levadura con una cucharadita de azúcar, una de harina y un chorrito de la leche tibia. Deja reposar 10 minutos hasta que espume.\r\n\r\n2- Formar la masa: Colocá en un bowl la harina y la sal. Hacé un hueco en el centro y agregá el azúcar, el huevo, la esencia de vainilla, la ralladura de limón, la manteca pomada y el fermento de levadura.\r\n\r\n3- Amasar y leudar: Incorporá la leche tibia de a poco. Amasá en la mesada durante 10 minutos hasta lograr un bollo liso y tierno. Tapá la masa y dejala descansar en un lugar cálido hasta que duplique su tamaño (aprox. 1 hora).\r\n\r\n4- Cortar los triángulos: Estirá la masa con un palote en forma rectangular de un espesor de 1 cm. Cortá tiras de unos 10-12 cm de ancho y, luego, cortá triángulos dentro de esas tiras.\r\n\r\n5- Armar las medialunas: Tomá un triángulo, estirá suavemente la punta superior y empezá a enrollar desde la base hacia la punta. Tomá los dos extremos de la base enrollada y doblalos un poco hacia adentro para darle la clásica forma de medialuna.\r\n\r\n6- Segundo leudado: Acomodá las piezas en una placa enmantecada, encimando sutilmente las puntas para que no se abran. Dejalas reposar tapadas hasta que vuelvan a duplicar su tamaño.\r\n\r\n7- Horneado: Pincelá las medialunas con el huevo batido. Cocinalas en horno precalentado a 180°C (fuego medio) por 15 a 20 minutos o hasta que estén bien doradas.\r\n\r\n8- Pincelar con almíbar: Mientras se hornean, herví el agua con el azúcar durante 5 minutos para hacer el almíbar. Apenas las medialunas salgan calientes del horno, pincelalas generosamente con el almíbar a temperatura ambiente.', 500000.00, 41666.67, 0, '2026-08-24 04:01:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta_ingredientes`
--

CREATE TABLE `receta_ingredientes` (
  `id` int(11) NOT NULL,
  `receta_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_medida_id` int(11) NOT NULL,
  `costo_parcial` decimal(10,4) DEFAULT 0.0000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `receta_ingredientes`
--

INSERT INTO `receta_ingredientes` (`id`, `receta_id`, `ingrediente_id`, `cantidad`, `unidad_medida_id`, `costo_parcial`, `created_at`) VALUES
(2, 1, 1, 500.00, 1, 500000.0000, '2026-08-24 04:02:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin'),
(4, 'encargado'),
(2, 'panadero'),
(5, 'produccion'),
(3, 'vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_permisos`
--

CREATE TABLE `roles_permisos` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `roles_permisos`
--

INSERT INTO `roles_permisos` (`id`, `rol_id`, `permiso_id`) VALUES
(1, 1, 25),
(2, 1, 26),
(3, 1, 24),
(4, 1, 6),
(5, 1, 7),
(6, 1, 8),
(7, 1, 5),
(8, 1, 34),
(9, 1, 32),
(10, 1, 33),
(11, 1, 31),
(12, 1, 10),
(13, 1, 11),
(14, 1, 12),
(15, 1, 9),
(16, 1, 40),
(17, 1, 39),
(18, 1, 30),
(19, 1, 28),
(20, 1, 29),
(21, 1, 27),
(22, 1, 18),
(23, 1, 20),
(24, 1, 19),
(25, 1, 17),
(26, 1, 2),
(27, 1, 3),
(28, 1, 4),
(29, 1, 1),
(30, 1, 36),
(31, 1, 37),
(32, 1, 38),
(33, 1, 35),
(34, 1, 14),
(35, 1, 15),
(36, 1, 16),
(37, 1, 13),
(38, 1, 41),
(39, 1, 43),
(40, 1, 44),
(41, 1, 45),
(42, 1, 42),
(43, 1, 23),
(44, 1, 22),
(45, 1, 21),
(64, 2, 25),
(65, 2, 24),
(66, 2, 6),
(67, 2, 7),
(68, 2, 8),
(69, 2, 5),
(70, 2, 32),
(71, 2, 33),
(72, 2, 31),
(73, 2, 10),
(74, 2, 11),
(75, 2, 12),
(76, 2, 9),
(77, 2, 40),
(78, 2, 39),
(79, 2, 28),
(80, 2, 29),
(81, 2, 27),
(82, 2, 18),
(83, 2, 20),
(84, 2, 19),
(85, 2, 17),
(86, 2, 2),
(87, 2, 3),
(88, 2, 4),
(89, 2, 1),
(90, 2, 36),
(91, 2, 37),
(92, 2, 38),
(93, 2, 35),
(94, 2, 14),
(95, 2, 15),
(96, 2, 16),
(97, 2, 13),
(98, 2, 41),
(99, 2, 23),
(100, 2, 22),
(101, 2, 21),
(127, 3, 24),
(128, 3, 28),
(129, 3, 27),
(130, 3, 1),
(131, 3, 22),
(132, 3, 21),
(135, 2, 9),
(136, 2, 40),
(137, 2, 39),
(138, 2, 18),
(139, 2, 20),
(140, 2, 19),
(141, 2, 17),
(142, 2, 1),
(143, 2, 14),
(144, 2, 15),
(145, 2, 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`id`, `nombre`, `abreviatura`, `activo`, `created_at`) VALUES
(1, 'Gramo', 'g', 1, '2026-08-11 23:28:10'),
(2, 'Kilogramo', 'kg', 1, '2026-08-11 23:28:10'),
(3, 'Mililitro', 'ml', 1, '2026-08-11 23:28:10'),
(4, 'Litro', 'l', 1, '2026-08-11 23:28:10'),
(5, 'Unidad', 'ud', 1, '2026-08-11 23:28:10'),
(6, 'Docena', 'doc', 1, '2026-08-11 23:28:10'),
(7, 'Paquete', 'pq', 1, '2026-08-11 23:28:10'),
(8, 'Taza', 'tza', 1, '2026-08-11 23:28:10'),
(9, 'Cucharada', 'cda', 1, '2026-08-11 23:28:10'),
(10, 'Cucharadita', 'cdta', 1, '2026-08-11 23:28:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `ultimo_acceso` datetime DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `telefono`, `password`, `rol_id`, `activo`, `ultimo_acceso`, `foto`, `created_at`) VALUES
(1, 'Administrador', NULL, 'admin@panaderia.com', NULL, '$2y$10$dJ6BEcKevGlQC2WcA3Oh7uQEHACJV/rdWOzhGRD86GVrmI6E1AdfG', 1, 1, NULL, NULL, '2026-08-06 13:53:57'),
(4, 'Juan', 'Pérez', 'encargado@panaderia.com', '987654321', '$2y$10$JE7jLb3kWNIMFhP7ZHZXy.APTgVaHI9WkWZb6HO08k21jkoqRFrcq', 2, 1, NULL, NULL, '2026-08-14 06:19:43'),
(5, 'Carlos', 'Gómez', 'panadero@panaderia.com', '555123456', '$2y$10$gzVu/BEd1AterLwRiNgI3unaCV6BPRtlo8OBvdayqC4vuif1VkKUi', 3, 1, NULL, NULL, '2026-08-14 06:19:43'),
(6, 'María', 'López', 'vendedor@panaderia.com', '555789123', '$2y$10$L7BRngRe.jmCdfOMhv1WeelUnhyY7uV55hKVozR/vblhI1oL8Ps5S', 4, 1, NULL, NULL, '2026-08-14 06:19:43'),
(7, 'Pedro', 'Martínez', 'produccion@panaderia.com', '555456789', '$2y$10$k41asXkYpFqEpyI9q/eoO.jeIFz4cgfRzAwIdXvV/IWvTxt2VSLDW', 5, 1, NULL, NULL, '2026-08-14 06:19:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `medio_pago` enum('efectivo','debito','credito','transferencia','qr','otro') NOT NULL,
  `estado` enum('completada','cancelada','devolucion') DEFAULT 'completada',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `numero`, `cliente_id`, `usuario_id`, `fecha`, `subtotal`, `descuento`, `total`, `medio_pago`, `estado`, `observaciones`) VALUES
(10, 'DBG-TEST-1', NULL, 5, '2026-09-27 16:20:03', 100.00, 0.00, 100.00, 'efectivo', 'completada', 'debug'),
(12, 'VTA-2026090002', NULL, 5, '2026-09-27 16:20:17', 2.50, 0.00, 2.50, 'efectivo', 'completada', 'debug'),
(13, 'VTA-2026090003', NULL, 5, '2026-09-27 16:21:37', 123.00, 0.00, 123.00, 'efectivo', 'completada', ''),
(15, 'VTA-2026100001', NULL, 1, '2026-10-29 11:17:12', 21651843.00, 0.00, 21651843.00, 'efectivo', 'completada', ''),
(16, 'VTA-2026100002', NULL, 1, '2026-10-29 11:18:59', 1500.00, 0.00, 1500.00, 'efectivo', 'completada', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `venta_detalles`
--

INSERT INTO `venta_detalles` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(10, 10, 1, 1.00, 100.00, 100.00),
(12, 12, 2, 1.00, 2.50, 2.50),
(13, 13, 14, 1.00, 123.00, 123.00),
(15, 15, 7, 3.00, 7217281.00, 21651843.00),
(16, 16, 21, 1.00, 1500.00, 1500.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_apertura_id` (`usuario_apertura_id`),
  ADD KEY `usuario_cierre_id` (`usuario_cierre_id`);

--
-- Indices de la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caja_id` (`caja_id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `config_pedidos`
--
ALTER TABLE `config_pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `config_reportes`
--
ALTER TABLE `config_reportes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `historial_precios`
--
ALTER TABLE `historial_precios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`),
  ADD KEY `orden_compra_id` (`orden_compra_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `unidad_medida_id` (`unidad_medida_id`);

--
-- Indices de la tabla `medios_pago`
--
ALTER TABLE `medios_pago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `mermas`
--
ALTER TABLE `mermas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`),
  ADD KEY `unidad_medida_id` (`unidad_medida_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `orden_produccion_id` (`orden_produccion_id`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `ordenes_compra`
--
ALTER TABLE `ordenes_compra`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `receta_id` (`receta_id`),
  ADD KEY `responsable_id` (`responsable_id`);

--
-- Indices de la tabla `orden_compra_detalles`
--
ALTER TABLE `orden_compra_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_compra_id` (`orden_compra_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `pedido_seguimiento`
--
ALTER TABLE `pedido_seguimiento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `produccion_ingredientes`
--
ALTER TABLE `produccion_ingredientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_produccion_id` (`orden_produccion_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`),
  ADD KEY `unidad_medida_id` (`unidad_medida_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cuit` (`cuit`);

--
-- Indices de la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `receta_ingredientes`
--
ALTER TABLE `receta_ingredientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receta_id` (`receta_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`),
  ADD KEY `unidad_medida_id` (`unidad_medida_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `permiso_id` (`permiso_id`);

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `config_pedidos`
--
ALTER TABLE `config_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `config_reportes`
--
ALTER TABLE `config_reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_precios`
--
ALTER TABLE `historial_precios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `medios_pago`
--
ALTER TABLE `medios_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `mermas`
--
ALTER TABLE `mermas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `ordenes_compra`
--
ALTER TABLE `ordenes_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `orden_compra_detalles`
--
ALTER TABLE `orden_compra_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido_seguimiento`
--
ALTER TABLE `pedido_seguimiento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `produccion_ingredientes`
--
ALTER TABLE `produccion_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `recetas`
--
ALTER TABLE `recetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `receta_ingredientes`
--
ALTER TABLE `receta_ingredientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `caja`
--
ALTER TABLE `caja`
  ADD CONSTRAINT `caja_ibfk_1` FOREIGN KEY (`usuario_apertura_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `caja_ibfk_2` FOREIGN KEY (`usuario_cierre_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `caja_movimientos`
--
ALTER TABLE `caja_movimientos`
  ADD CONSTRAINT `caja_movimientos_ibfk_1` FOREIGN KEY (`caja_id`) REFERENCES `caja` (`id`),
  ADD CONSTRAINT `caja_movimientos_ibfk_2` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `caja_movimientos_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `historial_precios`
--
ALTER TABLE `historial_precios`
  ADD CONSTRAINT `historial_precios_ibfk_1` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`),
  ADD CONSTRAINT `historial_precios_ibfk_2` FOREIGN KEY (`orden_compra_id`) REFERENCES `ordenes_compra` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `historial_precios_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD CONSTRAINT `ingredientes_ibfk_1` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidades_medida` (`id`);

--
-- Filtros para la tabla `mermas`
--
ALTER TABLE `mermas`
  ADD CONSTRAINT `mermas_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `mermas_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `mermas_ibfk_3` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidades_medida` (`id`),
  ADD CONSTRAINT `mermas_ibfk_4` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `mermas_ibfk_5` FOREIGN KEY (`orden_produccion_id`) REFERENCES `ordenes_produccion` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `movimientos_inventario_ibfk_3` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ordenes_compra`
--
ALTER TABLE `ordenes_compra`
  ADD CONSTRAINT `ordenes_compra_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  ADD CONSTRAINT `ordenes_compra_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `ordenes_produccion`
--
ALTER TABLE `ordenes_produccion`
  ADD CONSTRAINT `ordenes_produccion_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `ordenes_produccion_ibfk_2` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`),
  ADD CONSTRAINT `ordenes_produccion_ibfk_3` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `orden_compra_detalles`
--
ALTER TABLE `orden_compra_detalles`
  ADD CONSTRAINT `orden_compra_detalles_ibfk_1` FOREIGN KEY (`orden_compra_id`) REFERENCES `ordenes_compra` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orden_compra_detalles_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `pedido_detalles_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedido_seguimiento`
--
ALTER TABLE `pedido_seguimiento`
  ADD CONSTRAINT `pedido_seguimiento_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_seguimiento_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `produccion_ingredientes`
--
ALTER TABLE `produccion_ingredientes`
  ADD CONSTRAINT `produccion_ingredientes_ibfk_1` FOREIGN KEY (`orden_produccion_id`) REFERENCES `ordenes_produccion` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produccion_ingredientes_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`),
  ADD CONSTRAINT `produccion_ingredientes_ibfk_3` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidades_medida` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD CONSTRAINT `recetas_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `receta_ingredientes`
--
ALTER TABLE `receta_ingredientes`
  ADD CONSTRAINT `receta_ingredientes_ibfk_1` FOREIGN KEY (`receta_id`) REFERENCES `recetas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receta_ingredientes_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receta_ingredientes_ibfk_3` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidades_medida` (`id`);

--
-- Filtros para la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD CONSTRAINT `roles_permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_permisos_ibfk_2` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `venta_detalles_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
