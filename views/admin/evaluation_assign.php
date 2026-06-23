<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Evaluation - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <div class="container mt-4">
        <h1>Assign Evaluation: <?php echo htmlspecialchars($evaluation['title']); ?></h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="../../evaluations/<?php echo $evaluation['id']; ?>/assign">
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="searchNombre" class="form-label">Buscar por Nombre</label>
                            <input type="text" class="form-control" id="searchNombre" placeholder="Ingrese nombre...">
                        </div>
                        <div class="col-md-6">
                            <label for="filterGrupo" class="form-label">Filtrar por Grupo</label>
                            <select class="form-select" id="filterGrupo">
                                <option value="">-- Todos los grupos --</option>
                                <option value="Promotores">Promotores</option>
                                <option value="Administración Oficinas">Administración Oficinas</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                                <option value="Administración Operaciones">Administración Operaciones</option>
                                <option value="Operaciones">Operaciones</option>
                                <option value="Administración">Administración</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="selectAllBtn">Seleccionar Todo</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllBtn">Deseleccionar Todo</button>
                    </div>
                </div>
            </div>

            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                            </th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Grupo</th>
                            <th>CEF</th>
                        </tr>
                    </thead>
                    <tbody id="usersTable">
                        <?php
                        $assignedUserIds = isset($assignedUsers) ? array_column($assignedUsers, 'id') : [];
                        foreach ($users as $user):
                        ?>
                            <tr class="user-row" data-nombre="<?php echo htmlspecialchars(strtolower($user['nombre'] ?? '')); ?>" data-grupo="<?php echo htmlspecialchars($user['grupo'] ?? ''); ?>">
                                <td>
                                    <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="<?php echo $user['id']; ?>" <?php echo in_array($user['id'], $assignedUserIds) ? 'checked' : ''; ?>>
                                </td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['nombre'] ?? ''); ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($user['grupo'] ?? 'N/A'); ?></span></td>
                                <td><?php echo htmlspecialchars($user['cef'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Asignar Evaluación</button>
                <a href="evaluations/<?php echo $evaluation['id']; ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        const searchInput = document.getElementById('searchNombre');
        const filterSelect = document.getElementById('filterGrupo');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const usersTable = document.getElementById('usersTable');

        // Filtrar usuarios
        function filterUsers() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedGrupo = filterSelect.value;
            let visibleCount = 0;

            usersTable.querySelectorAll('.user-row').forEach(row => {
                const nombre = row.dataset.nombre;
                const grupo = row.dataset.grupo;
                
                const matchesSearch = nombre.includes(searchTerm);
                const matchesGrupo = selectedGrupo === '' || grupo === selectedGrupo;
                
                if (matchesSearch && matchesGrupo) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Seleccionar todos visible
        selectAllBtn.addEventListener('click', () => {
            usersTable.querySelectorAll('.user-row:not([style*="display: none"]) .user-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        // Deseleccionar todos visible
        deselectAllBtn.addEventListener('click', () => {
            usersTable.querySelectorAll('.user-row:not([style*="display: none"]) .user-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        // Checkbox "Seleccionar Todo" para filtrados
        selectAllCheckbox.addEventListener('change', () => {
            usersTable.querySelectorAll('.user-row:not([style*="display: none"]) .user-checkbox').forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        // Escuchadores de filtros
        searchInput.addEventListener('keyup', filterUsers);
        filterSelect.addEventListener('change', filterUsers);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>