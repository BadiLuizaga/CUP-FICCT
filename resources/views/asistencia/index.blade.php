<<?php $titulo = 'Registro de Asistencia - SIGAUCP'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Registro de asistencia</h2>
        <p class="text-muted mb-0">CU10 - Registrar asistencia en aula web / vista móvil</p>
    </div>
    <div>
        <a href="<?= e(url('/asistencia/reporte')) ?>" class="btn btn-info me-2">
            📊 Ver Reporte
        </a>
        <a href="<?= e(url('/dashboard')) ?>" class="btn btn-secondary">
            Volver al Dashboard
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Seleccionar grupo y materia</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($grupos)): ?>
                    <form action="/index.php" method="GET" class="row g-3">
                        <input type="hidden" name="url" value="/asistencia/registrar">
                        
                        <div class="col-md-6">
                            <label class="form-label">Grupo académico</label>
                            <select name="grupo_id" id="grupo_id" class="form-select" required>
                                <option value="">Seleccione un grupo</option>
                                <?php foreach ($grupos as $grupo): ?>
                                    <option value="<?= e($grupo['grupo_id']) ?>">
                                        <?= e($grupo['grupo_codigo']) ?> - <?= e($grupo['grupo_nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Materia</label>
                            <select name="materia_id" id="materia_id" class="form-select" required>
                                <option value="">Primero seleccione un grupo</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                Continuar
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        No hay grupos activos con materias asignadas.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    const grupos = <?= json_encode($grupos) ?>;

    const grupoSelect = document.getElementById('grupo_id');
    const materiaSelect = document.getElementById('materia_id');

    grupoSelect.addEventListener('change', function() {
        const grupoId = parseInt(this.value);
        const grupo = grupos.find(g => g.grupo_id === grupoId);

        materiaSelect.innerHTML = '<option value="">Seleccione una materia</option>';

        if (grupo && grupo.materias) {
            grupo.materias.forEach(materia => {
                const option = document.createElement('option');
                option.value = materia.id;
                option.textContent = materia.codigo + ' - ' + materia.nombre;
                materiaSelect.appendChild(option);
            });
        }
    });
</script>