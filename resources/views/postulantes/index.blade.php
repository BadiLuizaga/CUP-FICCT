<?php $titulo = 'Postulantes - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Postulantes</h2>
        <p class="text-muted mb-0">Registro y consulta de postulantes por gestión del CUP FICCT</p>
    </div>

    <a href="<?= e(url('/postulantes/create')) ?>" class="btn btn-primary">
        Nuevo postulante
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="<?= e(url('/postulantes')) ?>" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="url" value="/postulantes">

            <div class="col-md-8">
                <label class="form-label">Filtrar por gestión</label>
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

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            Cada postulante pertenece a una gestión del CUP y registra dos opciones de carrera: principal y secundaria.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Gestión</th>
                        <th>CI</th>
                        <th>Nombre completo</th>
                        <th>Carrera principal</th>
                        <th>Carrera secundaria</th>
                        <th>Estado</th>
                        <th>Requisitos</th>
                        <th>Fecha registro</th>
                        <th width="180">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($postulantes)): ?>
                        <?php foreach ($postulantes as $postulante): ?>
                            <?php
                                $total = (int)($postulante['total_requisitos'] ?? 0);
                                $aceptados = (int)($postulante['requisitos_aceptados'] ?? 0);
                            ?>
                            <tr>
                                <td><?= e($postulante['id']) ?></td>
                                <td><?= e($postulante['codigo']) ?></td>
                                <td>
                                    <span class="badge bg-dark">
                                        <?= e($postulante['periodo_codigo'] ?? 'Sin gestión') ?>
                                    </span>
                                </td>
                                <td><?= e($postulante['ci']) ?></td>
                                <td><?= e(trim(($postulante['nombres'] ?? '') . ' ' . ($postulante['apellidos'] ?? ''))) ?></td>
                                <td><?= e($postulante['carrera_principal'] ?? '-') ?></td>
                                <td><?= e($postulante['carrera_secundaria'] ?? '-') ?></td>
                                <td>
                                    <?php if (($postulante['estado_postulacion'] ?? '') === 'Admitido'): ?>
                                        <span class="badge bg-success">Admitido</span>
                                    <?php elseif (($postulante['estado_postulacion'] ?? '') === 'No admitido'): ?>
                                        <span class="badge bg-warning text-dark">No admitido</span>
                                    <?php elseif (($postulante['estado_postulacion'] ?? '') === 'Reprobado'): ?>
                                        <span class="badge bg-danger">Reprobado</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary"><?= e($postulante['estado_postulacion']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($aceptados) ?> / <?= e($total) ?></td>
                                <td><?= e(format_date_short($postulante['fecha_registro'])) ?></td>
                                <td>
                                    <a href="<?= e(url('/postulantes/show') . '&id=' . $postulante['id']) ?>" class="btn btn-sm btn-info text-white">
                                        Ver
                                    </a>

                                    <a href="<?= e(url('/postulantes/edit') . '&id=' . $postulante['id']) ?>" class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                No hay postulantes registrados para la gestión seleccionada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>