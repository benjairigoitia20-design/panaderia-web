<?php
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../includes/funciones.php';

class CajaController {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('encargado') && !tieneRol('vendedor')) {
            setMensaje('danger', 'No tienes permiso para esta acción.');
            redirigir('index.php?modulo=caja&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        
        // Obtener caja actual
        $stmt = $this->pdo->query("SELECT * FROM caja WHERE estado = 'abierta' ORDER BY id DESC LIMIT 1");
        $caja_actual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener movimientos de caja actual
        $movimientos = [];
        if ($caja_actual) {
            $sql = "SELECT cm.*, u.nombre as usuario_nombre, v.numero as venta_numero
                    FROM caja_movimientos cm
                    LEFT JOIN usuarios u ON cm.usuario_id = u.id
                    LEFT JOIN ventas v ON cm.venta_id = v.id
                    WHERE cm.caja_id = ?
                    ORDER BY cm.created_at DESC
                    LIMIT 50";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$caja_actual['id']]);
            $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Obtener historial de cierres
        $sql = "SELECT c.*, ua.nombre as usuario_apertura, uc.nombre as usuario_cierre
                FROM caja c
                LEFT JOIN usuarios ua ON c.usuario_apertura_id = ua.id
                LEFT JOIN usuarios uc ON c.usuario_cierre_id = uc.id
                WHERE c.estado = 'cerrada'
                ORDER BY c.fecha_cierre DESC
                LIMIT 10";
        $stmt = $this->pdo->query($sql);
        $cierres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'includes/header.php';
        include 'views/caja/index.php';
        include 'includes/footer.php';
    }

    public function abrir() {
        $this->verificarPermiso(true);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=caja&accion=index');
        }

        // Verificar si ya hay caja abierta
        $stmt = $this->pdo->query("SELECT id FROM caja WHERE estado = 'abierta' LIMIT 1");
        if ($stmt->fetch()) {
            setMensaje('danger', 'Ya hay una caja abierta. Debes cerrarla primero.');
            redirigir('index.php?modulo=caja&accion=index');
        }

        $monto_inicial = floatval($_POST['monto_inicial'] ?? 0);
        if ($monto_inicial < 0) {
            setMensaje('danger', 'El monto inicial no puede ser negativo.');
            redirigir('index.php?modulo=caja&accion=index');
        }

        $sql = "INSERT INTO caja (usuario_apertura_id, monto_inicial, estado, observaciones) 
                VALUES (?, ?, 'abierta', ?)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$_SESSION['usuario_id'], $monto_inicial, sanitizar($_POST['observaciones'] ?? '')])) {
            setMensaje('success', 'Caja abierta correctamente.');
        } else {
            setMensaje('danger', 'Error al abrir la caja.');
        }
        
        redirigir('index.php?modulo=caja&accion=index');
    }

    public function cerrar() {
        $this->verificarPermiso(true);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=caja&accion=index');
        }

        // Obtener caja actual
        $stmt = $this->pdo->query("SELECT * FROM caja WHERE estado = 'abierta' ORDER BY id DESC LIMIT 1");
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$caja) {
            setMensaje('danger', 'No hay caja abierta.');
            redirigir('index.php?modulo=caja&accion=index');
        }

        $monto_contado = floatval($_POST['monto_contado'] ?? 0);
        if ($monto_contado < 0) {
            setMensaje('danger', 'El monto contado no puede ser negativo.');
            redirigir('index.php?modulo=caja&accion=index');
        }

        // Calcular monto esperado
        $sql = "SELECT SUM(monto) as total_ingresos FROM caja_movimientos WHERE caja_id = ? AND tipo IN ('venta', 'ingreso')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$caja['id']]);
        $total_ingresos = $stmt->fetch(PDO::FETCH_ASSOC)['total_ingresos'] ?? 0;
        
        $sql = "SELECT SUM(monto) as total_egresos FROM caja_movimientos WHERE caja_id = ? AND tipo IN ('egreso', 'retiro')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$caja['id']]);
        $total_egresos = $stmt->fetch(PDO::FETCH_ASSOC)['total_egresos'] ?? 0;
        
        $monto_esperado = $caja['monto_inicial'] + $total_ingresos - $total_egresos;
        $diferencia = $monto_contado - $monto_esperado;

        // Cerrar caja
        $sql = "UPDATE caja SET 
                usuario_cierre_id = ?, fecha_cierre = NOW(), 
                monto_esperado = ?, monto_contado = ?, diferencia = ?, 
                estado = 'cerrada', observaciones = CONCAT(observaciones, ' - Cierre: ', ?) 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            $_SESSION['usuario_id'],
            $monto_esperado,
            $monto_contado,
            $diferencia,
            sanitizar($_POST['observaciones'] ?? ''),
            $caja['id']
        ]);

        if ($result) {
            $mensaje = "Caja cerrada correctamente. ";
            $mensaje .= "Monto esperado: $" . number_format($monto_esperado, 2) . ". ";
            $mensaje .= "Monto contado: $" . number_format($monto_contado, 2) . ". ";
            $mensaje .= "Diferencia: $" . number_format($diferencia, 2);
            setMensaje('success', $mensaje);
        } else {
            setMensaje('danger', 'Error al cerrar la caja.');
        }
        
        redirigir('index.php?modulo=caja&accion=index');
    }
}
?>