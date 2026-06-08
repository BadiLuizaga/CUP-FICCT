<?php $titulo = 'Nuevo Rol - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Nuevo rol</h2>
        <p class="text-muted mb-0">Registrar un nuevo rol para usuarios</p>
    </div>

    <a href="<?= e(url('/roles')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= e(url('/roles/store')) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label required">Nombre del rol</label>
                <input 
                    type="text" 
                    name="nombre" 
                    class="form-control" 
                    value="<?= e(old('nombre')) ?>" 
                    placeholder="Ejemplo: Administrador"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea 
                    name="descripcion" 
                    class="form-control" 
                    rows="4"
                    placeholder="Descripción del rol"
                ><?= e(old('descripcion')) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Guardar rol
            </button>

            <a href="<?= e(url('/roles')) ?>" class="btn btn-secondary">
                Cancelar
            </a>
        </form>
    </div>
</div>