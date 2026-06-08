<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= e($titulo ?? 'SIGIE') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

    <style>
        body {
            min-height: 100vh;
            background: #f4f6f9;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #1f2937;
            color: #ffffff;
        }

        .sidebar .brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar a {
            display: block;
            color: #d1d5db;
            text-decoration: none;
            padding: 12px 20px;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #374151;
            color: #ffffff;
        }

        .sidebar .user-box {
            padding: 15px 20px;
            font-size: 14px;
            color: #d1d5db;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .main-content {
            width: 100%;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            background: linear-gradient(135deg, #1f2937, #4b5563);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        .card-stat {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        .table thead th {
            white-space: nowrap;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .layout-wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['usuario'])): ?>
    <div class="d-flex layout-wrapper">
        <?php include BASE_PATH . '/resources/views/layouts/sidebar.blade.php'; ?>

        <main class="main-content p-4">
            <?php if ($mensaje = flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($mensaje = flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= e($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $contenido ?? '' ?>
        </main>
    </div>
<?php else: ?>
    <div class="login-wrapper">
        <div class="w-100">
            <?php if ($mensaje = flash('success')): ?>
                <div class="alert alert-success mx-auto mb-3" style="max-width: 420px;">
                    <?= e($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if ($mensaje = flash('error')): ?>
                <div class="alert alert-danger mx-auto mb-3" style="max-width: 420px;">
                    <?= e($mensaje) ?>
                </div>
            <?php endif; ?>

            <?= $contenido ?? '' ?>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>