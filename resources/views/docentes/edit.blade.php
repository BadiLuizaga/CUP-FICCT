<?php $titulo = 'Editar Docente - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Editar docente</h2>
        <p class="text-muted mb-0">Modificar expediente docente</p>
    </div>

    <a href="<?= e(url('/docentes')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= e(url('/docentes/update')) ?>" method="POST">
            <input type="hidden" name="id" value="<?= e($docente['id']) ?>">

            <h5 class="mb-3">Datos personales</h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label required">CI</label>
                    <input
                        type="text"
                        name="ci"
                        class="form-control"
                        value="<?= e(old('ci', $docente['ci'])) ?>"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Nombres</label>
                    <input
                        type="text"
                        name="nombres"
                        class="form-control"
                        value="<?= e(old('nombres', $docente['nombres'])) ?>"
                        required
                    >
                </div>

                <div class="col-md-5">
                    <label class="form-label required">Apellidos</label>
                    <input
                        type="text"
                        name="apellidos"
                        class="form-control"
                        value="<?= e(old('apellidos', $docente['apellidos'])) ?>"
                        required
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input
                        type="date"
                        name="fecha_nacimiento"
                        class="form-control"
                        value="<?= e(old('fecha_nacimiento', $docente['fecha_nacimiento'])) ?>"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">Seleccionar</option>
                        <option value="M" <?= selected_value(old('sexo', $docente['sexo']), 'M') ?>>Masculino</option>
                        <option value="F" <?= selected_value(old('sexo', $docente['sexo']), 'F') ?>>Femenino</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input
                        type="text"
                        name="telefono"
                        class="form-control"
                        value="<?= e(old('telefono', $docente['telefono'])) ?>"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= e(old('email', $docente['email'])) ?>"
                    >
                </div>

                <div class="col-md-12">
                    <label class="form-label">Dirección</label>
                    <textarea
                        name="direccion"
                        class="form-control"
                        rows="2"
                    ><?= e(old('direccion', $docente['direccion'])) ?></textarea>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="mb-3">Datos académicos y laborales</h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label required">Código docente</label>
                    <input
                        type="text"
                        name="codigo"
                        class="form-control"
                        value="<?= e(old('codigo', $docente['codigo'])) ?>"
                        required
                    >
                </div>

                <div class="col-md-5">
                    <label class="form-label required">Profesión</label>
                    <input
                        type="text"
                        name="profesion"
                        class="form-control"
                        value="<?= e(old('profesion', $docente['profesion'])) ?>"
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Maestría</label>
                    <input
                        type="text"
                        name="maestria"
                        class="form-control"
                        value="<?= e(old('maestria', $docente['maestria'])) ?>"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Experiencia</label>
                    <input
                        type="text"
                        name="experiencia"
                        class="form-control"
                        value="<?= e(old('experiencia', $docente['experiencia'])) ?>"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Estado de contrato</label>
                    <select name="estado_contrato" class="form-select" required>
                        <?php foreach ($estadosContrato as $estado): ?>
                            <option value="<?= e($estado) ?>" <?= selected_value(old('estado_contrato', $docente['estado_contrato']), $estado) ?>>
                                <?= e($estado) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="diplomado_educacion"
                            id="diplomado_educacion"
                            value="1"
                            <?= is_active(old('diplomado_educacion', $docente['diplomado_educacion'])) ? 'checked' : '' ?>
                        >
                        <label class="form-check-label" for="diplomado_educacion">
                            Cuenta con diplomado en educación
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    Actualizar docente
                </button>

                <a href="<?= e(url('/docentes')) ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>