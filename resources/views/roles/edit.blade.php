<?php $titulo = 'Editar Rol - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Editar rol</h2>
        <p class="text-muted mb-0">Modificar información del rol</p>
    </div>

    <a href="<?= e(url('/roles')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= e(url('/roles/update')) ?>" method="POST">
            <input type="hidden" name="id" value="<?= e($rol['id']) ?>">

            <div class="mb-3">
                <label class="form-label required">Nombre del rol</label>
                <input 
                    type="text" 
                    name="nombre" 
                    class="form-control" 
                    value="<?= e(old('nombre', $rol['nombre'])) ?>" 
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea 
                    name="descripcion" 
                    class="form-control" 
                    rows="4"
                ><?= e(old('descripcion', $rol['descripcion'])) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Actualizar rol
            </button>

            <a href="<?= e(url('/roles')) ?>" class="btn btn-secondary">
                Cancelar
            </a>
        </form>
    </div>
</div>