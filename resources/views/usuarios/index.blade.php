<?php $titulo = 'Usuarios - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Usuarios</h2>
        <p class="text-muted mb-0">Gestión de usuarios del sistema</p>
    </div>

    <a href="<?= e(url('/usuarios/create')) ?>" class="btn btn-primary">
        Nuevo usuario
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>CI</th>
                        <th>Nombre completo</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último acceso</th>
                        <th width="180">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= e($usuario['id']) ?></td>
                                <td><?= e($usuario['codigo']) ?></td>
                                <td><?= e($usuario['ci']) ?></td>
                                <td><?= e(trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''))) ?></td>
                                <td><?= e($usuario['username']) ?></td>
                                <td><?= e($usuario['roles']) ?></td>
                                <td>
                                    <?php if (is_active($usuario['estado'])): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e(format_date($usuario['ultimo_acceso'])) ?></td>
                                <td>
                                    <a 
                                        href="<?= e(url('/usuarios/edit') . '&id=' . $usuario['id']) ?>" 
                                        class="btn btn-sm btn-warning"
                                    >
                                        Editar
                                    </a>

                                    <?php if ((int)($_SESSION['usuario']['id'] ?? 0) !== (int)$usuario['id']): ?>
                                        <a 
                                            href="<?= e(url('/usuarios/cambiar-estado') . '&id=' . $usuario['id']) ?>" 
                                            class="btn btn-sm <?= is_active($usuario['estado']) ? 'btn-secondary' : 'btn-success' ?>"
                                            onclick="return confirm('¿Seguro que deseas cambiar el estado de este usuario?')"
                                        >
                                            <?= is_active($usuario['estado']) ? 'Inactivar' : 'Activar' ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>