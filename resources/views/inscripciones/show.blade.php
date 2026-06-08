<?php $titulo = 'Convalidar e Inscribir - SIGIE'; ?>

<?php
$total = (int)($postulante['total_requisitos'] ?? 0);
$aceptados = (int)($postulante['requisitos_aceptados'] ?? 0);
$pendientes = (int)($postulante['requisitos_pendientes'] ?? 0);
$observados = (int)($postulante['requisitos_observados'] ?? 0);
$saldo = (float)($postulante['saldo'] ?? 700.00);
$estadoCuenta = $postulante['estado_cuenta'] ?? 'Pendiente';
$docsOk = $total > 0 && $aceptados === $total && $pendientes === 0 && $observados === 0;
$pagoOk = $estadoCuenta === 'Pagado' && $saldo <= 0;
$yaInscrito = !empty($postulante['inscripcion_id']);
$puedeInscribirse = (bool)($validacion['puede_inscribirse'] ?? false);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Convalidar documentación e inscripción</h2>
        <p class="text-muted mb-0">Revisión final de requisitos, pago y grupo asignado</p>
    </div>

    <div>
        <a href="<?= e(url('/postulantes/show') . '&id=' . $postulante['id']) ?>" class="btn btn-info text-white">
            Ver postulante
        </a>

        <a href="<?= e(url('/inscripciones')) ?>" class="btn btn-secondary">
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

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">1. Requisitos documentales</h6>
                <?php if ($docsOk): ?>
                    <h4 class="text-success mb-1">Correcto</h4>
                    <p class="mb-0">Todos los requisitos están aceptados.</p>
                <?php else: ?>
                    <h4 class="text-warning mb-1">Pendiente</h4>
                    <p class="mb-0">
                        Aceptados: <?= e($aceptados) ?>/<?= e($total) ?>,
                        pendientes: <?= e($pendientes) ?>,
                        observados: <?= e($observados) ?>.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">2. Pago financiero</h6>
                <?php if ($pagoOk): ?>
                    <h4 class="text-success mb-1">Pagado</h4>
                    <p class="mb-0">La cuenta está pagada y el saldo es Bs 0,00.</p>
                <?php else: ?>
                    <h4 class="text-danger mb-1"><?= e($estadoCuenta) ?></h4>
                    <p class="mb-0">Saldo actual: Bs <?= e(number_format($saldo, 2, ',', '.')) ?>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">3. Inscripción oficial</h6>
                <?php if ($yaInscrito): ?>
                    <h4 class="text-primary mb-1">Inscrito</h4>
                    <p class="mb-0">
                        <?= e($postulante['codigo_inscripcion']) ?> en <?= e($postulante['grupo_codigo']) ?>.
                    </p>
                <?php elseif ($puedeInscribirse): ?>
                    <h4 class="text-success mb-1">Apto</h4>
                    <p class="mb-0">Puede asignarse a un grupo disponible.</p>
                <?php else: ?>
                    <h4 class="text-secondary mb-1">No apto</h4>
                    <p class="mb-0">Debe regularizar requisitos o pago.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($yaInscrito): ?>
    <div class="alert alert-success">
        El postulante ya fue inscrito oficialmente.
        <strong>Inscripción:</strong> <?= e($postulante['codigo_inscripcion']) ?>,
        <strong>Grupo:</strong> <?= e($postulante['grupo_codigo']) ?> - <?= e($postulante['grupo_nombre']) ?>,
        <strong>Fecha:</strong> <?= e(format_date_short($postulante['fecha_inscripcion'])) ?>.
    </div>
<?php elseif (!$puedeInscribirse): ?>
    <div class="alert alert-warning">
        <strong>El postulante todavía no puede ser inscrito.</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (($validacion['errores'] ?? []) as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <div class="alert alert-success">
        El postulante cumple con documentación aceptada y pago completo. Selecciona un grupo para completar la inscripción oficial.
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        Revisión de requisitos
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
                        <th>Observación</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($documentos)): ?>
                        <?php foreach ($documentos as $documento): ?>
                            <tr>
                                <td><?= e($documento['id']) ?></td>
                                <td><?= e($documento['tipo_documento']) ?></td>
                                <td><?= e($documento['archivo'] ?: 'Sin archivo') ?></td>
                                <td>
                                    <?php if ($documento['estado_validacion'] === 'Aceptado'): ?>
                                        <span class="badge bg-success">Aceptado</span>
                                    <?php elseif ($documento['estado_validacion'] === 'Observado'): ?>
                                        <span class="badge bg-warning text-dark">Observado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($documento['observacion'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                El postulante no tiene requisitos generados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Asignación de grupo
    </div>

    <div class="card-body">
        <?php if ($yaInscrito): ?>
            <table class="table table-bordered align-middle mb-0">
                <tr>
                    <th width="220">Código inscripción</th>
                    <td><?= e($postulante['codigo_inscripcion']) ?></td>
                </tr>
                <tr>
                    <th>Grupo asignado</th>
                    <td><?= e($postulante['grupo_codigo']) ?> - <?= e($postulante['grupo_nombre']) ?></td>
                </tr>
                <tr>
                    <th>Fecha inscripción</th>
                    <td><?= e(format_date_short($postulante['fecha_inscripcion'])) ?></td>
                </tr>
                <tr>
                    <th>Estado inscripción</th>
                    <td><span class="badge bg-success"><?= e($postulante['estado_inscripcion']) ?></span></td>
                </tr>
            </table>
        <?php else: ?>
            <form action="<?= e(url('/inscripciones/store')) ?>" method="POST">
                <input type="hidden" name="postulante_id" value="<?= e($postulante['id']) ?>">

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label required">Grupo disponible</label>
                        <select name="grupo_id" class="form-select" <?= $puedeInscribirse ? 'required' : 'disabled' ?>>
                            <option value="">Seleccione un grupo</option>

                            <?php foreach ($grupos as $grupo): ?>
                                <option value="<?= e($grupo['id']) ?>">
                                    <?= e($grupo['codigo']) ?> - <?= e($grupo['nombre']) ?>
                                    | Cupos libres: <?= e($grupo['cupos_disponibles']) ?>
                                    | <?= e($grupo['periodo_codigo'] ?? 'Sin periodo') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (empty($grupos)): ?>
                            <small class="text-danger">No hay grupos activos con cupos disponibles.</small>
                        <?php else: ?>
                            <small class="text-muted">Solo se muestran grupos activos con cupos disponibles.</small>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" <?= $puedeInscribirse && !empty($grupos) ? '' : 'disabled' ?>>
                            Confirmar inscripción
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>