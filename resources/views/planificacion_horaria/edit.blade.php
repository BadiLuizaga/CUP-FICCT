<?php $titulo = 'Editar Planificación Horaria - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Editar planificación horaria</h2>
        <p class="text-muted mb-0">
            Modificar asignación de grupo, materia, docente, horario y aula
        </p>
    </div>

    <a href="<?= e(url('/planificacion-horaria')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= e(url('/planificacion-horaria/update')) ?>" method="POST">
            <input type="hidden" name="id" value="<?= e($planificacion['id']) ?>">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label required">Grupo</label>
                    <select name="grupo_id" class="form-select" required>
                        <option value="">Seleccione un grupo</option>

                        <?php foreach (($catalogos['grupos'] ?? []) as $grupo): ?>
                            <option value="<?= e($grupo['id']) ?>" <?= selected_value(old('grupo_id', $planificacion['grupo_id']), $grupo['id']) ?>>
                                <?= e($grupo['codigo']) ?> - <?= e($grupo['nombre'] ?: 'Sin nombre') ?>
                                | <?= e($grupo['periodo_codigo'] ?: 'Sin gestión') ?>
                                | <?= e($grupo['estado']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Materia</label>
                    <select name="materia_id" class="form-select" required>
                        <option value="">Seleccione una materia</option>

                        <?php foreach (($catalogos['materias'] ?? []) as $materia): ?>
                            <option value="<?= e($materia['id']) ?>" <?= selected_value(old('materia_id', $planificacion['materia_id']), $materia['id']) ?>>
                                <?= e($materia['codigo']) ?> - <?= e($materia['nombre']) ?>
                                <?= $materia['sigla'] ? '(' . e($materia['sigla']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label required">Docente</label>
                    <select name="docente_id" class="form-select" required>
                        <option value="">Seleccione un docente</option>

                        <?php foreach (($catalogos['docentes'] ?? []) as $docente): ?>
                            <option value="<?= e($docente['id']) ?>" <?= selected_value(old('docente_id', $planificacion['docente_id']), $docente['id']) ?>>
                                <?= e($docente['codigo']) ?> -
                                <?= e(trim(($docente['nombres'] ?? '') . ' ' . ($docente['apellidos'] ?? ''))) ?>
                                | CI: <?= e($docente['ci']) ?>
                                | <?= e($docente['estado_contrato']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Horario</label>
                    <select name="horario_id" class="form-select" required>
                        <option value="">Seleccione un horario</option>

                        <?php foreach (($catalogos['horarios'] ?? []) as $horario): ?>
                            <option value="<?= e($horario['id']) ?>" <?= selected_value(old('horario_id', $planificacion['horario_id']), $horario['id']) ?>>
                                <?= e($horario['codigo']) ?> -
                                <?= e($horario['dia'] ?: '-') ?> |
                                <?= e(substr((string)$horario['hora_inicio'], 0, 5)) ?> -
                                <?= e(substr((string)$horario['hora_fin'], 0, 5)) ?>
                                | <?= e($horario['turno'] ?: '-') ?>
                                | <?= e($horario['modalidad'] ?: '-') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Aula</label>
                    <select name="aula_id" class="form-select" required>
                        <option value="">Seleccione un aula</option>

                        <?php foreach (($catalogos['aulas'] ?? []) as $aula): ?>
                            <option value="<?= e($aula['id']) ?>" <?= selected_value(old('aula_id', $planificacion['aula_id']), $aula['id']) ?>>
                                <?= e($aula['codigo']) ?> -
                                Bloque <?= e($aula['bloque'] ?: '-') ?>,
                                Aula <?= e($aula['numero'] ?: '-') ?>
                                | Capacidad: <?= e($aula['capacidad']) ?>
                                | <?= e($aula['tipo'] ?: '-') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="alert alert-warning mt-4 mb-0">
                Al actualizar, el sistema volverá a validar cruces de aula, docente y grupo en el mismo horario.
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    Actualizar planificación
                </button>

                <a href="<?= e(url('/planificacion-horaria')) ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>