<?php $titulo = 'Asentar Notas Manuales - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Asentar notas manuales</h2>
        <p class="text-muted mb-0">Grupo ID: <?= e($grupoId) ?></p>
    </div>
    <a href="<?= e(url('/examenes')) ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= e(url('/examenes/storeNotas')) ?>" method="POST">
            <input type="hidden" name="grupo_id" value="<?= e($grupoId) ?>">

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Postulante</th>
                            <?php foreach ($examenes as $ex): ?>
                                <th class="text-center"><?= e($ex['nombre']) ?> (<?= e($ex['ponderacion']) ?>%)</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Organizar notas por postulante
                        $notasPorPostulante = [];
                        $datosPostulante = [];
                        foreach ($notas as $n) {
                            $notasPorPostulante[$n['postulante_id']][$n['examen_id']] = $n['valor'];
                            if (!isset($datosPostulante[$n['postulante_id']])) {
                                $datosPostulante[$n['postulante_id']] = [
                                    'nombres' => $n['nombres'] ?? '',
                                    'apellidos' => $n['apellidos'] ?? ''
                                ];
                            }
                        }

                        $postulantesIds = array_unique(array_column($notas, 'postulante_id'));
                        foreach ($postulantesIds as $pid):
                            $nombreCompleto = trim(($datosPostulante[$pid]['nombres'] ?? '') . ' ' . ($datosPostulante[$pid]['apellidos'] ?? ''));
                            if ($nombreCompleto == '') {
                                $nombreCompleto = 'Postulante ID ' . $pid;
                            }
                        ?>
                            <tr>
                                <td><strong><?= e($nombreCompleto) ?></strong></td>
                                <?php foreach ($examenes as $ex): ?>
                                    <td class="text-center">
                                        <input type="number" min="0" max="100" step="0.01" required
                                               name="notas[<?= $pid ?>][<?= $ex['id'] ?>]"
                                               value="<?= e($notasPorPostulante[$pid][$ex['id']] ?? '') ?>"
                                               class="form-control" style="width: 80px; margin: 0 auto;">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar Notas</button>
                <a href="<?= e(url('/examenes')) ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>