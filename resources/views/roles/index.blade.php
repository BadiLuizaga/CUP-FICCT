<?php $titulo = 'Roles - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Roles</h2>
        <p class="text-muted mb-0">Gestión de roles del sistema</p>
    </div>

    <a href="<?= e(url('/roles/create')) ?>" class="btn btn-primary">
        Nuevo rol
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Usuarios asignados</th>
                        <th width="120">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $rol): ?>
                            <tr>
                                <td><?= e($rol['id']) ?></td>
                                <td><?= e($rol['nombre']) ?></td>
                                <td><?= e($rol['descripcion']) ?></td>
                                <td><?= e($rol['total_usuarios'] ?? 0) ?></td>
                                <td>
                                    <a 
                                        href="<?= e(url('/roles/edit') . '&id=' . $rol['id']) ?>" 
                                        class="btn btn-sm btn-warning"
                                    >
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No hay roles registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>