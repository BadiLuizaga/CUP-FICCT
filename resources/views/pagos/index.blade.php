<?php $titulo = 'Pagos - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Control de pagos</h2>
        <p class="text-muted mb-0">
            Control financiero de postulantes, pagos registrados y cuentas por cobrar del CUP
        </p>
    </div>

    <a href="<?= e(url('/pagos/create')) ?>" class="btn btn-primary">
        Registrar pago
    </a>
</div>

<div class="alert alert-info">
    El pago oficial del CUP es de <strong>Bs <?= e(number_format((float)$montoOficial, 2, ',', '.')) ?></strong>.
    El pago se realiza en Caja Facultativa, Módulo 236, Planta Baja.
    Horario referencial: 08:30 a 15:30.
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>CI</th>
                        <th>Postulante</th>
                        <th>Carrera principal</th>
                        <th>Total aceptado</th>
                        <th>Saldo</th>
                        <th>Estado cuenta</th>
                        <th>Pagos</th>
                        <th width="230">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($postulantes)): ?>
                        <?php foreach ($postulantes as $postulante): ?>
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

                            <tr>
                                <td><?= e($postulante['postulante_id']) ?></td>
                                <td><?= e($postulante['codigo_postulante']) ?></td>
                                <td><?= e($postulante['ci']) ?></td>
                                <td>
                                    <?= e(trim(($postulante['nombres'] ?? '') . ' ' . ($postulante['apellidos'] ?? ''))) ?>
                                </td>
                                <td><?= e($postulante['carrera_principal'] ?? 'Sin carrera') ?></td>

                                <td>
                                    Bs <?= e(number_format((float)($postulante['total_pagado_aceptado'] ?? 0), 2, ',', '.')) ?>
                                </td>

                                <td>
                                    Bs <?= e(number_format((float)($postulante['saldo'] ?? $montoOficial), 2, ',', '.')) ?>
                                </td>

                                <td>
                                    <span class="badge <?= e($badgeCuenta) ?>">
                                        <?= e($estadoCuenta) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-primary">
                                        <?= e($postulante['total_pagos'] ?? 0) ?> pagos
                                    </span>

                                    <?php if ((int)($postulante['pagos_pendientes'] ?? 0) > 0): ?>
                                        <span class="badge bg-warning text-dark">
                                            <?= e($postulante['pagos_pendientes']) ?> pendientes
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a
                                        href="<?= e(url('/pagos/show') . '&id=' . $postulante['postulante_id']) ?>"
                                        class="btn btn-sm btn-info text-white"
                                    >
                                        Ver
                                    </a>

                                    <a
                                        href="<?= e(url('/pagos/create') . '&postulante_id=' . $postulante['postulante_id']) ?>"
                                        class="btn btn-sm btn-primary"
                                    >
                                        Pagar
                                    </a>

                                    <?php if (empty($postulante['cuenta_id'])): ?>
                                        <form
                                            action="<?= e(url('/pagos/generar-cuenta')) ?>"
                                            method="POST"
                                            class="d-inline"
                                        >
                                            <input
                                                type="hidden"
                                                name="postulante_id"
                                                value="<?= e($postulante['postulante_id']) ?>"
                                            >

                                            <button type="submit" class="btn btn-sm btn-secondary">
                                                Cuenta
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                No hay postulantes registrados para controlar pagos.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>