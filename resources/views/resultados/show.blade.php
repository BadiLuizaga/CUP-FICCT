<?php $titulo = 'Resultados Finales - SIGIE'; ?>

<?php
$totalResultados = (int)($resumen['total_resultados'] ?? 0);
$totalAdmitidos = (int)($resumen['total_admitidos'] ?? 0);
$totalNoAdmitidos = (int)($resumen['total_no_admitidos'] ?? 0);
$totalReprobados = (int)($resumen['total_reprobados'] ?? 0);
$promedioGeneral = (float)($resumen['promedio_general'] ?? 0);
$periodoActivo = strtolower((string)($periodoSeleccionado['estado'] ?? '')) === 'activo';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Resultados finales</h2>
        <p class="text-muted mb-0">Lista oficial de admitidos, no admitidos y reprobados por gestión</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="<?= e(url('/resultados')) ?>" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="url" value="/resultados">

            <div class="col-md-5">
                <label class="form-label">Gestión / Periodo académico</label>
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
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="" <?= selected_value($estado, '') ?>>Todos</option>
                    <option value="ADMITIDO" <?= selected_value($estado, 'ADMITIDO') ?>>Admitidos</option>
                    <option value="NO ADMITIDO" <?= selected_value($estado, 'NO ADMITIDO') ?>>No admitidos</option>
                    <option value="REPROBADO" <?= selected_value($estado, 'REPROBADO') ?>>Reprobados</option>
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($periodoSeleccionado): ?>
    <?php if ($periodoActivo): ?>
        <div class="alert alert-warning">
            La gestión <strong><?= e($periodoSeleccionado['codigo']) ?></strong> está activa. Todavía no corresponde generar la lista final de admitidos.
        </div>
    <?php else: ?>
        <div class="card mb-4">
            <div class="card-body">
                <form action="<?= e(url('/resultados/generar')) ?>" method="POST" onsubmit="return confirm('¿Seguro que deseas regenerar los resultados finales de esta gestión? Se reemplazará el cálculo anterior.')">
                    <input type="hidden" name="periodo_id" value="<?= e($periodoId) ?>">
                    <button type="submit" class="btn btn-success">
                        Generar / recalcular resultados finales
                    </button>
                    <small class="text-muted ms-2">
                        Usa nota mínima 60, primera opción y luego segunda opción según cupos.
                    </small>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Procesados</h6>
                <h3 class="mb-0"><?= e($totalResultados) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Admitidos</h6>
                <h3 class="mb-0 text-success"><?= e($totalAdmitidos) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">No admitidos</h6>
                <h3 class="mb-0 text-warning"><?= e($totalNoAdmitidos) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-1">Reprobados</h6>
                <h3 class="mb-0 text-danger"><?= e($totalReprobados) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Cupos y admitidos por carrera
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>Carrera</th>
                        <th>Cupo</th>
                        <th>Admitidos</th>
                        <th>Libres</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($admitidosPorCarrera)): ?>
                        <?php foreach ($admitidosPorCarrera as $fila): ?>
                            <tr>
                                <td><?= e($fila['codigo']) ?></td>
                                <td><?= e($fila['nombre']) ?></td>
                                <td><?= e($fila['cupo_maximo']) ?></td>
                                <td><?= e($fila['total_admitidos']) ?></td>
                                <td><?= e($fila['cupos_libres']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Sin información de cupos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Resultados de postulantes
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            La lista oficial de admitidos debe tomar únicamente los registros con estado <strong>ADMITIDO</strong>.
            Los postulantes con nota mayor o igual a 60 que no alcanzan cupo quedan como <strong>NO ADMITIDO</strong> y no aparecen en la lista oficial de admitidos.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Código</th>
                        <th>CI</th>
                        <th>Postulante</th>
                        <th>Promedio</th>
                        <th>Estado</th>
                        <th>Carrera admitida</th>
                        <th>Opción</th>
                        <th>Observación</th>
                        <th width="100">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resultados)): ?>
                        <?php foreach ($resultados as $resultado): ?>
                            <tr>
                                <td><?= e($resultado['codigo_postulante']) ?></td>
                                <td><?= e($resultado['ci']) ?></td>
                                <td><?= e(trim(($resultado['nombres'] ?? '') . ' ' . ($resultado['apellidos'] ?? ''))) ?></td>
                                <td><strong><?= e(number_format((float)$resultado['promedio_general'], 2, ',', '.')) ?></strong></td>
                                <td>
                                    <?php if ($resultado['estado_final'] === 'ADMITIDO'): ?>
                                        <span class="badge bg-success">ADMITIDO</span>
                                    <?php elseif ($resultado['estado_final'] === 'NO ADMITIDO'): ?>
                                        <span class="badge bg-warning text-dark">NO ADMITIDO</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">REPROBADO</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($resultado['carrera_admitida'] ?: '-') ?></td>
                                <td><?= e($resultado['opcion_admitida'] ?: '-') ?></td>
                                <td><?= e($resultado['observacion']) ?></td>
                                <td>
                                    <a href="<?= e(url('/resultados/show') . '&id=' . $resultado['id']) ?>" class="btn btn-sm btn-info text-white">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                No hay resultados registrados para la gestión seleccionada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>