<?php $titulo = 'Cupos por Carrera y Gestión - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Cupos por carrera y gestión</h2>
        <p class="text-muted mb-0">Administración de cupos del CUP por periodo académico</p>
    </div>

    <a href="<?= e(url('/carreras')) ?>" class="btn btn-secondary">
        Volver a carreras
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="<?= e(url('/carreras/cupos')) ?>" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="url" value="/carreras/cupos">

            <div class="col-md-8">
                <label class="form-label">Gestión / Periodo académico</label>
                <select name="periodo_id" class="form-select">
                    <?php foreach ($periodos as $periodo): ?>
                        <option value="<?= e($periodo['id']) ?>" <?= selected_value($periodoId, $periodo['id']) ?>>
                            <?= e($periodo['codigo']) ?> - <?= e($periodo['gestion']) ?>/<?= e($periodo['semestre']) ?>
                            (<?= e($periodo['estado']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($periodoSeleccionado): ?>
    <div class="alert alert-info">
        Gestión seleccionada: <strong><?= e($periodoSeleccionado['codigo']) ?></strong>.
        Los cupos se guardan por periodo. Total esperado actual: <strong>600 cupos</strong>.
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Carrera</th>
                        <th width="180">Cupo periodo</th>
                        <th>Admitidos</th>
                        <th>Cupos libres</th>
                        <th width="160">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($carreras)): ?>
                        <?php foreach ($carreras as $carrera): ?>
                            <tr>
                                <form action="<?= e(url('/carreras/actualizar-cupo')) ?>" method="POST">
                                    <input type="hidden" name="periodo_id" value="<?= e($periodoId) ?>">
                                    <input type="hidden" name="carrera_id" value="<?= e($carrera['id']) ?>">

                                    <td><?= e($carrera['id']) ?></td>
                                    <td><?= e($carrera['codigo']) ?></td>
                                    <td><?= e($carrera['nombre']) ?></td>
                                    <td>
                                        <input
                                            type="number"
                                            name="cupo_maximo"
                                            class="form-control"
                                            value="<?= e($carrera['cupo_maximo']) ?>"
                                            min="1"
                                            required
                                        >
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= e($carrera['total_admitidos']) ?> admitidos
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <?= e($carrera['cupos_disponibles']) ?> libres
                                        </span>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            Guardar
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No hay carreras o no se seleccionó una gestión.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>