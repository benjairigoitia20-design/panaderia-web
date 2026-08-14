<div class="container mt-4">
    <h2>Nueva Venta</h2>
    
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <form id="formVenta" action="index.php?modulo=ventas&accion=guardar" method="POST">
                <input type="hidden" name="productos" id="productosInput">
                <input type="hidden" name="subtotal" id="subtotalInput">
                <input type="hidden" name="descuento" id="descuentoInput">
                <input type="hidden" name="total" id="totalInput">

                <!-- Buscador de productos -->
                <div class="mb-3">
                    <label for="buscarProducto" class="form-label">Buscar Producto</label>
                    <div class="input-group">
                        <input type="text" id="buscarProducto" class="form-control" placeholder="Escribe el nombre del producto...">
                        <button class="btn btn-primary" type="button" id="btnBuscar">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                    <div id="resultadosBusqueda" class="list-group mt-2" style="display:none;"></div>
                </div>

                <!-- Lista de productos seleccionados -->
                <div class="mb-3">
                    <label class="form-label">Productos en la venta</label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaProductos">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaProductos">
                                <tr id="filaVacia">
                                    <td colspan="5" class="text-center text-muted">No hay productos agregados</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th id="subtotalDisplay">$0.00</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label for="descuentoInputForm" class="form-label">Descuento:</label>
                                        <input type="number" id="descuentoInputForm" class="form-control" step="0.01" value="0" min="0">
                                    </td>
                                    <th colspan="2" class="text-end">Total:</th>
                                    <th id="totalDisplay" class="text-success">$0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Cliente -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <div class="input-group">
                            <select name="cliente_id" id="cliente_id" class="form-select">
                                <option value="">Sin cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-secondary" type="button" id="btnNuevoCliente">Nuevo</button>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="medio_pago" class="form-label">Medio de Pago *</label>
                        <select name="medio_pago" id="medio_pago" class="form-select" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="debito">Débito</option>
                            <option value="credito">Crédito</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="qr">QR</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <input type="text" name="observaciones" id="observaciones" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg" id="btnGuardarVenta" disabled>
                    <i class="bi bi-cash"></i> Cobrar Venta
                </button>
                <a href="index.php?modulo=ventas&accion=index" class="btn btn-secondary btn-lg">Cancelar</a>
            </form>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Resumen</h5>
                </div>
                <div class="card-body">
                    <div id="resumenVenta">
                        <p class="text-muted">Agrega productos para ver el resumen</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let productos = [];
let subtotal = 0;
let descuento = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Buscar productos
    document.getElementById('btnBuscar').addEventListener('click', buscarProductos);
    document.getElementById('buscarProducto').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarProductos();
    });

    // Descuento
    document.getElementById('descuentoInputForm').addEventListener('input', function() {
        descuento = parseFloat(this.value) || 0;
        actualizarTotales();
    });

    // Nuevo cliente
    document.getElementById('btnNuevoCliente').addEventListener('click', function() {
        // Simplificado - redirigir a creación de cliente
        alert('Función de creación de cliente - Implementar en futura fase');
    });
});

