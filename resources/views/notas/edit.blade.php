<?php $titulo = 'Registrar Notas - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Registrar notas</h2>
        <p class="text-muted mb-0">Grupo ID: <?= e($grupoId) ?></p>
    </div>
    <a href="<?= e(url('/notas')) ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= e(url('/notas/store')) ?>" method="POST">
            <input type="hidden" name="grupo_id" value="<?= e($grupoId) ?>">

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Estudiante</th>
                            <?php foreach ($examenes as $ex): ?>
                                <th class="text-center"><?= e($ex['nombre']) ?> (<?= e($ex['ponderacion']) ?>%)</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estudiantes as $e): ?>
                            <tr>
                                <td>
                                    <strong><?= e($e['nombres']) ?> <?= e($e['apellidos']) ?></strong><br>
                                    <small class="text-muted">CI: <?= e($e['ci']) ?></small>
                                </td>
                                <?php foreach ($examenes as $ex): ?>
                                    <td class="text-center">
                                        <input type="number" min="0" max="100" step="0.01"
                                               name="notas[<?= $e['postulante_id'] ?>][<?= $ex['id'] ?>]"
                                               value="<?= e($mapaNotas[$e['postulante_id']][$ex['id']] ?? '') ?>"
                                               class="form-control" style="width: 100px; margin: 0 auto;">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar Notas</button>
                <a href="<?= e(url('/notas')) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>