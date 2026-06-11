<?php $titulo = 'Reporte de Asistencia - SIGAUCP'; ?>

<!-- Agregar Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
    .estado-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .estado-presente { background: #d1e7dd; color: #0a3622; }
    .estado-ausente { background: #f8d7da; color: #842029; }
    .estado-justificado { background: #fff3cd; color: #856404; }
    .estado-tarde { background: #cfe2ff; color: #084298; }
    .estado-licencia { background: #e2e3e5; color: #41464b; }
    .chart-container {
        max-width: 400px;
        margin: 0 auto;
    }
    @media print {
        .no-print { display: none !important; }
        .chart-container { page-break-inside: avoid; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="mb-1">Reporte de asistencia</h2>
        <p class="text-muted mb-0">Resumen de asistencia por grupo y período</p>
    </div>
    <div class="no-print">
        <a href="<?= e(url('/asistencia')) ?>" class="btn btn-secondary">← Volver</a>
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir</button>
    </div>
</div>

<!-- Formulario de filtros -->
<div class="card shadow-sm mb-4 no-print">
    <div class="card-body">
        <form action="/index.php" method="GET" class="row g-3">
            <input type="hidden" name="url" value="/asistencia/reporte">
            
            <div class="col-md-5">
                <label class="form-label">Grupo académico</label>
                <select name="grupo_id" class="form-select" required>
                    <option value="">Seleccione un grupo</option>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?= e($grupo['grupo_id']) ?>" <?= ($grupoId == $grupo['grupo_id']) ? 'selected' : '' ?>>
                            <?= e($grupo['grupo_codigo']) ?> - <?= e($grupo['grupo_nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= e($fechaInicio) ?>">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= e($fechaFin) ?>">
            </div>
            
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<?php if ($grupoId > 0 && !empty($postulantes)): ?>
    <!-- Información del grupo -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>Grupo:</strong><br>
                    <?= e($grupo['codigo'] ?? '-') ?> - <?= e($grupo['nombre'] ?? '-') ?>
                </div>
                <div class="col-md-3">
                    <strong>Período:</strong><br>
                    <?= e(date('d/m/Y', strtotime($fechaInicio))) ?> - <?= e(date('d/m/Y', strtotime($fechaFin))) ?>
                </div>
                <div class="col-md-3">
                    <strong>Total estudiantes:</strong><br>
                    <?= count($postulantes) ?>
                </div>
                <div class="col-md-3">
                    <strong>Total registros:</strong><br>
                    <?= array_sum($resumen) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- GRÁFICAS -->
    <div class="row g-4 mb-4">
        <!-- Gráfica de Pastel -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Distribución por estado</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="estadosChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráfica de Barras -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0">Total por estado</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="barrasChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de estados (tarjetas) -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="estado-badge estado-presente">Presente</div>
                    <h3 class="mt-2 mb-0"><?= e($resumen['Presente'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="estado-badge estado-ausente">Ausente</div>
                    <h3 class="mt-2 mb-0"><?= e($resumen['Ausente'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="estado-badge estado-justificado">Justificado</div>
                    <h3 class="mt-2 mb-0"><?= e($resumen['Justificado'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="estado-badge estado-tarde">Tarde</div>
                    <h3 class="mt-2 mb-0"><?= e($resumen['Tarde'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card text-center">
                <div class="card-body">
                    <div class="estado-badge estado-licencia">Licencia</div>
                    <h3 class="mt-2 mb-0"><?= e($resumen['Licencia'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de estudiantes -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Detalle por estudiante</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>CI</th>
                            <th>Estudiante</th>
                            <th class="text-center">Presente</th>
                            <th class="text-center">Ausente</th>
                            <th class="text-center">Justif.</th>
                            <th class="text-center">Tarde</th>
                            <th class="text-center">Licencia</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">% Asist.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postulantes as $p): ?>
                            <?php 
                                $pid = $p['postulante_id'];
                                $stats = $estadisticasEstudiantes[$pid] ?? [
                                    'total' => 0, 'presente' => 0, 'ausente' => 0,
                                    'justificado' => 0, 'tarde' => 0, 'licencia' => 0, 'porcentaje' => 0
                                ];
                                $color = $stats['porcentaje'] >= 80 ? 'text-success' : ($stats['porcentaje'] >= 60 ? 'text-warning' : 'text-danger');
                            ?>
                            <tr>
                                <td><?= e($p['ci']) ?></td>
                                <td><?= e(trim($p['nombres'] . ' ' . $p['apellidos'])) ?></td>
                                <td class="text-center"><?= e($stats['presente']) ?></td>
                                <td class="text-center"><?= e($stats['ausente']) ?></td>
                                <td class="text-center"><?= e($stats['justificado']) ?></td>
                                <td class="text-center"><?= e($stats['tarde']) ?></td>
                                <td class="text-center"><?= e($stats['licencia']) ?></td>
                                <td class="text-center"><?= e($stats['total']) ?></td>
                                <td class="text-center"><strong class="<?= $color ?>"><?= e($stats['porcentaje']) ?>%</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Datos para las gráficas
        const estados = ['Presente', 'Ausente', 'Justificado', 'Tarde', 'Licencia'];
        const valores = [
            <?= $resumen['Presente'] ?? 0 ?>,
            <?= $resumen['Ausente'] ?? 0 ?>,
            <?= $resumen['Justificado'] ?? 0 ?>,
            <?= $resumen['Tarde'] ?? 0 ?>,
            <?= $resumen['Licencia'] ?? 0 ?>
        ];
        const colores = ['#198754', '#dc3545', '#ffc107', '#0d6efd', '#6c757d'];

        // Gráfica de Pastel
        const ctxPie = document.getElementById('estadosChart')?.getContext('2d');
        if (ctxPie) {
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: estados,
                    datasets: [{
                        data: valores,
                        backgroundColor: colores,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: function(tooltipItem) {
                            const total = valores.reduce((a, b) => a + b, 0);
                            const valor = tooltipItem.raw;
                            const porcentaje = total > 0 ? ((valor / total) * 100).toFixed(1) : 0;
                            return `${tooltipItem.label}: ${valor} (${porcentaje}%)`;
                        }}}
                    }
                }
            });
        }

        // Gráfica de Barras
        const ctxBar = document.getElementById('barrasChart')?.getContext('2d');
        if (ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: estados,
                    datasets: [{
                        label: 'Cantidad de registros',
                        data: valores,
                        backgroundColor: colores,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                    plugins: { legend: { display: false } }
                }
            });
        }
    </script>

<?php elseif ($grupoId > 0 && empty($postulantes)): ?>
    <div class="alert alert-warning">No hay estudiantes inscritos en este grupo.</div>
<?php elseif ($grupoId == 0): ?>
    <div class="alert alert-info">Seleccione un grupo y rango de fechas para ver el reporte.</div>
<?php endif; ?>