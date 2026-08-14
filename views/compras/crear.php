<div class="container mt-4">
    <h2>Nueva Orden de Compra</h2>
    
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <form id="formCompra" action="index.php?modulo=compras&accion=guardar" method="POST">
                <input type="hidden" name="productos" id="productosInput">
                <input type="hidden" name="subtotal" id="subtotalInput">
                <input type="hidden" name="descuento" id="descuentoInput">
                <input type="hidden" name="total" id="totalInput">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="proveedor_id" class="form-label">Proveedor *</label>
                        <select name="proveedor_id" id="proveedor_id" class="form-select" required>
                            <option value="">Seleccionar proveedor...</option>
                            <?php foreach ($proveedores as $proveedor): ?>
                                <option value="<?= $proveedor['id'] ?>"><?= htmlspecialchars($proveedor['razon_social']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_orden" class="form-label">Fecha de Orden *</label>
                        <input type="date" name="fecha_orden" id="fecha_orden" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <!-- Buscador de ingredientes -->
                <div class="mb-3">
                    <label for="buscarIngrediente" class="form-label">Buscar Ingrediente</label>
                    <div class="input-group">
                        <input type="text" id="buscarIngrediente" class="form-control" placeholder="Escribe el nombre del ingrediente...">
                        <button class="btn btn-primary" type="button" id="btnBuscar">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                    <div id="resultadosBusqueda" class="list-group mt-2" style="display:none;"></div>
                </div>

                <!-- Lista de productos -->
                <div class="mb-3">
                    <label class="form-label">Productos de la compra</label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaProductos">
                            <thead class="table-light">
                                <tr>
                                    <th>Ingrediente</th>
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

                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="2" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-success btn-lg" id="btnGuardarCompra" disabled>
                    <i class="bi bi-check-circle"></i> Crear Orden
                </button>
                <a href="index.php?modulo=compras&accion=index" class="btn btn-secondary btn-lg">Cancelar</a>
            </form>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Resumen</h5>
                </div>
                <div class="card-body">
                    <div id="resumenCompra">
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
    document.getElementById('btnBuscar').addEventListener('click', buscarIngredientes);
    document.getElementById('buscarIngrediente').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') buscarIngredientes();
    });

    document.getElementById('descuentoInputForm').addEventListener('input', function() {
        descuento = parseFloat(this.value) || 0;
        actualizarTotales();
    });
});

function buscarIngredientes() {
    const termino = document.getElementById('buscarIngrediente').value.trim();
    if (termino.length < 2) {
        alert('Escribe al menos 2 caracteres para buscar.');
        return;
    }

    fetch('index.php?modulo=compras&accion=buscarIngredientes&termino=' + encodeURIComponent(termino))
        .then(response => response.json())
        .then(data => {
            const resultados = document.getElementById('resultadosBusqueda');
            resultados.style.display = 'block';
            resultados.innerHTML = '';
            
            if (data.length === 0) {
                resultados.innerHTML = '<div class="list-group-item text-muted">No se encontraron ingredientes</div>';
                return;
            }

            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'list-group-item';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${item.nombre}</strong>
                            <br>
                            <small>Costo: $${item.costo_unitario} | Stock: ${item.stock_actual} ${item.unidad_abreviatura}</small>
                        </div>
                        <button class="btn btn-sm btn-primary agregar-producto" data-id="${item.id}" data-nombre="${item.nombre}" data-precio="${item.costo_unitario}">
                            Agregar
                        </button>
                    </div>
                `;
                resultados.appendChild(div);
            });

            document.querySelectorAll('.agregar-producto').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    agregarProducto(
                        parseInt(this.dataset.id),
                        this.dataset.nombre,
                        parseFloat(this.dataset.precio)
                    );
                    document.getElementById('resultadosBusqueda').style.display = 'none';
                    document.getElementById('buscarIngrediente').value = '';
                });
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al buscar ingredientes');
        });
}

function agregarProducto(id, nombre, precio) {
    const existe = productos.find(p => p.ingrediente_id === id);
    if (existe) {
        existe.cantidad++;
        existe.subtotal = existe.cantidad * existe.precio_unitario;
    } else {
        productos.push({
            ingrediente_id: id,
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
        document.getElementById('btnGuardarCompra').disabled = true;
        return;
    }
    
    document.getElementById('btnGuardarCompra').disabled = false;
    
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
            <td>
                <input type="number" step="0.0001" class="form-control form-control-sm precio-input" data-index="${index}" value="${p.precio_unitario}">
            </td>
            <td>$${p.subtotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-danger eliminar-producto" data-index="${index}">✕</button>
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
                actualizarTotales();
            }
            renderizarProductos();
        });
    });

    document.querySelectorAll('.precio-input').forEach(input => {
        input.addEventListener('change', function() {
            const index = parseInt(this.dataset.index);
            const precio = parseFloat(this.value) || 0;
            if (precio >= 0) {
                productos[index].precio_unitario = precio;
                productos[index].subtotal = productos[index].cantidad * precio;
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
    
    document.getElementById('subtotalInput').value = subtotal;
    document.getElementById('descuentoInput').value = descuento;
    document.getElementById('totalInput').value = Math.max(0, total);
    document.getElementById('productosInput').value = JSON.stringify(productos);
    
    const resumen = document.getElementById('resumenCompra');
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