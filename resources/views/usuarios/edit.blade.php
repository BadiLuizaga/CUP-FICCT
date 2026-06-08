<?php $titulo = 'Editar Usuario - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Editar usuario</h2>
        <p class="text-muted mb-0">Modificar datos personales, acceso y rol</p>
    </div>

    <a href="<?= e(url('/usuarios')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<form action="<?= e(url('/usuarios/update')) ?>" method="POST">
    <input type="hidden" name="id" value="<?= e($usuario['id']) ?>">

    <div class="card mb-4">
        <div class="card-header">
            Datos personales
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">CI</label>
                    <input 
                        type="text" 
                        name="ci" 
                        class="form-control" 
                        value="<?= e(old('ci', $usuario['ci'])) ?>" 
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Nombres</label>
                    <input 
                        type="text" 
                        name="nombres" 
                        class="form-control" 
                        value="<?= e(old('nombres', $usuario['nombres'])) ?>" 
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Apellidos</label>
                    <input 
                        type="text" 
                        name="apellidos" 
                        class="form-control" 
                        value="<?= e(old('apellidos', $usuario['apellidos'])) ?>" 
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input 
                        type="date" 
                        name="fecha_nacimiento" 
                        class="form-control" 
                        value="<?= e(old('fecha_nacimiento', $usuario['fecha_nacimiento'])) ?>"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="M" <?= selected_value(old('sexo', $usuario['sexo']), 'M') ?>>Masculino</option>
                        <option value="F" <?= selected_value(old('sexo', $usuario['sexo']), 'F') ?>>Femenino</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input 
                        type="text" 
                        name="telefono" 
                        class="form-control" 
                        value="<?= e(old('telefono', $usuario['telefono'])) ?>"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        value="<?= e(old('email', $usuario['email'])) ?>"
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Dirección</label>
                    <input 
                        type="text" 
                        name="direccion" 
                        class="form-control" 
                        value="<?= e(old('direccion', $usuario['direccion'])) ?>"
                    >
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
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        value="<?= e(old('username', $usuario['username'])) ?>" 
                        required
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted">Déjalo vacío si no quieres cambiarla.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label required">Rol</label>
                    <select name="rol_id" class="form-select" required>
                        <option value="">Seleccione un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option 
                                value="<?= e($rol['id']) ?>" 
                                <?= selected_value(old('rol_id', $usuario['rol_id']), $rol['id']) ?>
                            >
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
                            <?= old('estado', is_active($usuario['estado']) ? '1' : '') ? 'checked' : '' ?>
                            <?= (int)($_SESSION['usuario']['id'] ?? 0) === (int)$usuario['id'] ? 'disabled' : '' ?>
                        >

                        <?php if ((int)($_SESSION['usuario']['id'] ?? 0) === (int)$usuario['id']): ?>
                            <input type="hidden" name="estado" value="1">
                        <?php endif; ?>

                        <label for="estado" class="form-check-label">Usuario activo</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        Actualizar usuario
    </button>

    <a href="<?= e(url('/usuarios')) ?>" class="btn btn-secondary">
        Cancelar
    </a>
</form>