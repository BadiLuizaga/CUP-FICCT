<?php $titulo = 'Registrar Pago - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Registrar pago</h2>
        <p class="text-muted mb-0">Registrar pago del CUP y actualizar cuenta por cobrar</p>
    </div>

    <a href="<?= e(url('/pagos')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<div class="alert alert-info">
    Monto oficial del CUP:
    <strong>Bs <?= e(number_format((float)$montoOficial, 2, ',', '.')) ?></strong>.
    Caja Facultativa, Módulo 236, Planta Baja. Horario referencial: 08:30 a 15:30.
</div>

<?php if (!empty($postulanteSeleccionado)): ?>
    <div class="card mb-4">
        <div class="card-header">
            Postulante seleccionado
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Código:</strong><br>
                    <?= e($postulanteSeleccionado['codigo']) ?>
                </div>

                <div class="col-md-3">
                    <strong>CI:</strong><br>
                    <?= e($postulanteSeleccionado['ci']) ?>
                </div>

                <div class="col-md-6">
                    <strong>Nombre completo:</strong><br>
                    <?= e(trim(($postulanteSeleccionado['nombres'] ?? '') . ' ' . ($postulanteSeleccionado['apellidos'] ?? ''))) ?>
                </div>

                <div class="col-md-4">
                    <strong>Total pagado aceptado:</strong><br>
                    Bs <?= e(number_format((float)($postulanteSeleccionado['total_pagado_aceptado'] ?? 0), 2, ',', '.')) ?>
                </div>

                <div class="col-md-4">
                    <strong>Saldo actual:</strong><br>
                    Bs <?= e(number_format((float)($postulanteSeleccionado['saldo'] ?? $montoOficial), 2, ',', '.')) ?>
                </div>

                <div class="col-md-4">
                    <strong>Estado cuenta:</strong><br>
                    <span class="badge bg-primary">
                        <?= e($postulanteSeleccionado['estado_cuenta'] ?? 'Pendiente') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="<?= e(url('/pagos/store')) ?>" method="POST">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label required">Postulante</label>
                    <select name="postulante_id" class="form-select" required>
                        <option value="">Seleccione un postulante</option>

                        <?php foreach ($postulantes as $postulante): ?>
                            <?php
                                $nombrePostulante = trim(($postulante['nombres'] ?? '') . ' ' . ($postulante['apellidos'] ?? ''));
                                $valorActual = old('postulante_id', $postulanteSeleccionado['id'] ?? '');
                            ?>

                            <option
                                value="<?= e($postulante['id']) ?>"
                                <?= selected_value($valorActual, $postulante['id']) ?>
                            >
                                <?= e($postulante['codigo']) ?> -
                                CI <?= e($postulante['ci']) ?> -
                                <?= e($nombrePostulante) ?> -
                                <?= e($postulante['carrera_principal'] ?? 'Sin carrera') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Tipo de pago</label>
                    <select name="tipo_pago_id" class="form-select" required>
                        <option value="">Seleccione</option>

                        <?php foreach ($tiposPago as $tipo): ?>
                            <option
                                value="<?= e($tipo['id']) ?>"
                                <?= selected_value(old('tipo_pago_id'), $tipo['id']) ?>
                            >
                                <?= e($tipo['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Monto Bs</label>
                    <input
                        type="number"
                        name="monto"
                        class="form-control"
                        value="<?= e(old('monto', number_format((float)$montoOficial, 2, '.', ''))) ?>"
                        min="0.01"
                        step="0.01"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Estado del pago</label>
                    <select name="estado" class="form-select" required>
                        <?php foreach ($estadosPago as $estado): ?>
                            <option
                                value="<?= e($estado) ?>"
                                <?= selected_value(old('estado', 'Aceptado'), $estado) ?>
                            >
                                <?= e($estado) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">
                        Si queda Aceptado, se descuenta automáticamente del saldo.
                    </small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha y hora de pago</label>
                    <input
                        type="datetime-local"
                        name="fecha_pago"
                        class="form-control"
                        value="<?= e(old('fecha_pago', date('Y-m-d\TH:i'))) ?>"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Referencia</label>
                    <input
                        type="text"
                        name="referencia"
                        class="form-control"
                        value="<?= e(old('referencia')) ?>"
                        placeholder="Ejemplo: REC-001, QR-123, TRANS-456"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Comprobante</label>
                    <input
                        type="text"
                        name="comprobante"
                        class="form-control"
                        value="<?= e(old('comprobante')) ?>"
                        placeholder="Nombre o código del comprobante"
                    >
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    Guardar pago
                </button>

                <a href="<?= e(url('/pagos')) ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>