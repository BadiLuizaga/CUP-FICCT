<?php $titulo = 'Rendir Examen - SIGIE'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Rendir examen</h2>
        <p class="text-muted mb-0">Examen ID: <?= e($examenId) ?></p>
    </div>
    <a href="<?= e(url('/examenes')) ?>" class="btn btn-secondary">Volver</a>
</div>

<form action="<?= e(url('/examenes/storeRespuestas')) ?>" method="POST">
    <input type="hidden" name="postulante_id" value="<?= e($postulanteId) ?>">
    <input type="hidden" name="examen_id" value="<?= e($examenId) ?>">

    <?php foreach ($preguntas as $idx => $pregunta): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title">Pregunta <?= $idx + 1 ?>:</h6>
                <p class="card-text"><?= e($pregunta['enunciado']) ?></p>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="respuestas[<?= $pregunta['id'] ?>]" value="A" required>
                    <label class="form-check-label">A) <?= e($pregunta['opcion_a']) ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="respuestas[<?= $pregunta['id'] ?>]" value="B">
                    <label class="form-check-label">B) <?= e($pregunta['opcion_b']) ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="respuestas[<?= $pregunta['id'] ?>]" value="C">
                    <label class="form-check-label">C) <?= e($pregunta['opcion_c']) ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="respuestas[<?= $pregunta['id'] ?>]" value="D">
                    <label class="form-check-label">D) <?= e($pregunta['opcion_d']) ?></label>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">Enviar Examen</button>
        <a href="<?= e(url('/examenes')) ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<hr class="my-4">

<form action="<?= e(url('/examenes/finalizar')) ?>" method="POST">
    <button type="submit" class="btn btn-warning" onclick="return confirm('¿Estás seguro de finalizar y calcular tu promedio final?')">
        Finalizar Examen y Calcular Promedio Final
    </button>
</form>