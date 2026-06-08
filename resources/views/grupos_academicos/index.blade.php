<?php $titulo = 'Grupos Académicos - SIGIE'; ?>

<?php
$totalGrupos = (int)($indicadores['total_grupos'] ?? 0);
$totalInscritos = (int)($indicadores['total_inscritos'] ?? 0);
$capacidadTotal = (int)($indicadores['capacidad_total'] ?? 0);
$cuposLibres = (int)($indicadores['cupos_libres'] ?? 0);
$capacidadAula = (int)($indicadores['capacidad_aula'] ?? 60);
$gruposNecesarios = (int)($indicadores['grupos_necesarios_capacidad_60'] ?? 0);
$porcentajeGeneral = (float)($indicadores['porcentaje_general'] ?? 0);
$gruposActivos = (int)($indicadores['grupos_activos'] ?? 0);
$gruposSaturados = (int)($indicadores['grupos_saturados'] ?? 0);
$gruposInactivos = (int)($indicadores['grupos_inactivos'] ?? 0);
$gruposCerrados = (int)($indicadores['grupos_cerrados'] ?? 0);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Grupos académicos</h2>
        <p class="text-muted mb-0">
            CU08 - Cálculo y habilitación de grupos académicos con capacidad de <?= e($capacidadAula) ?> estudiantes por aula
        </p>
    </div>

    <form action="<?= e(url('/grupos-academicos/recalcular')) ?>" method="POST">
        <button type="submit" class="btn btn-primary">
            Recalcular grupos
        </button>
    </form>
</div>

<div class="alert alert-info">
    Este módulo sincroniza la cantidad real de estudiantes inscritos por grupo usando las inscripciones activas.
    También calcula la cantidad sugerida de grupos según la capacidad de aula de <strong><?= e($capacidadAula) ?> estudiantes</strong>.
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Postulantes inscritos</h6>
                <h3 class="mb-0"><?= e($totalInscritos) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Grupos necesarios</h6>
                <h3 class="mb-0 text-primary"><?= e($gruposNecesarios) ?></h3>
                <small class="text-muted">Según capacidad <?= e($capacidadAula) ?></small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Capacidad total</h6>
                <h3 class="mb-0"><?= e($capacidadTotal) ?></h3>
                <small class="text-muted">Suma de cupos configurados en grupos</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Cupos libres</h6>
                <h3 class="mb-0 text-success"><?= e($cuposLibres) ?></h3>
                <small class="text-muted">Disponibles actualmente</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total grupos</h6>
                <h3 class="mb-0"><?= e($totalGrupos) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Activos</h6>
                <h3 class="mb-0 text-success"><?= e($gruposActivos) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Saturados</h6>
                <h3 class="mb-0 text-danger"><?= e($gruposSaturados) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Inactivos / cerrados</h6>
                <h3 class="mb-0 text-secondary"><?= e($gruposInactivos + $gruposCerrados) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Ocupación general
    </div>

    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span>Ocupación total del CUP</span>
            <strong><?= e(number_format($porcentajeGeneral, 2, ',', '.')) ?>%</strong>
        </div>

        <div class="progress" style="height: 12px;">
            <div
                class="progress-bar"
                role="progressbar"
                style="width: <?= e(min($porcentajeGeneral, 100)) ?>%;"
                aria-valuenow="<?= e($porcentajeGeneral) ?>"
                aria-valuemin="0"
                aria-valuemax="100"
            ></div>
        </div>
    </div>
</div>

<?php if ($totalInscritos > 0 && $gruposNecesarios > $totalGrupos): ?>
    <div class="alert alert-warning">
        Según la capacidad de <?= e($capacidadAula) ?> estudiantes por aula, se requieren <?= e($gruposNecesarios) ?> grupos,
        pero solo existen <?= e($totalGrupos) ?> registrados.
        Debes registrar más grupos o revisar la planificación académica.
    </div>
<?php elseif ($totalInscritos > 0): ?>
    <div class="alert alert-success">
        La cantidad de grupos registrados permite cubrir la demanda actual según la capacidad de <?= e($capacidadAula) ?> estudiantes por aula.
    </div>
