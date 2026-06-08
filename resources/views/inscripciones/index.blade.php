<?php $titulo = 'Inscripciones - SIGIE'; ?>

<?php
$totalPostulantes = count($postulantes ?? []);
$totalInscritos = 0;
$totalAptos = 0;
$totalObservados = 0;

foreach (($postulantes ?? []) as $fila) {
    $totalReq = (int)($fila['total_requisitos'] ?? 0);
    $aceptados = (int)($fila['requisitos_aceptados'] ?? 0);
    $pendientes = (int)($fila['requisitos_pendientes'] ?? 0);
    $observados = (int)($fila['requisitos_observados'] ?? 0);
    $saldo = (float)($fila['saldo'] ?? 700.00);
    $estadoCuenta = $fila['estado_cuenta'] ?? 'Pendiente';

    if (!empty($fila['inscripcion_id'])) {
        $totalInscritos++;
    }

    if ($totalReq > 0 && $aceptados === $totalReq && $pendientes === 0 && $observados === 0 && $estadoCuenta === 'Pagado' && $saldo <= 0 && empty($fila['inscripcion_id'])) {
        $totalAptos++;
    }

    if ($observados > 0 || $pendientes > 0 || $estadoCuenta !== 'Pagado') {
        $totalObservados++;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Convalidación e inscripción</h2>
        <p class="text-muted mb-0">Validación final de requisitos, pago y asignación oficial a grupo</p>
    </div>

    <a href="<?= e(url('/postulantes')) ?>" class="btn btn-secondary">
        Ver postulantes
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Postulantes</h6>
                <h3 class="mb-0"><?= e($totalPostulantes) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Aptos para inscribir</h6>
                <h3 class="mb-0 text-success"><?= e($totalAptos) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Ya inscritos</h6>
                <h3 class="mb-0 text-primary"><?= e($totalInscritos) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Por regularizar</h6>
                <h3 class="mb-0 text-warning"><?= e($totalObservados) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="alert alert-info mb-0">
            Para inscribir oficialmente, el postulante debe tener todos sus requisitos en <strong>Aceptado</strong>, su cuenta en estado <strong>Pagado</strong> y debe existir cupo disponible en un grupo activo.
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Disponibilidad de grupos
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Grupo</th>
                        <th>Nombre</th>
                        <th>Capacidad</th>
                        <th>Inscritos</th>
                        <th>Cupos libres</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($grupos)): ?>
                        <?php foreach ($grupos as $grupo): ?>
                            <?php
                                $cuposLibres = (int)($grupo['cupos_disponibles'] ?? 0);
                                $badgeGrupo = $cuposLibres > 0 ? 'bg-success' : 'bg-danger';
                            ?>
                            <tr>
                                <td><?= e($grupo['codigo']) ?></td>
                                <td><?= e($grupo['nombre']) ?></td>
                                <td><?= e($grupo['capacidad']) ?></td>
                                <td><?= e($grupo['cantidad_estudiantes']) ?></td>
                                <td>
                                    <span class="badge <?= e($badgeGrupo) ?>">
                                        <?= e($cuposLibres) ?> libres
                                    </span>
                                </td>
                                <td><?= e($grupo['estado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No hay grupos registrados.
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
        Postulantes para convalidación
    </div>

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
                        <th>Requisitos</th>
                        <th>Pago</th>
                        <th>Estado postulación</th>
                        <th>Inscripción</th>
                        <th width="160">Acciones</th>
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
                                $saldo = (float)($postulante['saldo'] ?? 700.00);
                                $estadoCuenta = $postulante['estado_cuenta'] ?? 'Pendiente';

                                $docsOk = $total > 0 && $aceptados === $total && $pendientes === 0 && $observados === 0;
                                $pagoOk = $estadoCuenta === 'Pagado' && $saldo <= 0;
                                $yaInscrito = !empty($postulante['inscripcion_id']);
                            ?>

                            <tr>
                                <td><?= e($postulante['postulante_id']) ?></td>
                                <td><?= e($postulante['codigo_postulante']) ?></td>
                                <td><?= e($postulante['ci']) ?></td>
                                <td><?= e(trim(($postulante['nombres'] ?? '') . ' ' . ($postulante['apellidos'] ?? ''))) ?></td>
                                <td><?= e($postulante['carrera_principal'] ?? 'Sin carrera') ?></td>
                                <td>
                                    <?php if ($docsOk): ?>
                                        <span class="badge bg-success">Aceptados <?= e($aceptados) ?>/<?= e($total) ?></span>
                                    <?php elseif ($observados > 0): ?>
                                        <span class="badge bg-warning text-dark">Observados <?= e($observados) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pendientes <?= e($pendientes) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($pagoOk): ?>
                                        <span class="badge bg-success">Pagado</span>
                                    <?php elseif ($estadoCuenta === 'Parcial'): ?>
                                        <span class="badge bg-warning text-dark">Parcial</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= e($estadoCuenta) ?></span>
                                    <?php endif; ?>
                                    <div class="small text-muted">
                                        Saldo: Bs <?= e(number_format($saldo, 2, ',', '.')) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?= e($postulante['estado_postulacion']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($yaInscrito): ?>
                                        <span class="badge bg-success">Inscrito</span>
                                        <div class="small text-muted">
                                            <?= e($postulante['codigo_inscripcion']) ?> - <?= e($postulante['grupo_codigo']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin inscripción</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a
                                        href="<?= e(url('/inscripciones/show') . '&id=' . $postulante['postulante_id']) ?>"
                                        class="btn btn-sm btn-primary"
                                    >
                                        Convalidar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                No hay postulantes registrados para convalidación.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>