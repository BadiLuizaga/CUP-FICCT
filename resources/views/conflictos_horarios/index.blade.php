<?php $titulo = 'Conflictos de Horario - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Validación de cruces de horario</h2>
        <p class="text-muted mb-0">
            Revisión formal del CU07 sobre aula, docente, grupo y materia en la planificación académica.
        </p>
    </div>

    <div>
        <a href="<?= e(url('/planificacion-horaria')) ?>" class="btn btn-secondary">
            Ver planificación
        </a>

        <a href="<?= e(url('/conflictos-horarios')) ?>" class="btn btn-primary">
            Revalidar
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Planificaciones</div>
                <h3 class="mb-0"><?= e($indicadores['total_planificaciones'] ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Aula/Horario</div>
                <h3 class="mb-0"><?= e($indicadores['conflictos_aula_horario'] ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Docente/Horario</div>
                <h3 class="mb-0"><?= e($indicadores['conflictos_docente_horario'] ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Grupo/Horario</div>
                <h3 class="mb-0"><?= e($indicadores['conflictos_grupo_horario'] ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Grupo/Materia</div>
                <h3 class="mb-0"><?= e($indicadores['conflictos_grupo_materia'] ?? 0) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">Total conflictos</div>
                <h3 class="mb-0"><?= e($indicadores['total_conflictos'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>

<?php if (($indicadores['total_conflictos'] ?? 0) > 0): ?>
    <div class="alert alert-danger">
        <strong>CU07 detectó conflictos.</strong>
        Revisa los grupos siguientes y corrige la asignación desde el módulo de planificación horaria.
    </div>
<?php else: ?>
    <div class="alert alert-success">
        <strong>CU07 validado correctamente.</strong>
        No existen cruces de aula, docente, grupo ni materias duplicadas en la planificación actual.
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Conflictos por aula y horario</span>
        <span class="badge bg-danger"><?= e(count($conflictosAulaHorario ?? [])) ?></span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Aula</th>
                        <th>Horario</th>
                        <th>Coincidencias</th>
                        <th>Planificaciones involucradas</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($conflictosAulaHorario)): ?>
                        <?php foreach ($conflictosAulaHorario as $conflicto): ?>
                            <tr>
                                <td>
                                    <strong><?= e($conflicto['aula_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        Bloque <?= e($conflicto['bloque'] ?: '-') ?>,
                                        Aula <?= e($conflicto['numero'] ?: '-') ?>,
                                        Capacidad <?= e($conflicto['aula_capacidad'] ?: '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= e($conflicto['horario_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($conflicto['dia'] ?: '-') ?> |
                                        <?= e(substr((string)$conflicto['hora_inicio'], 0, 5)) ?> -
                                        <?= e(substr((string)$conflicto['hora_fin'], 0, 5)) ?> |
                                        <?= e($conflicto['turno'] ?: '-') ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-danger"><?= e($conflicto['cantidad_conflictos']) ?></span></td>
                                <td><?= e($conflicto['planificacion_ids']) ?></td>
                                <td class="small"><?= e($conflicto['detalle_asignaciones']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No hay conflictos de aula en el mismo horario.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Conflictos por docente y horario</span>
        <span class="badge bg-danger"><?= e(count($conflictosDocenteHorario ?? [])) ?></span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Horario</th>
                        <th>Coincidencias</th>
                        <th>Planificaciones involucradas</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($conflictosDocenteHorario)): ?>
                        <?php foreach ($conflictosDocenteHorario as $conflicto): ?>
                            <tr>
                                <td>
                                    <strong><?= e(trim(($conflicto['docente_nombres'] ?? '') . ' ' . ($conflicto['docente_apellidos'] ?? ''))) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($conflicto['docente_codigo']) ?> · CI: <?= e($conflicto['docente_ci']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= e($conflicto['horario_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($conflicto['dia'] ?: '-') ?> |
                                        <?= e(substr((string)$conflicto['hora_inicio'], 0, 5)) ?> -
                                        <?= e(substr((string)$conflicto['hora_fin'], 0, 5)) ?> |
                                        <?= e($conflicto['turno'] ?: '-') ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-danger"><?= e($conflicto['cantidad_conflictos']) ?></span></td>
                                <td><?= e($conflicto['planificacion_ids']) ?></td>
                                <td class="small"><?= e($conflicto['detalle_asignaciones']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No hay docentes asignados a dos clases en el mismo horario.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Conflictos por grupo y horario</span>
        <span class="badge bg-danger"><?= e(count($conflictosGrupoHorario ?? [])) ?></span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Grupo</th>
                        <th>Horario</th>
                        <th>Coincidencias</th>
                        <th>Planificaciones involucradas</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($conflictosGrupoHorario)): ?>
                        <?php foreach ($conflictosGrupoHorario as $conflicto): ?>
                            <tr>
                                <td>
                                    <strong><?= e($conflicto['grupo_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($conflicto['grupo_nombre'] ?: '-') ?> · <?= e($conflicto['grupo_estado']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= e($conflicto['horario_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($conflicto['dia'] ?: '-') ?> |
                                        <?= e(substr((string)$conflicto['hora_inicio'], 0, 5)) ?> -
                                        <?= e(substr((string)$conflicto['hora_fin'], 0, 5)) ?> |
                                        <?= e($conflicto['turno'] ?: '-') ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-danger"><?= e($conflicto['cantidad_conflictos']) ?></span></td>
                                <td><?= e($conflicto['planificacion_ids']) ?></td>
                                <td class="small"><?= e($conflicto['detalle_asignaciones']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No hay grupos con dos materias en el mismo horario.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Conflictos por grupo y materia repetida</span>
        <span class="badge bg-danger"><?= e(count($conflictosGrupoMateria ?? [])) ?></span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Grupo</th>
                        <th>Materia</th>
                        <th>Coincidencias</th>
                        <th>Planificaciones involucradas</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($conflictosGrupoMateria)): ?>
                        <?php foreach ($conflictosGrupoMateria as $conflicto): ?>
                            <tr>
                                <td>
                                    <strong><?= e($conflicto['grupo_codigo']) ?></strong><br>
                                    <span class="text-muted small"><?= e($conflicto['grupo_nombre'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <strong><?= e($conflicto['materia_nombre']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($conflicto['materia_codigo']) ?>
                                        <?= $conflicto['materia_sigla'] ? ' · ' . e($conflicto['materia_sigla']) : '' ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-danger"><?= e($conflicto['cantidad_conflictos']) ?></span></td>
                                <td><?= e($conflicto['planificacion_ids']) ?></td>
                                <td class="small"><?= e($conflicto['detalle_asignaciones']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No hay materias repetidas dentro del mismo grupo.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Planificaciones afectadas por conflictos</span>
        <span class="badge bg-secondary"><?= e(count($planificacionesConflictivas ?? [])) ?></span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Grupo</th>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th>Horario</th>
                        <th>Aula</th>
                        <th width="110">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($planificacionesConflictivas)): ?>
                        <?php foreach ($planificacionesConflictivas as $fila): ?>
                            <tr>
                                <td><?= e($fila['id']) ?></td>
                                <td>
                                    <strong><?= e($fila['grupo_codigo']) ?></strong><br>
                                    <span class="text-muted small"><?= e($fila['grupo_nombre'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <strong><?= e($fila['materia_nombre']) ?></strong><br>
                                    <span class="text-muted small"><?= e($fila['materia_codigo']) ?></span>
                                </td>
                                <td>
                                    <strong><?= e(trim(($fila['docente_nombres'] ?? '') . ' ' . ($fila['docente_apellidos'] ?? ''))) ?></strong><br>
                                    <span class="text-muted small"><?= e($fila['docente_codigo']) ?> · CI: <?= e($fila['docente_ci']) ?></span>
                                </td>
                                <td>
                                    <strong><?= e($fila['horario_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($fila['dia'] ?: '-') ?> |
                                        <?= e(substr((string)$fila['hora_inicio'], 0, 5)) ?> -
                                        <?= e(substr((string)$fila['hora_fin'], 0, 5)) ?> |
                                        <?= e($fila['turno'] ?: '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= e($fila['aula_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        Bloque <?= e($fila['bloque'] ?: '-') ?>,
                                        Aula <?= e($fila['numero'] ?: '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= e(url('/planificacion-horaria/edit') . '&id=' . $fila['id']) ?>" class="btn btn-sm btn-warning">
                                        Corregir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No hay planificaciones afectadas por conflictos.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>