<?php else: ?>
    <div class="alert alert-secondary">
        Todavía no existen postulantes inscritos oficialmente. El cálculo se actualizará cuando existan inscripciones activas.
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Detalle de grupos académicos
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Grupo</th>
                        <th>Nombre</th>
                        <th>Periodo</th>
                        <th>Capacidad</th>
                        <th>Inscritos registrados</th>
                        <th>Inscritos reales</th>
                        <th>Diferencia</th>
                        <th>Cupos libres</th>
                        <th>Ocupación</th>
                        <th>Estado</th>
                        <th width="250">Actualizar estado</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($grupos)): ?>
                        <?php foreach ($grupos as $grupo): ?>
                            <?php
                                $capacidad = (int)($grupo['capacidad'] ?? 0);
                                $registrados = (int)($grupo['cantidad_estudiantes'] ?? 0);
                                $reales = (int)($grupo['inscritos_reales'] ?? 0);
                                $diferencia = (int)($grupo['diferencia_registro'] ?? 0);
                                $cupos = (int)($grupo['cupos_libres'] ?? 0);
                                $porcentaje = (float)($grupo['porcentaje_ocupacion'] ?? 0);
                                $estado = trim((string)($grupo['estado'] ?? 'Activo'));

                                if ($estado === 'Activo') {
                                    $badgeEstado = 'bg-success';
                                } elseif ($estado === 'Saturado') {
                                    $badgeEstado = 'bg-danger';
                                } elseif ($estado === 'Inactivo') {
                                    $badgeEstado = 'bg-secondary';
                                } elseif ($estado === 'Cerrado') {
                                    $badgeEstado = 'bg-dark';
                                } else {
                                    $badgeEstado = 'bg-primary';
                                }

                                if ($diferencia === 0) {
                                    $badgeDiferencia = 'bg-success';
                                } else {
                                    $badgeDiferencia = 'bg-warning text-dark';
                                }
                            ?>

                            <tr>
                                <td><?= e($grupo['id']) ?></td>

                                <td>
                                    <strong><?= e($grupo['codigo']) ?></strong>
                                </td>

                                <td><?= e($grupo['nombre']) ?></td>

                                <td>
                                    <?= e($grupo['periodo_codigo'] ?? 'Sin periodo') ?>
                                </td>

                                <td>
                                    <span class="badge bg-primary">
                                        <?= e($capacidad) ?>
                                    </span>
                                </td>

                                <td><?= e($registrados) ?></td>

                                <td><?= e($reales) ?></td>

                                <td>
                                    <span class="badge <?= e($badgeDiferencia) ?>">
                                        <?= e($diferencia) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($cupos > 0): ?>
                                        <span class="badge bg-success">
                                            <?= e($cupos) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            0
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td style="min-width: 140px;">
                                    <div class="small mb-1">
                                        <?= e(number_format($porcentaje, 2, ',', '.')) ?>%
                                    </div>

                                    <div class="progress" style="height: 8px;">
                                        <div
                                            class="progress-bar"
                                            role="progressbar"
                                            style="width: <?= e(min($porcentaje, 100)) ?>%;"
                                            aria-valuenow="<?= e($porcentaje) ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge <?= e($badgeEstado) ?>">
                                        <?= e($estado) ?>
                                    </span>
                                </td>

                                <td>
                                    <form action="<?= e(url('/grupos-academicos/cambiar-estado')) ?>" method="POST">
                                        <input type="hidden" name="grupo_id" value="<?= e($grupo['id']) ?>">

                                        <div class="input-group input-group-sm">
                                            <select name="estado" class="form-select" required>
                                                <?php foreach ($estadosValidos as $estadoValido): ?>
                                                    <option
                                                        value="<?= e($estadoValido) ?>"
                                                        <?= selected_value($estado, $estadoValido) ?>
                                                    >
                                                        <?= e($estadoValido) ?>
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
                            <td colspan="12" class="text-center text-muted">
                                No hay grupos académicos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-light border mt-3 mb-0">
            <strong>Nota:</strong> si la diferencia es distinta de 0, significa que el campo
            <strong>cantidad_estudiantes</strong> de la tabla <strong>grupo</strong> no coincide con las inscripciones activas.
            Usa el botón <strong>Recalcular grupos</strong> para sincronizarlo.
        </div>
    </div>
</div>