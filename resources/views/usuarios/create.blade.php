<?php $titulo = 'Nuevo Usuario - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Nuevo usuario</h2>
        <p class="text-muted mb-0">Registrar persona, usuario y rol</p>
    </div>

    <a href="<?= e(url('/usuarios')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<form action="<?= e(url('/usuarios/store')) ?>" method="POST">
    <div class="card mb-4">
        <div class="card-header">
            Datos personales
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">CI</label>
                    <input type="text" name="ci" class="form-control" value="<?= e(old('ci')) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Nombres</label>
                    <input type="text" name="nombres" class="form-control" value="<?= e(old('nombres')) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" value="<?= e(old('apellidos')) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?= e(old('fecha_nacimiento')) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="M" <?= selected_value(old('sexo'), 'M') ?>>Masculino</option>
                        <option value="F" <?= selected_value(old('sexo'), 'F') ?>>Femenino</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= e(old('telefono')) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="<?= e(old('direccion')) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Datos de acceso
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Usuario</label>
                    <input type="text" name="username" class="form-control" value="<?= e(old('username')) ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                    <small class="text-muted">Mínimo 6 caracteres.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Rol</label>
                    <select name="rol_id" class="form-select" required>
                        <option value="">Seleccione un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= e($rol['id']) ?>" <?= selected_value(old('rol_id'), $rol['id']) ?>>
                                <?= e($rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input 
                            type="checkbox" 
                            name="estado" 
                            id="estado" 
                            class="form-check-input" 
                            value="1" 
                            <?= old('estado', '1') ? 'checked' : '' ?>
                        >
                        <label for="estado" class="form-check-label">Usuario activo</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        Guardar usuario
    </button>

    <a href="<?= e(url('/usuarios')) ?>" class="btn btn-secondary">
        Cancelar
    </a>
</form>