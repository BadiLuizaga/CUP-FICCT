<?php $titulo = 'Detalle de Planificación Horaria - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Detalle de planificación horaria</h2>
        <p class="text-muted mb-0">
            Información completa de la asignación grupo-materia
        </p>
    </div>

    <div>
        <a href="<?= e(url('/planificacion-horaria/edit') . '&id=' . $planificacion['id']) ?>" class="btn btn-warning">
            Editar
        </a>

        <a href="<?= e(url('/planificacion-horaria')) ?>" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Datos generales
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <strong>ID planificación:</strong><br>
                <?= e($planificacion['id']) ?>
            </div>

            <div class="col-md-3">
                <strong>Gestión:</strong><br>
                <?= e($planificacion['periodo_codigo'] ?: '-') ?>
            </div>

            <div class="col-md-3">
                <strong>Periodo:</strong><br>
                <?= e($planificacion['gestion'] ?: '-') ?> /
                <?= e($planificacion['semestre'] ?: '-') ?>
            </div>

            <div class="col-md-3">
                <strong>Estado del grupo:</strong><br>
                <?php if ($planificacion['grupo_estado'] === 'Activo'): ?>
                    <span class="badge bg-success">Activo</span>
                <?php elseif ($planificacion['grupo_estado'] === 'Saturado'): ?>
                    <span class="badge bg-danger">Saturado</span>
                <?php elseif ($planificacion['grupo_estado'] === 'Inactivo'): ?>
                    <span class="badge bg-secondary">Inactivo</span>
                <?php else: ?>
                    <span class="badge bg-dark"><?= e($planificacion['grupo_estado']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                Grupo y materia
            </div>

            <div class="card-body">
                <p>
                    <strong>Grupo:</strong><br>
                    <?= e($planificacion['grupo_codigo']) ?> -
                    <?= e($planificacion['grupo_nombre'] ?: '-') ?>
                </p>

                <p>
                    <strong>Capacidad:</strong><br>
                    <?= e($planificacion['grupo_capacidad']) ?> estudiantes
                </p>

                <p>
                    <strong>Estudiantes actuales:</strong><br>
                    <?= e($planificacion['cantidad_estudiantes']) ?> inscritos
                </p>

                <hr>

                <p>
                    <strong>Materia:</strong><br>
                    <?= e($planificacion['materia_codigo']) ?> -
                    <?= e($planificacion['materia_nombre']) ?>
                </p>

                <p>
                    <strong>Sigla:</strong><br>
                    <?= e($planificacion['materia_sigla'] ?: '-') ?>
                </p>

                <p class="mb-0">
                    <strong>Área:</strong><br>
                    <?= e($planificacion['materia_area'] ?: '-') ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                Docente asignado
            </div>

            <div class="card-body">
                <p>
                    <strong>Código docente:</strong><br>
                    <?= e($planificacion['docente_codigo']) ?>
                </p>

                <p>
                    <strong>Nombre completo:</strong><br>
                    <?= e(trim(($planificacion['docente_nombres'] ?? '') . ' ' . ($planificacion['docente_apellidos'] ?? ''))) ?>
                </p>

                <p>
                    <strong>CI:</strong><br>
                    <?= e($planificacion['docente_ci']) ?>
                </p>

                <p>
                    <strong>Email:</strong><br>
                    <?= e($planificacion['docente_email'] ?: '-') ?>
                </p>

                <p>
                    <strong>Teléfono:</strong><br>
                    <?= e($planificacion['docente_telefono'] ?: '-') ?>
                </p>

                <p class="mb-0">
                    <strong>Estado contrato:</strong><br>
                    <?= e($planificacion['estado_contrato']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                Horario
            </div>

            <div class="card-body">
                <p>
                    <strong>Código horario:</strong><br>
                    <?= e($planificacion['horario_codigo']) ?>
                </p>

                <p>
                    <strong>Día:</strong><br>
                    <?= e($planificacion['dia'] ?: '-') ?>
                </p>

                <p>
                    <strong>Hora:</strong><br>
                    <?= e(substr((string)$planificacion['hora_inicio'], 0, 5)) ?> -
                    <?= e(substr((string)$planificacion['hora_fin'], 0, 5)) ?>
                </p>

                <p>
                    <strong>Modalidad:</strong><br>
                    <?= e($planificacion['modalidad'] ?: '-') ?>
                </p>

                <p class="mb-0">
                    <strong>Turno:</strong><br>
                    <?= e($planificacion['turno'] ?: '-') ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                Aula
            </div>

            <div class="card-body">
                <p>
                    <strong>Código aula:</strong><br>
                    <?= e($planificacion['aula_codigo']) ?>
                </p>

                <p>
                    <strong>Bloque:</strong><br>
                    <?= e($planificacion['bloque'] ?: '-') ?>
                </p>

                <p>
                    <strong>Número:</strong><br>
                    <?= e($planificacion['numero'] ?: '-') ?>
                </p>

                <p>
                    <strong>Capacidad:</strong><br>
                    <?= e($planificacion['aula_capacidad']) ?> estudiantes
                </p>

                <p class="mb-0">
                    <strong>Tipo:</strong><br>
                    <?= e($planificacion['aula_tipo'] ?: '-') ?>
                </p>
            </div>
        </div>
    </div>
</div>