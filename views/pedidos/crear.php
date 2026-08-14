<div class="container mt-4">
    <h2>Nuevo Pedido</h2>
    
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <form id="formPedido" action="index.php?modulo=pedidos&accion=guardar" method="POST">
                <input type="hidden" name="productos" id="productosInput">
                <input type="hidden" name="subtotal" id="subtotalInput">
                <input type="hidden" name="descuento" id="descuentoInput">
                <input type="hidden" name="total" id="totalInput">

                <!-- Cliente -->
                <div class="mb-3">
                    <label for="cliente_id" class="form-label">Cliente *</label>
                    <div class="input-group">
                        <select name="cliente_id" id="cliente_id" class="form-select" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id'] ?>">
                                    <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?>
                                    <?php if ($cliente['telefono']): ?>
                                        - <?= htmlspecialchars($cliente['telefono']) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-secondary" type="button" id="btnNuevoCliente" data-bs-toggle="modal" data-bs-target="#modalCliente">
                            <i class="bi bi-plus"></i> Nuevo
                        </button>
                    </div>
                </div>

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
                    <label class="form-label">Productos del pedido</label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaProductos">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Observaciones</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="listaProductos">
                                <tr id="filaVacia">
                                    <td colspan="6" class="text-center text-muted">No hay productos agregados</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal:</th>
                                    <th id="subtotalDisplay">$0.00</th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label for="descuentoInputForm" class="form-label">Descuento:</label>
                                        <input type="number" id="descuentoInputForm" class="form-control" step="0.01" value="0" min="0">
                                    </td>
                                    <td colspan="2">
                                        <label for="seniaInput" class="form-label">Seña:</label>
                                        <input type="number" id="seniaInput" class="form-control" step="0.01" value="0" min="0">
                                    </td>
                                    <th colspan="2" class="text-end">Total:</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Total del Pedido:</th>
                                    <th id="totalDisplay" class="text-success">$0.00</th>
                                    <th colspan="2">
                                        <span id="saldoDisplay" class="text-danger">Saldo: $0.00</span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Fecha y hora de entrega -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fecha_entrega" class="form-label">Fecha de Entrega *</label>
                        <input type="date" name="fecha_entrega" id="fecha_entrega" class="form-control" 
                               value="<?= date('Y-m-d', strtotime('+2 days')) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="hora_entrega" class="form-label">Hora de Entrega</label>
                        <input type="time" name="hora_entrega" id="hora_entrega" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <input type="text" name="observaciones" id="observaciones" class="form-control" placeholder="Ej: Decoración especial">
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg" id="btnGuardarPedido" disabled>
                    <i class="bi bi-check-circle"></i> Crear Pedido
                </button>
                <a href="index.php?modulo=pedidos&accion=index" class="btn btn-secondary btn-lg">Cancelar</a>
            </form>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Resumen del Pedido</h5>
                </div>
                <div class="card-body">
                    <div id="resumenPedido">
                        <p class="text-muted">Agrega productos para ver el resumen</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nuevo cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCliente">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cli_nombre" class="form-label">Nombre *</label>
                            <input type="text" id="cli_nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cli_apellido" class="form-label">Apellido</label>
                            <input type="text" id="cli_apellido" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="cli_telefono" class="form-label">Teléfono</label>
                        <input type="text" id="cli_telefono" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="cli_email" class="form-label">Email</label>
                        <input type="email" id="cli_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="cli_direccion" class="form-label">Dirección</label>
                        <textarea id="cli_direccion" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCliente">Guardar Cliente</button>
            </div>
        </div>
    </div>
</div>

<script>
let productos = [];
let subtotal = 0;
let descuento = 0;
let senia = 0;

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

    // Seña
    document.getElementById('seniaInput').addEventListener('input', function() {
        senia = parseFloat(this.value) || 0;
        actualizarTotales();
    });

    // Guardar cliente
    document.getElementById('btnGuardarCliente').addEventListener('click', function() {
        const nombre = document.getElementById('cli_nombre').value.trim();
        if (!nombre) {
            alert('El nombre es obligatorio.');
            return;
        }

        const datos = {
            nombre: nombre,
            apellido: document.getElementById('cli_apellido').value.trim(),
            telefono: document.getElementById('cli_telefono').value.trim(),
            email: document.getElementById('cli_email').value.trim(),
            direccion: document.getElementById('cli_direccion').value.trim()
        };

        fetch('index.php?modulo=clientes&accion=guardar_ajax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('cliente_id');
                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = data.nombre + ' ' + (data.apellido || '');
                select.appendChild(option);
                select.value = data.id;
                document.getElementById('modalCliente').querySelector('.btn-close').click();
                alert('Cliente creado correctamente.');
            } else {
                alert('Error al crear el cliente.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al crear el cliente.');
        });
    });
});

function buscarProductos() {
    const termino = document.getElementById('buscarProducto').value.trim();
    if (termino.length < 2) {
        alert('Escribe al menos 2 caracteres para buscar.');
        return;
    }

    fetch('index.php?modulo=pedidos&accion=buscarProductos&termino=' + encodeURIComponent(termino))
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
            subtotal: precio,
            observaciones: ''
        });
    }
    
    renderizarProductos();
    actualizarTotales();
}

function renderizarProductos() {
    const tbody = document.getElementById('listaProductos');
    tbody.innerHTML = '';
    
    if (productos.length === 0) {
        tbody.innerHTML = '<tr id="filaVacia"><td colspan="6" class="text-center text-muted">No hay productos agregados</td></tr>';
        document.getElementById('btnGuardarPedido').disabled = true;
        return;
    }
    
    document.getElementById('btnGuardarPedido').disabled = false;
    
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
                <input type="text" class="form-control form-control-sm obs-input" data-index="${index}" placeholder="Observaciones" value="${p.observaciones || ''}">
            </td>
            <td>
                <button class="btn btn-sm btn-danger eliminar-producto" data-index="${index}">✕</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Event listeners
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

    document.querySelectorAll('.obs-input').forEach(input => {
        input.addEventListener('change', function() {
            const index = parseInt(this.dataset.index);
            productos[index].observaciones = this.value;
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
    const saldo = total - senia;
    
    document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('totalDisplay').textContent = '$' + Math.max(0, total).toFixed(2);
    document.getElementById('saldoDisplay').textContent = 'Saldo: $' + Math.max(0, saldo).toFixed(2);
    
    document.getElementById('subtotalInput').value = subtotal;
    document.getElementById('descuentoInput').value = descuento;
    document.getElementById('totalInput').value = Math.max(0, total);
    document.getElementById('productosInput').value = JSON.stringify(productos);
    
    // Resumen
    const resumen = document.getElementById('resumenPedido');
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
            <div class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                Total
                <span class="text-success">$${Math.max(0, total).toFixed(2)}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                Seña
                <span class="text-info">$${senia.toFixed(2)}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center fw-bold ${saldo > 0 ? 'text-danger' : 'text-success'}">
                Saldo
                <span>$${Math.max(0, saldo).toFixed(2)}</span>
            </div>
        `;
        resumen.innerHTML = html;
    }
}
</script>