function buscarProductos() {
    const termino = document.getElementById('buscarProducto').value.trim();
    if (termino.length < 2) {
        alert('Escribe al menos 2 caracteres para buscar.');
        return;
    }

    fetch('index.php?modulo=ventas&accion=buscarProductos&termino=' + encodeURIComponent(termino))
        .then(response => response.json())
        .then(data => {
            const resultados = document.getElementById('resultadosBusqueda');
            resultados.style.display = 'block';
            resultados.innerHTML = '';
            
            if (data.length === 0) {
                resultados.innerHTML = '<div class="list-group-item text-muted">No se encontraron productos</div>';
                return;
            }

            data.forEach(producto => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${producto.nombre}</strong>
                            <br>
                            <small>Stock: ${producto.stock} | $${producto.precio}</small>
                        </div>
                        <button class="btn btn-sm btn-primary agregar-producto" data-id="${producto.id}" data-nombre="${producto.nombre}" data-precio="${producto.precio}" data-stock="${producto.stock}">
                            Agregar
                        </button>
                    </div>
                `;
                resultados.appendChild(item);
            });

            // Agregar event listeners a los botones
            document.querySelectorAll('.agregar-producto').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    agregarProducto(
                        parseInt(this.dataset.id),
                        this.dataset.nombre,
                        parseFloat(this.dataset.precio),
                        parseInt(this.dataset.stock)
                    );
                    document.getElementById('resultadosBusqueda').style.display = 'none';
                    document.getElementById('buscarProducto').value = '';
                });
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al buscar productos');
        });
}

function agregarProducto(id, nombre, precio, stock) {
    // Verificar si ya está en la lista
    const existe = productos.find(p => p.producto_id === id);
    if (existe) {
        if (existe.cantidad < stock) {
            existe.cantidad++;
            existe.subtotal = existe.cantidad * existe.precio_unitario;
        } else {
            alert('No hay suficiente stock disponible.');
            return;
        }
    } else {
        if (stock <= 0) {
            alert('Producto sin stock disponible.');
            return;
        }
        productos.push({
            producto_id: id,
            nombre: nombre,
            precio_unitario: precio,
            cantidad: 1,
            subtotal: precio
        });
    }
    
    renderizarProductos();
    actualizarTotales();
}

function renderizarProductos() {
    const tbody = document.getElementById('listaProductos');
    tbody.innerHTML = '';
    
    if (productos.length === 0) {
        tbody.innerHTML = '<tr id="filaVacia"><td colspan="5" class="text-center text-muted">No hay productos agregados</td></tr>';
        document.getElementById('btnGuardarVenta').disabled = true;
        return;
    }
    
    document.getElementById('btnGuardarVenta').disabled = false;
    
    productos.forEach((p, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${p.nombre}</td>
            <td>
                <div class="input-group">
                    <button class="btn btn-sm btn-outline-secondary cambiar-cantidad" data-index="${index}" data-cambio="-1">-</button>
                    <input type="number" class="form-control form-control-sm cantidad-input" data-index="${index}" value="${p.cantidad}" min="1" style="width:60px;">
                    <button class="btn btn-sm btn-outline-secondary cambiar-cantidad" data-index="${index}" data-cambio="1">+</button>
                </div>
            </td>
            <td>$${p.precio_unitario.toFixed(2)}</td>
            <td>$${p.subtotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-danger eliminar-producto" data-index="${index}">✕</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Event listeners para cantidad
    document.querySelectorAll('.cambiar-cantidad').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            const cambio = parseInt(this.dataset.cambio);
            const nuevo = productos[index].cantidad + cambio;
            if (nuevo > 0) {
                productos[index].cantidad = nuevo;
                productos[index].subtotal = nuevo * productos[index].precio_unitario;
                renderizarProductos();
                actualizarTotales();
            }
        });
    });

    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.addEventListener('change', function() {
            const index = parseInt(this.dataset.index);
            const cantidad = parseInt(this.value) || 1;
            if (cantidad > 0) {
                productos[index].cantidad = cantidad;
                productos[index].subtotal = cantidad * productos[index].precio_unitario;
                actualizarTotales();
            }
            renderizarProductos();
        });
    });

    document.querySelectorAll('.eliminar-producto').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            productos.splice(index, 1);
            renderizarProductos();
            actualizarTotales();
        });
    });
}

function actualizarTotales() {
    subtotal = productos.reduce((sum, p) => sum + p.subtotal, 0);
    const total = subtotal - descuento;
    
    document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('totalDisplay').textContent = '$' + Math.max(0, total).toFixed(2);
    
    // Actualizar campos ocultos
    document.getElementById('subtotalInput').value = subtotal;
    document.getElementById('descuentoInput').value = descuento;
    document.getElementById('totalInput').value = Math.max(0, total);
    document.getElementById('productosInput').value = JSON.stringify(productos);
    
    // Actualizar resumen
    const resumen = document.getElementById('resumenVenta');
    if (productos.length === 0) {
        resumen.innerHTML = '<p class="text-muted">Agrega productos para ver el resumen</p>';
    } else {
        let html = '<div class="list-group">';
        productos.forEach(p => {
            html += `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    ${p.nombre} x ${p.cantidad}
                    <span class="badge bg-primary rounded-pill">$${p.subtotal.toFixed(2)}</span>
                </div>
            `;
        });
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                Subtotal
                <span>$${subtotal.toFixed(2)}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                Descuento
                <span class="text-danger">-$${descuento.toFixed(2)}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center fw-bold text-success">
                Total
                <span>$${Math.max(0, total).toFixed(2)}</span>
            </div>
        `;
        resumen.innerHTML = html;
    }
}
</script>