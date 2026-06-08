<?php $titulo = 'Planificación Horaria - SIGIE'; ?>

<?php
$totalPlanificaciones = (int)($indicadores['total_planificaciones'] ?? 0);
$totalGrupos = (int)($indicadores['total_grupos_planificados'] ?? 0);
$totalDocentes = (int)($indicadores['total_docentes_asignados'] ?? 0);
$totalAulas = (int)($indicadores['total_aulas_usadas'] ?? 0);
$totalHorarios = (int)($indicadores['total_horarios_usados'] ?? 0);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Planificación horaria</h2>
        <p class="text-muted mb-0">
            CU06 - Asignación de aulas, horarios, grupos, materias y docentes
        </p>
    </div>

    <a href="<?= e(url('/planificacion-horaria/create')) ?>" class="btn btn-primary">
        Nueva planificación
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="text-muted mb-1">Planificaciones</h6>
                <h3 class="mb-0"><?= e($totalPlanificaciones) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-success">
            <div class="card-body">
                <h6 class="text-muted mb-1">Grupos</h6>
                <h3 class="mb-0"><?= e($totalGrupos) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-info">
            <div class="card-body">
                <h6 class="text-muted mb-1">Docentes</h6>
                <h3 class="mb-0"><?= e($totalDocentes) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-warning">
            <div class="card-body">
                <h6 class="text-muted mb-1">Aulas</h6>
                <h3 class="mb-0"><?= e($totalAulas) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-secondary">
            <div class="card-body">
                <h6 class="text-muted mb-1">Horarios usados</h6>
                <h3 class="mb-0"><?= e($totalHorarios) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info">
    Este módulo ya evita cruces básicos: un aula no puede estar ocupada dos veces en el mismo horario,
    un docente no puede tener dos clases en el mismo horario y un grupo no puede tener dos materias al mismo tiempo.
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Grupo</th>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th>Horario</th>
                        <th>Aula</th>
                        <th>Gestión</th>
                        <th width="160">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($planificaciones)): ?>
                        <?php foreach ($planificaciones as $planificacion): ?>
                            <tr>
                                <td><?= e($planificacion['id']) ?></td>

                                <td>
                                    <strong><?= e($planificacion['grupo_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($planificacion['grupo_nombre'] ?: '-') ?>
                                    </span>
                                </td>

                                <td>
                                    <strong><?= e($planificacion['materia_nombre']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($planificacion['materia_codigo']) ?>
                                        <?= $planificacion['materia_sigla'] ? ' - ' . e($planificacion['materia_sigla']) : '' ?>
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        <?= e(trim(($planificacion['docente_nombres'] ?? '') . ' ' . ($planificacion['docente_apellidos'] ?? ''))) ?>
                                    </strong><br>
                                    <span class="text-muted small">
                                        <?= e($planificacion['docente_codigo']) ?> · CI: <?= e($planificacion['docente_ci']) ?>
                                    </span>
                                </td>

                                <td>
                                    <strong><?= e($planificacion['horario_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        <?= e($planificacion['dia'] ?: '-') ?> |
                                        <?= e(substr((string)$planificacion['hora_inicio'], 0, 5)) ?> -
                                        <?= e(substr((string)$planificacion['hora_fin'], 0, 5)) ?>
                                    </span><br>
                                    <span class="badge bg-secondary">
                                        <?= e($planificacion['turno'] ?: '-') ?>
                                    </span>
                                </td>

                                <td>
                                    <strong><?= e($planificacion['aula_codigo']) ?></strong><br>
                                    <span class="text-muted small">
                                        Bloque <?= e($planificacion['bloque'] ?: '-') ?>,
                                        Aula <?= e($planificacion['numero'] ?: '-') ?>
                                    </span>
                                </td>

                                <td>
                                    <?= e($planificacion['periodo_codigo'] ?: '-') ?>
                                </td>

                                <td>
                                    <a href="<?= e(url('/planificacion-horaria/show') . '&id=' . $planificacion['id']) ?>" class="btn btn-sm btn-info text-white">
                                        Ver
                                    </a>

                                    <a href="<?= e(url('/planificacion-horaria/edit') . '&id=' . $planificacion['id']) ?>" class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No hay planificación horaria registrada.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>