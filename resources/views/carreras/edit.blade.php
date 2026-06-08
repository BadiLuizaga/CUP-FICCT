<?php $titulo = 'Editar Carrera - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Editar carrera</h2>
        <p class="text-muted mb-0">Modificar datos de la carrera y su cupo máximo</p>
    </div>

    <a href="<?= e(url('/carreras')) ?>" class="btn btn-secondary">
        Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= e(url('/carreras/update')) ?>" method="POST">
            <input type="hidden" name="id" value="<?= e($carrera['id']) ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Código</label>
                    <input
                        type="text"
                        name="codigo"
                        class="form-control"
                        value="<?= e(old('codigo', $carrera['codigo'])) ?>"
                        required
                    >
                </div>

                <div class="col-md-5">
                    <label class="form-label required">Nombre de la carrera</label>
                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        value="<?= e(old('nombre', $carrera['nombre'])) ?>"
                        required
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label required">Cupo máximo</label>
                    <input
                        type="number"
                        name="cupo_maximo"
                        class="form-control"
                        value="<?= e(old('cupo_maximo', $carrera['cupo_maximo'])) ?>"
                        min="1"
                        required
                    >
                </div>

                <div class="col-md-12">
                    <label class="form-label">Descripción</label>
                    <textarea
                        name="descripcion"
                        class="form-control"
                        rows="4"
                    ><?= e(old('descripcion', $carrera['descripcion'])) ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    Actualizar carrera
                </button>

                <a href="<?= e(url('/carreras')) ?>" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>