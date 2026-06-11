<?php $titulo = 'Asentar Calificaciones - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Asentar calificaciones</h2>
        <p class="text-muted mb-0">Registro de notas por parciales</p>
    </div>
    <a href="<?= e(url('/examenes')) ?>" class="btn btn-secondary">Volver a Exámenes</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="/index.php" method="GET" class="row g-3">
            <input type="hidden" name="url" value="/notas/edit">
            <div class="col-md-8">
                <label class="form-label required">Seleccione un grupo</label>
                <select name="grupo_id" class="form-select" required>
                    <option value="">Seleccione un grupo</option>
                    <?php foreach ($grupos as $g): ?>
                        <option value="<?= e($g['id']) ?>">
                            <?= e($g['codigo']) ?> - <?= e($g['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Ver Notas</button>
            </div>
        </form>
    </div>
</div>