<?php $titulo = 'Detalle de Carrera - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Detalle de carrera</h2>
        <p class="text-muted mb-0">Información registrada de la carrera seleccionada</p>
    </div>

    <a href="<?= e(url('/carreras')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered align-middle">
            <tr>
                <th width="220">ID</th>
                <td><?= e($carrera['id']) ?></td>
            </tr>

            <tr>
                <th>Código</th>
                <td><?= e($carrera['codigo']) ?></td>
            </tr>

            <tr>
                <th>Nombre</th>
                <td><?= e($carrera['nombre']) ?></td>
            </tr>

            <tr>
                <th>Cupo máximo</th>
                <td>
                    <span class="badge bg-primary">
                        <?= e($carrera['cupo_maximo']) ?> cupos
                    </span>
                </td>
            </tr>

            <tr>
                <th>Descripción</th>
                <td><?= e($carrera['descripcion'] ?: 'Sin descripción') ?></td>
            </tr>
        </table>

        <a
            href="<?= e(url('/carreras/edit') . '&id=' . $carrera['id']) ?>"
            class="btn btn-warning"
        >
            Editar carrera
        </a>

        <a href="<?= e(url('/carreras/cupos')) ?>" class="btn btn-outline-primary">
            Administrar cupos
        </a>
    </div>
</div>