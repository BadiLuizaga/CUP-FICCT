<?php $titulo = 'Detalle de Pagos - SIGIE'; ?>

<?php
$estadoCuenta = $postulante['estado_cuenta'] ?? 'Pendiente';
$badgeCuenta = 'bg-secondary';

if ($estadoCuenta === 'Pagado') {
    $badgeCuenta = 'bg-success';
} elseif ($estadoCuenta === 'Parcial') {
    $badgeCuenta = 'bg-warning text-dark';
} elseif ($estadoCuenta === 'Pendiente') {
    $badgeCuenta = 'bg-danger';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Detalle financiero</h2>
        <p class="text-muted mb-0">Pagos registrados y cuenta por cobrar del postulante</p>
    </div>

    <div>
        <a
            href="<?= e(url('/pagos/create') . '&postulante_id=' . $postulante['id']) ?>"
            class="btn btn-primary"
        >
            Registrar pago
        </a>

        <a href="<?= e(url('/pagos')) ?>" class="btn btn-secondary">
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
                <strong>Email:</strong><br>
                <?= e($postulante['email'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Teléfono:</strong><br>
                <?= e($postulante['telefono'] ?: '-') ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Monto oficial CUP</h6>
                <h3 class="mb-0">
                    Bs <?= e(number_format((float)$montoOficial, 2, ',', '.')) ?>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total aceptado</h6>
                <h3 class="mb-0 text-success">
                    Bs <?= e(number_format((float)($postulante['total_pagado_aceptado'] ?? 0), 2, ',', '.')) ?>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Saldo pendiente</h6>
                <h3 class="mb-0 text-danger">
                    Bs <?= e(number_format((float)($postulante['saldo'] ?? $montoOficial), 2, ',', '.')) ?>
                </h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Estado cuenta</h6>
                <h3 class="mb-0">
                    <span class="badge <?= e($badgeCuenta) ?>">
                        <?= e($estadoCuenta) ?>
                    </span>
                </h3>
            </div>
        </div>
    </div>
</div>

<?php if ($estadoCuenta === 'Pagado'): ?>
    <div class="alert alert-success">
        El postulante tiene el pago completo del CUP. Puede continuar con la convalidación e inscripción.
    </div>
<?php elseif ($estadoCuenta === 'Parcial'): ?>
    <div class="alert alert-warning">
        El postulante tiene pagos aceptados, pero todavía mantiene saldo pendiente.
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        El postulante aún no tiene pago aceptado o mantiene la cuenta pendiente.
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Pagos registrados
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Fecha pago</th>
                        <th>Referencia</th>
                        <th>Comprobante</th>
                        <th>Estado actual</th>
                        <th width="220">Actualizar estado</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($pagos)): ?>
                        <?php foreach ($pagos as $pago): ?>
                            <?php
                                $estadoPago = $pago['estado'] ?? 'Pendiente';
                                $badgePago = 'bg-secondary';

                                if ($estadoPago === 'Aceptado') {
                                    $badgePago = 'bg-success';
                                } elseif ($estadoPago === 'Pendiente') {
                                    $badgePago = 'bg-warning text-dark';
                                } elseif ($estadoPago === 'Rechazado') {
                                    $badgePago = 'bg-danger';
                                }
                            ?>

                            <tr>
                                <td><?= e($pago['id']) ?></td>
                                <td><?= e($pago['codigo']) ?></td>
                                <td><?= e($pago['tipo_pago'] ?: 'Sin tipo') ?></td>
                                <td>
                                    Bs <?= e(number_format((float)$pago['monto'], 2, ',', '.')) ?>
                                </td>
                                <td><?= e(format_date($pago['fecha_pago'])) ?></td>
                                <td><?= e($pago['referencia'] ?: '-') ?></td>
                                <td><?= e($pago['comprobante'] ?: '-') ?></td>
                                <td>
                                    <span class="badge <?= e($badgePago) ?>">
                                        <?= e($estadoPago) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= e(url('/pagos/cambiar-estado')) ?>" method="POST">
                                        <input type="hidden" name="pago_id" value="<?= e($pago['id']) ?>">
                                        <input type="hidden" name="postulante_id" value="<?= e($postulante['id']) ?>">

                                        <div class="input-group input-group-sm">
                                            <select name="estado" class="form-select" required>
                                                <?php foreach ($estadosPago as $estado): ?>
                                                    <option
                                                        value="<?= e($estado) ?>"
                                                        <?= selected_value($estadoPago, $estado) ?>
                                                    >
                                                        <?= e($estado) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <button type="submit" class="btn btn-primary">
                                                Guardar
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                Este postulante todavía no tiene pagos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>