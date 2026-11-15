<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Venta rápida</h2>
            <small class="text-muted">Panel de ventas</small>
        </div>
        <a href="index.php?modulo=ventas&accion=index" class="btn btn-outline-secondary">Volver</a>
    </div>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form id="formVenta" action="index.php?modulo=ventas&accion=guardar" method="POST">
        <input type="hidden" name="productos" id="productosInput">
        <input type="hidden" name="subtotal" id="subtotalInput">
        <input type="hidden" name="descuento" id="descuentoInput">
        <input type="hidden" name="total" id="totalInput">
        <input type="hidden" name="cliente_id" value="">

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="buscarProducto" class="form-label fw-semibold">Buscar producto</label>
                            <div class="input-group">
                                <input type="text" id="buscarProducto" class="form-control" placeholder="Escribe el nombre del producto...">
                                <button class="btn btn-primary" type="button" id="btnBuscar">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                            <div id="resultadosBusqueda" class="list-group mt-2" style="display:none;"></div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tablaProductos">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th style="width: 170px;">Cantidad</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>
                                        <th style="width: 80px;">Quitar</th>
                                    </tr>
                                </thead>
                                <tbody id="listaProductos">
                                    <tr id="filaVacia">
                                        <td colspan="5" class="text-center text-muted py-4">Todavía no agregaste productos.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Resumen</h5>

                        <div class="mb-3">
                            <label for="descuentoInputForm" class="form-label">Descuento</label>
                            <input type="number" id="descuentoInputForm" class="form-control" step="0.01" value="0" min="0">
                        </div>

                        <div class="mb-3">
                            <label for="medio_pago" class="form-label">Pago</label>
                            <select name="medio_pago" id="medio_pago" class="form-select" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="debito">Débito</option>
                                <option value="credito">Crédito</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="qr">QR</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observación</label>
                            <input type="text" name="observaciones" id="observaciones" class="form-control" placeholder="Ej: sin azúcar, entrega hoy">
                        </div>

                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <strong id="subtotalDisplay">$0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Descuento</span>
                                <strong id="descuentoDisplay">-$0.00</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center text-success fs-5">
                                <span>Total</span>
                                <strong id="totalDisplay">$0.00</strong>
                            </div>
                        </div>

                        <div id="resumenVenta" class="mb-3 small text-muted">
                            Agrega productos para ver el detalle.
                        </div>

                        <button type="submit" class="btn btn-success w-100 btn-lg" id="btnGuardarVenta" disabled>
                            <i class="bi bi-cash"></i> Cobrar venta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let productos = [];
let subtotal = 0;
let descuento = 0;

document.addEventListener('DOMContentLoaded', function() {
    const inputBuscar = document.getElementById('buscarProducto');
    const botonBuscar = document.getElementById('btnBuscar');

    botonBuscar.addEventListener('click', buscarProductos);

    inputBuscar.addEventListener('input', function() {
        const termino = inputBuscar.value.trim();
        if (termino.length >= 2) {
            buscarProductos();
        } else {
            const resultados = document.getElementById('resultadosBusqueda');
            resultados.innerHTML = '';
            resultados.style.display = 'none';
        }
    });

    inputBuscar.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarProductos();
        }
    });

    document.getElementById('descuentoInputForm').addEventListener('input', function() {
        descuento = parseFloat(this.value) || 0;
        actualizarTotales();
    });
});

function buscarProductos() {
    const termino = document.getElementById('buscarProducto').value.trim();
    if (termino.length < 2) {
        const resultados = document.getElementById('resultadosBusqueda');
        resultados.innerHTML = '<div class="list-group-item text-muted">Escribe al menos 2 caracteres.</div>';
        resultados.style.display = 'block';
        return;
    }

    fetch('index.php?modulo=ventas&accion=buscarProductos&termino=' + encodeURIComponent(termino))
        .then(response => response.json())
        .then(data => {
            const resultados = document.getElementById('resultadosBusqueda');
            resultados.style.display = 'block';
            resultados.innerHTML = '';

            if (data.length === 0) {
                resultados.innerHTML = '<div class="list-group-item text-muted">No se encontraron productos.</div>';
                return;
            }

            data.forEach(producto => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <strong>${producto.nombre}</strong><br>
                            <small class="text-muted">Stock: ${producto.stock} · $${producto.precio}</small>
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
        tbody.innerHTML = '<tr id="filaVacia"><td colspan="5" class="text-center text-muted py-4">Todavía no agregaste productos.</td></tr>';
        document.getElementById('btnGuardarVenta').disabled = true;
        return;
    }

    document.getElementById('btnGuardarVenta').disabled = false;

    productos.forEach((p, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${p.nombre}</td>
            <td>
                <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary cambiar-cantidad" type="button" data-index="${index}" data-cambio="-1">-</button>
                    <input type="number" class="form-control cantidad-input" data-index="${index}" value="${p.cantidad}" min="1" style="max-width: 80px;">
                    <button class="btn btn-outline-secondary cambiar-cantidad" type="button" data-index="${index}" data-cambio="1">+</button>
                </div>
            </td>
            <td>$${p.precio_unitario.toFixed(2)}</td>
            <td>$${p.subtotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-danger eliminar-producto" type="button" data-index="${index}">✕</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

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
            }
            renderizarProductos();
            actualizarTotales();
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
    const total = Math.max(0, subtotal - descuento);

    document.getElementById('subtotalInput').value = subtotal;
    document.getElementById('descuentoInput').value = descuento;
    document.getElementById('totalInput').value = total;
    document.getElementById('productosInput').value = JSON.stringify(productos);

    document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('descuentoDisplay').textContent = '-$' + descuento.toFixed(2);
    document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);

    const resumen = document.getElementById('resumenVenta');
    if (productos.length === 0) {
        resumen.innerHTML = 'Agrega productos para ver el detalle.';
        return;
    }

    let html = '';
    productos.forEach(p => {
        html += `<div class="d-flex justify-content-between mb-2"><span>${p.nombre} x ${p.cantidad}</span><strong>$${p.subtotal.toFixed(2)}</strong></div>`;
    });
    resumen.innerHTML = html;
}
</script>