<?php $titulo = 'Detalle de Postulante - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Detalle de postulante</h2>
        <p class="text-muted mb-0">Información personal, académica y requisitos generados</p>
    </div>

    <div>
        <a href="<?= e(url('/postulantes/edit') . '&id=' . $postulante['id']) ?>" class="btn btn-warning">
            Editar
        </a>
        <a href="<?= e(url('/postulantes')) ?>" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Información general
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <strong>Código:</strong><br>
                <?= e($postulante['codigo']) ?>
            </div>

            <div class="col-md-3">
                <strong>CI:</strong><br>
                <?= e($postulante['ci']) ?>
            </div>

            <div class="col-md-6">
                <strong>Nombre completo:</strong><br>
                <?= e(trim(($postulante['nombres'] ?? '') . ' ' . ($postulante['apellidos'] ?? ''))) ?>
            </div>

            <div class="col-md-3">
                <strong>Gestión CUP:</strong><br>
                <span class="badge bg-dark">
                    <?= e($postulante['periodo_codigo'] ?? 'Sin gestión') ?>
                </span>
            </div>

            <div class="col-md-3">
                <strong>Estado gestión:</strong><br>
                <?= e($postulante['periodo_estado'] ?? '-') ?>
            </div>

            <div class="col-md-3">
                <strong>Inicio CUP:</strong><br>
                <?= e(format_date_short($postulante['periodo_fecha_inicio'] ?? null)) ?>
            </div>

            <div class="col-md-3">
                <strong>Fin CUP:</strong><br>
                <?= e(format_date_short($postulante['periodo_fecha_fin'] ?? null)) ?>
            </div>

            <div class="col-md-4">
                <strong>Estado postulación:</strong><br>
                <span class="badge bg-primary">
                    <?= e($postulante['estado_postulacion']) ?>
                </span>
            </div>

            <div class="col-md-4">
                <strong>Fecha registro:</strong><br>
                <?= e(format_date_short($postulante['fecha_registro'])) ?>
            </div>

            <div class="col-md-4">
                <strong>Email:</strong><br>
                <?= e($postulante['email'] ?: '-') ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Datos académicos y opciones de carrera
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <strong>Colegio:</strong><br>
                <?= e($postulante['colegio'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Ciudad:</strong><br>
                <?= e($postulante['ciudad'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Título bachiller:</strong><br>
                <?= e($postulante['titulo_bachiller'] ?: '-') ?>
            </div>

            <div class="col-md-6">
                <strong>Primera opción:</strong><br>
                <?= e($postulante['carrera_principal'] ?: '-') ?>
            </div>

            <div class="col-md-6">
                <strong>Segunda opción:</strong><br>
                <?= e($postulante['carrera_secundaria'] ?: '-') ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Requisitos documentales
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Requisito</th>
                        <th>Archivo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requisitos)): ?>
                        <?php foreach ($requisitos as $requisito): ?>
                            <tr>
                                <td><?= e($requisito['id']) ?></td>
                                <td><?= e($requisito['tipo_documento']) ?></td>
                                <td><?= e($requisito['archivo'] ?: 'Sin archivo') ?></td>
                                <td>
                                    <?php if ($requisito['estado_validacion'] === 'Aceptado'): ?>
                                        <span class="badge bg-success">Aceptado</span>
                                    <?php elseif ($requisito['estado_validacion'] === 'Observado'): ?>
                                        <span class="badge bg-warning text-dark">Observado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No tiene requisitos generados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>