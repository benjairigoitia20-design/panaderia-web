<div class="container mt-4">
    <h2>Mi Perfil</h2>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">
                        <?= strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1)) ?>
                    </div>
                </div>
                <div class="col-md-9">
                    <h4><?= htmlspecialchars(trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''))) ?></h4>
                    <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
                    <p class="mb-2"><strong>Rol:</strong> <?= htmlspecialchars($usuario['rol_nombre'] ?? 'Sin rol') ?></p>
                    <p class="mb-2"><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono'] ?? '-') ?></p>
                    <p class="mb-0"><strong>Estado:</strong>
                        <span class="badge <?= $usuario['activo'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
