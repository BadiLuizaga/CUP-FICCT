<?php $titulo = 'Validar Requisitos - SIGIE'; ?>

<?php
$total = (int)($resumen['total_requisitos'] ?? 0);
$aceptados = (int)($resumen['requisitos_aceptados'] ?? 0);
$pendientes = (int)($resumen['requisitos_pendientes'] ?? 0);
$observados = (int)($resumen['requisitos_observados'] ?? 0);
$porcentaje = $total > 0 ? round(($aceptados / $total) * 100) : 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Validar requisitos</h2>
        <p class="text-muted mb-0">Revisión documental del postulante seleccionado</p>
    </div>

    <div>
        <a href="<?= e(url('/postulantes/show') . '&id=' . $postulante['id']) ?>" class="btn btn-info text-white">
            Ver perfil
        </a>

        <a href="<?= e(url('/documentos')) ?>" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Información del postulante
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

            <div class="col-md-6">
                <strong>Carrera principal:</strong><br>
                <?= e($postulante['carrera_principal'] ?: '-') ?>
            </div>

            <div class="col-md-6">
                <strong>Carrera secundaria:</strong><br>
                <?= e($postulante['carrera_secundaria'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Estado postulación:</strong><br>
                <?php if ($postulante['estado_postulacion'] === 'Documentación validada'): ?>
                    <span class="badge bg-success">Documentación validada</span>
                <?php elseif ($postulante['estado_postulacion'] === 'Documentación observada'): ?>
                    <span class="badge bg-warning text-dark">Documentación observada</span>
                <?php else: ?>
                    <span class="badge bg-primary"><?= e($postulante['estado_postulacion']) ?></span>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <strong>Fecha de registro:</strong><br>
                <?= e(format_date_short($postulante['fecha_registro'])) ?>
            </div>

            <div class="col-md-4">
                <strong>Avance documental:</strong><br>
                <?= e($aceptados) ?> / <?= e($total) ?> aceptados
            </div>
        </div>

        <div class="mt-3">
            <div class="progress" style="height: 10px;">
                <div
                    class="progress-bar"
                    role="progressbar"
                    style="width: <?= e($porcentaje) ?>%;"
                    aria-valuenow="<?= e($porcentaje) ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total requisitos</h6>
                <h3 class="mb-0"><?= e($total) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Aceptados</h6>
                <h3 class="mb-0 text-success"><?= e($aceptados) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Pendientes</h6>
                <h3 class="mb-0 text-secondary"><?= e($pendientes) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Observados</h6>
                <h3 class="mb-0 text-warning"><?= e($observados) ?></h3>
            </div>
        </div>
    </div>
</div>

<?php if ($total > 0 && $aceptados === $total): ?>
    <div class="alert alert-success">
        Todos los requisitos del postulante están aceptados. El postulante ya puede continuar al siguiente paso del proceso.
    </div>
<?php elseif ($observados > 0): ?>
    <div class="alert alert-warning">
        El postulante tiene requisitos observados. Debe corregir o completar la documentación antes de continuar.
    </div>
<?php else: ?>
    <div class="alert alert-info">
        Revisa cada requisito y cambia su estado según corresponda.
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Requisitos del postulante
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Requisito</th>
                        <th>Archivo</th>
                        <th>Estado actual</th>
                        <th width="360">Actualizar validación</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($documentos)): ?>
                        <?php foreach ($documentos as $documento): ?>
                            <tr>
                                <td><?= e($documento['id']) ?></td>

                                <td><?= e($documento['tipo_documento']) ?></td>

                                <td>
                                    <?php if (!empty($documento['archivo'])): ?>
                                        <?= e($documento['archivo']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Sin archivo</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($documento['estado_validacion'] === 'Aceptado'): ?>
                                        <span class="badge bg-success">Aceptado</span>
                                    <?php elseif ($documento['estado_validacion'] === 'Observado'): ?>
                                        <span class="badge bg-warning text-dark">Observado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pendiente</span>
                                    <?php endif; ?>

                                    <?php if (!empty($documento['observacion'])): ?>
                                        <div class="small text-muted mt-2">
                                            <strong>Obs:</strong> <?= e($documento['observacion']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <form action="<?= e(url('/documentos/update')) ?>" method="POST">
                                        <input type="hidden" name="postulante_id" value="<?= e($postulante['id']) ?>">
                                        <input type="hidden" name="documento_id" value="<?= e($documento['id']) ?>">

                                        <div class="mb-2">
                                            <select name="estado_validacion" class="form-select form-select-sm" required>
                                                <option value="Pendiente" <?= selected_value(old('estado_validacion', $documento['estado_validacion']), 'Pendiente') ?>>
                                                    Pendiente
                                                </option>

                                                <option value="Aceptado" <?= selected_value(old('estado_validacion', $documento['estado_validacion']), 'Aceptado') ?>>
                                                    Aceptado
                                                </option>

                                                <option value="Observado" <?= selected_value(old('estado_validacion', $documento['estado_validacion']), 'Observado') ?>>
                                                    Observado
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <textarea
                                                name="observacion"
                                                class="form-control form-control-sm"
                                                rows="2"
                                                placeholder="Escribe una observación si el requisito está observado"
                                            ><?= e(old('observacion', $documento['observacion'])) ?></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-sm btn-primary">
                                            Guardar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Este postulante no tiene requisitos generados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>