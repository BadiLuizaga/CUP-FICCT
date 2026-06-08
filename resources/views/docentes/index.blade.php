<?php $titulo = 'Docentes - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Docentes</h2>
        <p class="text-muted mb-0">CU03 - Administración del expediente de docentes</p>
    </div>

    <a href="<?= e(url('/docentes/create')) ?>" class="btn btn-primary">
        Nuevo docente
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            Desde este módulo puedes registrar, editar y consultar el expediente de docentes.
            También se muestra la relación del docente con grupos y materias cuando ya exista planificación en grupo_materia.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>CI</th>
                        <th>Docente</th>
                        <th>Profesión</th>
                        <th>Maestría</th>
                        <th>Diplomado</th>
                        <th>Experiencia</th>
                        <th>Estado</th>
                        <th>Asignaciones</th>
                        <th width="220">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($docentes)): ?>
                        <?php foreach ($docentes as $docente): ?>
                            <tr>
                                <td><?= e($docente['id']) ?></td>

                                <td>
                                    <strong><?= e($docente['codigo']) ?></strong>
                                </td>

                                <td><?= e($docente['ci']) ?></td>

                                <td>
                                    <?= e(trim(($docente['nombres'] ?? '') . ' ' . ($docente['apellidos'] ?? ''))) ?>
                                    <?php if (!empty($docente['email'])): ?>
                                        <div class="small text-muted">
                                            <?= e($docente['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td><?= e($docente['profesion'] ?: '-') ?></td>

                                <td><?= e($docente['maestria'] ?: '-') ?></td>

                                <td>
                                    <?php if (is_active($docente['diplomado_educacion'])): ?>
                                        <span class="badge bg-success">Sí</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>

                                <td><?= e($docente['experiencia'] ?: '-') ?></td>

                                <td>
                                    <?php if ($docente['estado_contrato'] === 'Activo'): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php elseif ($docente['estado_contrato'] === 'Inactivo'): ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php elseif ($docente['estado_contrato'] === 'Suspendido'): ?>
                                        <span class="badge bg-warning text-dark">Suspendido</span>
                                    <?php else: ?>
                                        <span class="badge bg-dark"><?= e($docente['estado_contrato']) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge bg-primary">
                                        <?= e($docente['total_asignaciones'] ?? 0) ?>
                                    </span>

                                    <div class="small text-muted mt-1">
                                        <?= e($docente['materias'] ?? 'Sin materias') ?>
                                    </div>

                                    <div class="small text-muted">
                                        Grupos: <?= e($docente['grupos'] ?? 'Sin grupos') ?>
                                    </div>
                                </td>

                                <td>
                                    <a
                                        href="<?= e(url('/docentes/show') . '&id=' . $docente['id']) ?>"
                                        class="btn btn-sm btn-info text-white"
                                    >
                                        Ver
                                    </a>

                                    <a
                                        href="<?= e(url('/docentes/edit') . '&id=' . $docente['id']) ?>"
                                        class="btn btn-sm btn-warning"
                                    >
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                No hay docentes registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>