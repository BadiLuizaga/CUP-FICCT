<?php $titulo = 'Carreras - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Carreras</h2>
        <p class="text-muted mb-0">Administración de carreras disponibles para admisión FICCT</p>
    </div>

    <div>
        <a href="<?= e(url('/carreras/cupos')) ?>" class="btn btn-outline-primary">
            Administrar cupos
        </a>

        <a href="<?= e(url('/carreras/create')) ?>" class="btn btn-primary">
            Nueva carrera
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Cupo máximo</th>
                        <th>Descripción</th>
                        <th width="220">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($carreras)): ?>
                        <?php foreach ($carreras as $carrera): ?>
                            <tr>
                                <td><?= e($carrera['id']) ?></td>
                                <td><?= e($carrera['codigo']) ?></td>
                                <td><?= e($carrera['nombre']) ?></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= e($carrera['cupo_maximo']) ?> cupos
                                    </span>
                                </td>
                                <td><?= e($carrera['descripcion']) ?></td>
                                <td>
                                    <a
                                        href="<?= e(url('/carreras/show') . '&id=' . $carrera['id']) ?>"
                                        class="btn btn-sm btn-info text-white"
                                    >
                                        Ver
                                    </a>

                                    <a
                                        href="<?= e(url('/carreras/edit') . '&id=' . $carrera['id']) ?>"
                                        class="btn btn-sm btn-warning"
                                    >
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No hay carreras registradas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>