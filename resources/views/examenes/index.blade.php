<?php $titulo = 'Exámenes Parciales - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Exámenes parciales</h2>
        <p class="text-muted mb-0">Seleccione un examen para rendir</p>
    </div>
    <a href="<?= e(url('/dashboard')) ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($examenesDisponibles)): ?>
            <div class="alert alert-warning">
                No hay exámenes disponibles en este momento.
            </div>
        <?php else: ?>
            <form action="/index.php" method="GET" class="row g-3">
                <input type="hidden" name="url" value="/examenes/rendir">
                <div class="col-md-8">
                    <label class="form-label required">Seleccione un examen</label>
                    <select name="examen_id" class="form-select" required>
                        <option value="">Seleccione un examen</option>
                        <?php foreach ($examenesDisponibles as $ex): ?>
                            <option value="<?= e($ex['id']) ?>">
                                <?= e($ex['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Rendir Examen</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Para docentes</h5>
        </div>
        <div class="card-body">
            <a href="<?= e(url('/notas')) ?>" class="btn btn-outline-primary">Asentar notas manuales</a>
        </div>
    </div>
</div>