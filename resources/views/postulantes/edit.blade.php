<?php $titulo = 'Editar Postulante - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Editar postulante</h2>
        <p class="text-muted mb-0">Modificar datos personales, gestión y opciones de carrera</p>
    </div>

    <a href="<?= e(url('/postulantes')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<form action="<?= e(url('/postulantes/update')) ?>" method="POST">
    <input type="hidden" name="id" value="<?= e($postulante['id']) ?>">

    <div class="card mb-4">
        <div class="card-header">
            Gestión del CUP
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Gestión / Periodo académico</label>
                    <select name="periodo_id" class="form-select" required>
                        <option value="">Seleccione una gestión</option>
                        <?php foreach ($periodos as $periodo): ?>
                            <option value="<?= e($periodo['id']) ?>" <?= selected_value(old('periodo_id', $postulante['periodo_id']), $periodo['id']) ?>>
                                <?= e($periodo['codigo']) ?> - <?= e($periodo['gestion']) ?>/<?= e($periodo['semestre']) ?>
                                (<?= e($periodo['estado']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Datos personales
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">CI</label>
                    <input type="text" name="ci" class="form-control" value="<?= e(old('ci', $postulante['ci'])) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Nombres</label>
                    <input type="text" name="nombres" class="form-control" value="<?= e(old('nombres', $postulante['nombres'])) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" value="<?= e(old('apellidos', $postulante['apellidos'])) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?= e(old('fecha_nacimiento', $postulante['fecha_nacimiento'])) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="M" <?= selected_value(old('sexo', $postulante['sexo']), 'M') ?>>Masculino</option>
                        <option value="F" <?= selected_value(old('sexo', $postulante['sexo']), 'F') ?>>Femenino</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e(old('email', $postulante['email'])) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= e(old('telefono', $postulante['telefono'])) ?>">
                </div>

                <div class="col-md-8">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="<?= e(old('direccion', $postulante['direccion'])) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Datos académicos
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Colegio</label>
                    <input type="text" name="colegio" class="form-control" value="<?= e(old('colegio', $postulante['colegio'])) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" value="<?= e(old('ciudad', $postulante['ciudad'])) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Título de bachiller</label>
                    <input type="text" name="titulo_bachiller" class="form-control" value="<?= e(old('titulo_bachiller', $postulante['titulo_bachiller'])) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Carrera principal</label>
                    <select name="carrera_principal_id" class="form-select" required>
                        <option value="">Seleccione una carrera</option>
                        <?php foreach ($carreras as $carrera): ?>
                            <option value="<?= e($carrera['id']) ?>" <?= selected_value(old('carrera_principal_id', $postulante['carrera_principal_id']), $carrera['id']) ?>>
                                <?= e($carrera['codigo']) ?> - <?= e($carrera['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Carrera secundaria</label>
                    <select name="carrera_secundaria_id" class="form-select" required>
                        <option value="">Seleccione una carrera</option>
                        <?php foreach ($carreras as $carrera): ?>
                            <option value="<?= e($carrera['id']) ?>" <?= selected_value(old('carrera_secundaria_id', $postulante['carrera_secundaria_id']), $carrera['id']) ?>>
                                <?= e($carrera['codigo']) ?> - <?= e($carrera['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Debe ser diferente a la carrera principal.</small>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        Actualizar postulante
    </button>

    <a href="<?= e(url('/postulantes')) ?>" class="btn btn-secondary">
        Cancelar
    </a>
</form>