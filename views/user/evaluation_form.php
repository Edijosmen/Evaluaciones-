<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($evaluation['title']); ?> - Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .question-card {
            border: 1px solid #0d6efd;
            background: linear-gradient(180deg, rgba(13,110,253,0.05), #ffffff);
            box-shadow: 0 0 18px rgba(13,110,253,0.12);
        }
        .question-card .question-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0d6efd;
        }
        .question-card .question-note {
            border-left: 4px solid #198754;
            background: #e9f7ef;
            color: #0f5132;
            padding: 0.85rem 1rem;
            margin-top: 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.95rem;
        }
        .question-card .question-meta {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .question-card .form-check-input:checked + .form-check-label {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard">Sistema de Evaluación</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">Bienvenido, <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></span>
                <a class="nav-link" href="logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1><?php echo htmlspecialchars($evaluation['title']); ?></h1>
        <p><?php echo htmlspecialchars($evaluation['description']); ?></p>
        <p><strong>Period:</strong> <?php echo $evaluation['start_date']; ?> to <?php echo $evaluation['end_date']; ?></p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST" action="../evaluations/<?php echo $evaluation['id']; ?>">
            <?php if (empty($questions)): ?>
                <div class="alert alert-warning">This evaluation has no questions yet.</div>
            <?php else: ?>
                <?php foreach ($questions as $question): ?>
                <div class="card mb-4 shadow-sm question-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title question-title mb-1"><?php echo htmlspecialchars($question['question_text']); ?></h5>
                                <?php if (!empty($question['note'])): ?>
                                    <div class="question-note">
                                        <?php echo htmlspecialchars($question['note']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
            
                        </div>

                        <?php if ($question['type'] === 'multiple_choice'): ?>
                            <?php $options = json_decode($question['options']); ?>
                            <?php foreach ($options as $option): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="question_<?php echo $question['id']; ?>" value="<?php echo htmlspecialchars($option); ?>" id="q<?php echo $question['id']; ?>_<?php echo md5($option); ?>">
                                    <label class="form-check-label" for="q<?php echo $question['id']; ?>_<?php echo md5($option); ?>">
                                        <?php echo htmlspecialchars($option); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($question['type'] === 'scale'): ?>
                            <div class="mb-3">
                                <label class="form-label">Selecciona una calificación</label>
                                <select class="form-control" name="question_<?php echo $question['id']; ?>">
                                    <option value="">Selecciona</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <div class="form-text">1 = Muy bajo, 5 = Excelente</div>
                            </div>
                        <?php elseif ($question['type'] === 'text'): ?>
                            <div class="mb-3">
                                <textarea class="form-control" name="question_<?php echo $question['id']; ?>" rows="4" placeholder="Escribe tu respuesta aquí..."></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Enviar Evaluación</button>
            <a href="../dashboard"  class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>