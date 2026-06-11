<?php $titulo = 'Registrar Asistencia - SIGAUCP'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Registrar asistencia</h2>
        <p class="text-muted mb-0">Grupo: <?= e($grupo['codigo'] ?? '-') ?> - <?= e($grupo['nombre'] ?? '-') ?></p>
    </div>
    <a href="<?= e(url('/asistencia')) ?>" class="btn btn-secondary">
        ← Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($postulantes)): ?>
            <div class="alert alert-warning">No hay postulantes inscritos en este grupo.</div>
        <?php else: ?>
            <form method="POST" action="<?= e(url('/asistencia/store')) ?>">
                <input type="hidden" name="grupo_id" value="<?= e($grupoId) ?>">
                <input type="hidden" name="materia_id" value="<?= e($materiaId) ?>">
                <input type="hidden" name="fecha" value="<?= e($fecha) ?>">

                <div class="alert alert-info">
                    <strong>Fecha:</strong> <?= e(date('d/m/Y', strtotime($fecha))) ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>CI</th>
                                <th>Postulante</th>
                                <th>Estado</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($postulantes as $p): ?>
                                <?php 
                                    $pid = $p['postulante_id'];
                                    $estadoActual = $mapaAsistencias[$pid]['estado'] ?? 'Ausente';
                                ?>
                                <tr>
                                    <td><?= e($p['ci']) ?></td>
                                    <td><?= e(trim($p['nombres'] . ' ' . $p['apellidos'])) ?></td>
                                    <td>
                                        <select name="asistencia[<?= $pid ?>]" class="form-select">
                                            <option value="Presente" <?= $estadoActual === 'Presente' ? 'selected' : '' ?>>Presente</option>
                                            <option value="Ausente" <?= $estadoActual === 'Ausente' ? 'selected' : '' ?>>Ausente</option>
                                            <option value="Justificado" <?= $estadoActual === 'Justificado' ? 'selected' : '' ?>>Justificado</option>
                                            <option value="Tarde" <?= $estadoActual === 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                                            <option value="Licencia" <?= $estadoActual === 'Licencia' ? 'selected' : '' ?>>Licencia</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea name="observacion[<?= $pid ?>]" class="form-control" rows="1" placeholder="Opcional"><?= e($mapaAsistencias[$pid]['observacion'] ?? '') ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Guardar asistencia</button>
                    <button type="button" id="btnTodosPresente" class="btn btn-outline-secondary">Marcar todos como Presente</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('btnTodosPresente')?.addEventListener('click', function() {
        document.querySelectorAll('select[name^="asistencia["]').forEach(select => {
            select.value = 'Presente';
        });
    });
</script>