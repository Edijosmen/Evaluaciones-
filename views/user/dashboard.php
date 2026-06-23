<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(90deg, #0d6efd22, #6f42c122);
            border-radius: .5rem;
            padding: 1.25rem;
        }
        .card-title { min-height: 3em; }
        .search-input { max-width: 420px; }
        .status-badge.open { background-color: #198754; }
        .status-badge.closed { background-color: #6c757d; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard">Evaluation System</a>
            <div class="d-flex align-items-center ms-auto">
                <div class="me-3 text-white">Hola, <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></div>
                <a class="btn btn-outline-light btn-sm" href="logout">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h1 class="h3 mb-1">Bienvenido, <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></h1>
                <p class="text-muted mb-0">Aquí puedes ver tus evaluaciones activas y próximas fechas.</p>
            </div>

        </div>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="hero mb-4">
            <?php $total = is_array($evaluations) ? count($evaluations) : 0; ?>
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start">
                <div>
                    <h4 class="mb-0">Evaluaciones Activas</h4>
                    <small class="text-muted">Tienes <strong><?php echo $total; ?></strong> evaluaciones asignadas</small>
                </div>
                <div class="mt-3 mt-sm-0">
                    <input id="search" class="form-control search-input" placeholder="Buscar evaluaciones..." />
                </div>
            </div>
        </div>

        <h2 class="visually-hidden">Lista de evaluaciones</h2>
        <?php require_once __DIR__ . '/../../app/Models/Response.php'; $responseModel = new Response(); ?>
        <div class="row" id="cards">
            <?php if (empty($evaluations)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No tienes Evaluaciones Activas.</div>
                </div>
            <?php else: ?>
                <?php foreach ($evaluations as $eval): ?>
                    <?php
                        $end = DateTime::createFromFormat('Y-m-d', $eval['end_date']);
                        $now = new DateTime();
                        $isOpen = $end && $end >= $now;
                        $isCompleted = isset($eval['assignment_id']) ? $responseModel->hasAnyResponse($eval['assignment_id']) : false;
                    ?>
                    <div class="col-md-4 mb-3 card-item">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($eval['title']); ?></h5>
                                    <span class="badge text-white status-badge <?php echo $isOpen ? 'open' : 'closed'; ?>">
                                        <?php echo $isOpen ? 'Abierta' : 'Cerrada'; ?>
                                    </span>
                                </div>
                                <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars(substr($eval['description'], 0, 140)); ?><?php echo strlen($eval['description'])>140? '...':''; ?></p>
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Finaliza: <?php echo htmlspecialchars($eval['end_date']); ?></small>
                                    <?php if ($isCompleted): ?>
                                        <span class="btn btn-sm btn-success disabled" aria-disabled="true">Realizada</span>
                                    <?php else: ?>
                                        <a href="evaluations/<?php echo $eval['id']; ?>" class="btn btn-sm btn-primary">Abrir</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side search for cards
        document.getElementById('search').addEventListener('input', function(e){
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('.card-item').forEach(function(col){
                const title = col.querySelector('.card-title').textContent.toLowerCase();
                const desc = col.querySelector('.card-text').textContent.toLowerCase();
                col.style.display = (title.indexOf(q) !== -1 || desc.indexOf(q) !== -1) ? '' : 'none';
            });
        });
    </script>
</body>
</html>