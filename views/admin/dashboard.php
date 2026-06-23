<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 700;
        }
        .card-header h5 {
            margin-bottom: 0;
        }
        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }
        .search-input-group .form-control {
            min-width: 240px;
        }
        .badge.bg-warning {
            color: #212529;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard">Evaluation System - Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="users">Users</a>
                <a class="nav-link" href="evaluations">Evaluations</a>
                <a class="nav-link" href="logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Panel de administración</h1>
                <p class="text-muted">Supervisa evaluaciones, usuarios y accesos desde un solo lugar.</p>
            </div>
            <div class="text-md-end">
                <a href="evaluations/create" class="btn btn-primary me-2">Nueva evaluación</a>
                <a href="users/create" class="btn btn-outline-secondary">Nuevo usuario</a>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div>
                        <h2 class="h5 mb-1">Evaluaciones recientes</h2>
                        <p class="text-muted mb-0">Accede rápidamente a las evaluaciones más recientes.</p>
                    </div>
                    <div class="input-group" style="max-width: 320px;">
                        <span class="input-group-text">Buscar</span>
                        <input id="dashboardSearch" type="search" class="form-control" placeholder="Buscar título..." />
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="evaluationsTable">
                <thead class="table-light">
                    <tr>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluations as $eval): ?>
                        <tr class="evaluation-row">
                            <td><?php echo htmlspecialchars($eval['title']); ?></td>
                            <td>
                                <?php
                                    $badgeClass = 'secondary';
                                    if ($eval['status'] === 'published') $badgeClass = 'success';
                                    if ($eval['status'] === 'closed') $badgeClass = 'dark';
                                    if ($eval['status'] === 'draft') $badgeClass = 'warning';
                                ?>
                                <span class="badge bg-<?php echo $badgeClass; ?> text-capitalize"><?php echo htmlspecialchars($eval['status']); ?></span>
                            </td>
                            <td><?php echo $eval['start_date']; ?></td>
                            <td><?php echo $eval['end_date']; ?></td>
                            <td class="text-end">
                                <a href="evaluations/<?php echo $eval['id']; ?>" class="btn btn-sm btn-outline-info me-1">Ver</a>
                                <a href="evaluations/<?php echo $eval['id']; ?>/edit" class="btn btn-sm btn-outline-warning me-1">Editar</a>
                                <a href="evaluations/<?php echo $eval['id']; ?>/assign" class="btn btn-sm btn-outline-success me-1">Asignar</a>
                                <a href="evaluations/<?php echo $eval['id']; ?>/download" class="btn btn-sm btn-outline-secondary">Descargar CSV</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bulk Upload Modal -->

    <!-- Bulk Upload Modal -->
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="users/bulk-upload" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="csvFile" class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                            <div class="form-text">
                                The CSV file should have columns: Username, Email, Password, Role (optional, defaults to 'user'). First row should be headers. <a href="sample_users.csv" target="_blank">Download sample CSV</a>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="defaultRole" class="form-label">Default Role (if not specified in CSV)</label>
                            <select class="form-control" id="defaultRole" name="default_role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload & Create Users</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('dashboardSearch')?.addEventListener('input', function(event) {
            const query = event.target.value.toLowerCase();
            document.querySelectorAll('.evaluation-row').forEach(function(row) {
                const title = row.querySelector('td:first-child').textContent.toLowerCase();
                row.style.display = title.includes(query) ? '' : 'none';
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>