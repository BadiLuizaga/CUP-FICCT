<?php $titulo = 'Detalle de Docente - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Detalle del docente</h2>
        <p class="text-muted mb-0">Expediente docente y asignaciones académicas</p>
    </div>

    <div>
        <a href="<?= e(url('/docentes/edit') . '&id=' . $docente['id']) ?>" class="btn btn-warning">
            Editar
        </a>

        <a href="<?= e(url('/docentes')) ?>" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Información del docente
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <strong>ID:</strong><br>
                <?= e($docente['id']) ?>
            </div>

            <div class="col-md-3">
                <strong>Código docente:</strong><br>
                <?= e($docente['codigo']) ?>
            </div>

            <div class="col-md-3">
                <strong>CI:</strong><br>
                <?= e($docente['ci']) ?>
            </div>

            <div class="col-md-3">
                <strong>Estado contrato:</strong><br>
                <?php if ($docente['estado_contrato'] === 'Activo'): ?>
                    <span class="badge bg-success">Activo</span>
                <?php elseif ($docente['estado_contrato'] === 'Inactivo'): ?>
                    <span class="badge bg-secondary">Inactivo</span>
                <?php elseif ($docente['estado_contrato'] === 'Suspendido'): ?>
                    <span class="badge bg-warning text-dark">Suspendido</span>
                <?php else: ?>
                    <span class="badge bg-dark"><?= e($docente['estado_contrato']) ?></span>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <strong>Nombre completo:</strong><br>
                <?= e(trim(($docente['nombres'] ?? '') . ' ' . ($docente['apellidos'] ?? ''))) ?>
            </div>

            <div class="col-md-3">
                <strong>Fecha de nacimiento:</strong><br>
                <?= e(format_date_short($docente['fecha_nacimiento'])) ?>
            </div>

            <div class="col-md-3">
                <strong>Sexo:</strong><br>
                <?php if ($docente['sexo'] === 'M'): ?>
                    Masculino
                <?php elseif ($docente['sexo'] === 'F'): ?>
                    Femenino
                <?php else: ?>
                    -
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <strong>Email:</strong><br>
                <?= e($docente['email'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Teléfono:</strong><br>
                <?= e($docente['telefono'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Dirección:</strong><br>
                <?= e($docente['direccion'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Profesión:</strong><br>
                <?= e($docente['profesion'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Maestría:</strong><br>
                <?= e($docente['maestria'] ?: '-') ?>
            </div>

            <div class="col-md-4">
                <strong>Diplomado en educación:</strong><br>
                <?php if (is_active($docente['diplomado_educacion'])): ?>
                    <span class="badge bg-success">Sí</span>
                <?php else: ?>
                    <span class="badge bg-secondary">No</span>
                <?php endif; ?>
            </div>

            <div class="col-md-12">
                <strong>Experiencia:</strong><br>
                <?= e($docente['experiencia'] ?: '-') ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Asignaciones actuales en grupo_materia
    </div>

    <div class="card-body">
        <?php if (!empty($asignaciones)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Grupo</th>
                            <th>Materia</th>
                            <th>Horario</th>
                            <th>Turno</th>
                            <th>Aula</th>
                            <th>Modalidad</th>
                            <th>Estado grupo</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($asignaciones as $asignacion): ?>
                            <tr>
                                <td>
                                    <strong><?= e($asignacion['grupo_codigo']) ?></strong><br>
                                    <span class="small text-muted"><?= e($asignacion['grupo_nombre']) ?></span>
                                </td>

                                <td>
                                    <strong><?= e($asignacion['materia_nombre']) ?></strong><br>
                                    <span class="small text-muted"><?= e($asignacion['materia_codigo']) ?></span>
                                </td>

                                <td>
                                    <?= e($asignacion['dia'] ?: '-') ?><br>
                                    <span class="small text-muted">
                                        <?= e($asignacion['hora_inicio'] ?: '-') ?> a <?= e($asignacion['hora_fin'] ?: '-') ?>
                                    </span>
                                </td>

                                <td><?= e($asignacion['turno'] ?: '-') ?></td>

                                <td>
                                    <?= e($asignacion['aula_codigo'] ?: '-') ?><br>
                                    <span class="small text-muted">
                                        <?= e($asignacion['bloque'] ?: '') ?> <?= e($asignacion['numero'] ?: '') ?>
                                    </span>
                                </td>

                                <td><?= e($asignacion['modalidad'] ?: '-') ?></td>

                                <td>
                                    <?php if ($asignacion['grupo_estado'] === 'Activo'): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php elseif ($asignacion['grupo_estado'] === 'Saturado'): ?>
                                        <span class="badge bg-danger">Saturado</span>
                                    <?php elseif ($asignacion['grupo_estado'] === 'Inactivo'): ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php else: ?>
                                        <span class="badge bg-dark"><?= e($asignacion['grupo_estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary mb-0">
                Este docente todavía no tiene asignaciones en grupo_materia.
            </div>
        <?php endif; ?>
    </div>
</div>