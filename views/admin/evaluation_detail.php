<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Details - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../dashboard">Evaluation System - Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="users">Users</a>
                <a class="nav-link" href="evaluations">Evaluations</a>
                <a class="nav-link" href="logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><?php echo htmlspecialchars($evaluation['title']); ?></h1>
            <div>
                <a href="../evaluations/<?php echo $evaluation['id']; ?>/edit" class="btn btn-warning">Editar</a>
                <a href="../evaluations/<?php echo $evaluation['id']; ?>/assign" class="btn btn-success">Asignar Usuarios</a>
            </div>
        </div>
        <p><?php echo htmlspecialchars($evaluation['description']); ?></p>
        <p><strong>Estado:</strong> <?php echo ucfirst($evaluation['status']); ?></p>
        <p><strong>Periodo:</strong> <?php echo $evaluation['start_date']; ?> to <?php echo $evaluation['end_date']; ?></p>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-primary h-100">
                    <div class="card-body">
                        <h5 class="card-title">Usuarios inscritos</h5>
                        <p class="display-6 mb-0"><?php echo $totalAssigned; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">Completados</h5>
                        <p class="display-6 mb-0"><?php echo $completedCount; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning h-100">
                    <div class="card-body">
                        <h5 class="card-title">Pendientes</h5>
                        <p class="display-6 mb-0"><?php echo count($pendingUsers); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <h2>Preguntas</h2>
        <a href="#addQuestion" class="btn btn-primary mb-3" data-bs-toggle="collapse">Add Pregunta</a>
        <div id="addQuestion" class="collapse">
            <form method="POST" action="../evaluations/<?php echo $evaluation['id']; ?>/add-question" class="mb-3">
                <div class="mb-3">
                    <label for="question_text" class="form-label">Texto de la Pregunta</label>
                    <input type="text" class="form-control" id="question_text" name="question_text" required>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Tipo</label>
                    <select class="form-control" id="type" name="type" onchange="toggleOptions()">
                        <option value="multiple_choice">Multiple opción</option>
                        <option value="scale">Scale (1-5)</option>
                        <option value="text">Text</option>
                    </select>
                </div>
                <div id="optionsDiv" class="mb-3">
                    <label class="form-label">Options (one per line)</label>
                    <textarea class="form-control" id="options" name="options" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label">Información adicional</label>
                    <textarea class="form-control" id="note" name="note" rows="2" placeholder="Ingresa cualquier detalle o información adicional sobre la pregunta..."></textarea>
                </div>
                <button type="submit" class="btn btn-success">Add Pregunta</button>
            </form>
        </div>

        <div class="list-group">
            <?php foreach ($questions as $question): ?>
                <div class="list-group-item">
                    <h5><?php echo htmlspecialchars($question['question_text']); ?></h5>
                    <p><em><?php echo ucfirst(str_replace('_', ' ', $question['type'])); ?></em></p>
                    <?php if ($question['type'] === 'multiple_choice' && $question['options']): ?>
                        <ul>
                            <?php foreach (json_decode($question['options']) as $option): ?>
                                <li><?php echo htmlspecialchars($option); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if (!empty($question['note'])): ?>
                        <div class="alert alert-info mt-2 mb-0">
                            <strong>Nota:</strong> <?php echo htmlspecialchars($question['note']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="mt-4">Usuarios pendientes</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingUsers)): ?>
                        <tr>
                            <td colspan="3" class="text-center">No hay usuarios pendientes. Todos los inscritos han completado la evaluación.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendingUsers as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleOptions() {
            const type = document.getElementById('type').value;
            const optionsDiv = document.getElementById('optionsDiv');
            if (type === 'multiple_choice') {
                optionsDiv.style.display = 'block';
            } else {
                optionsDiv.style.display = 'none';
            }
        }
        toggleOptions();
    </script>
</body>
</html>