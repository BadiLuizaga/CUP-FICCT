<?php $titulo = 'Requisitos de Inscripción - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Requisitos de inscripción</h2>
        <p class="text-muted mb-0">Control y validación de requisitos presentados por los postulantes</p>
    </div>

    <a href="<?= e(url('/postulantes')) ?>" class="btn btn-secondary">
        Ver postulantes
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            Desde este módulo puedes revisar los requisitos generados automáticamente al registrar un postulante y cambiar su estado a
            <strong>Pendiente</strong>, <strong>Aceptado</strong> u <strong>Observado</strong>.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>CI</th>
                        <th>Postulante</th>
                        <th>Carrera principal</th>
                        <th>Estado postulación</th>
                        <th>Requisitos</th>
                        <th>Pendientes</th>
                        <th>Aceptados</th>
                        <th>Observados</th>
                        <th width="170">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($postulantes)): ?>
                        <?php foreach ($postulantes as $postulante): ?>
                            <?php
                                $total = (int)($postulante['total_requisitos'] ?? 0);
                                $aceptados = (int)($postulante['requisitos_aceptados'] ?? 0);
                                $pendientes = (int)($postulante['requisitos_pendientes'] ?? 0);
                                $observados = (int)($postulante['requisitos_observados'] ?? 0);
                                $porcentaje = $total > 0 ? round(($aceptados / $total) * 100) : 0;
                            ?>

                            <tr>
                                <td><?= e($postulante['postulante_id']) ?></td>
                                <td><?= e($postulante['codigo_postulante']) ?></td>
                                <td><?= e($postulante['ci']) ?></td>
                                <td><?= e(trim(($postulante['nombres'] ?? '') . ' ' . ($postulante['apellidos'] ?? ''))) ?></td>
                                <td><?= e($postulante['carrera_principal'] ?? 'Sin carrera') ?></td>
                                <td>
                                    <?php if ($postulante['estado_postulacion'] === 'Documentación validada'): ?>
                                        <span class="badge bg-success">Documentación validada</span>
                                    <?php elseif ($postulante['estado_postulacion'] === 'Documentación observada'): ?>
                                        <span class="badge bg-warning text-dark">Documentación observada</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary"><?= e($postulante['estado_postulacion']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <?= e($aceptados) ?> / <?= e($total) ?>
                                    </div>

                                    <div class="progress" style="height: 8px;">
                                        <div
                                            class="progress-bar"
                                            role="progressbar"
                                            style="width: <?= e($porcentaje) ?>%;"
                                            aria-valuenow="<?= e($porcentaje) ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= e($pendientes) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <?= e($aceptados) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= e($observados) ?>
                                    </span>
                                </td>
                                <td>
                                    <a
                                        href="<?= e(url('/documentos/show') . '&id=' . $postulante['postulante_id']) ?>"
                                        class="btn btn-sm btn-primary"
                                    >
                                        Validar
                                    </a>

                                    <a
                                        href="<?= e(url('/postulantes/show') . '&id=' . $postulante['postulante_id']) ?>"
                                        class="btn btn-sm btn-info text-white"
                                    >
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                No hay postulantes con requisitos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>