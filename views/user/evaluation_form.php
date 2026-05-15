<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($evaluation['title']); ?> - Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard">Evaluation System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
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
                    <div class="card mb-3">
                    <div class="card-body">
                        <h5><?php echo htmlspecialchars($question['question_text']); ?></h5>
                        <?php if ($question['type'] === 'multiple_choice'): ?>
                            <?php $options = json_decode($question['options']); ?>
                            <?php foreach ($options as $option): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="question_<?php echo $question['id']; ?>" value="<?php echo htmlspecialchars($option); ?>" id="q<?php echo $question['id']; ?>_<?php echo md5($option); ?>">
                                    <label class="form-check-label" for="q<?php echo $question['id']; ?>_<?php echo md5($option); ?>">
                                        <?php echo htmlspecialchars($option); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($question['type'] === 'scale'): ?>
                            <div class="mb-3">
                                <label class="form-label">Rate from 1 to 5 (1 = Poor, 5 = Excellent)</label>
                                <select class="form-control" name="question_<?php echo $question['id']; ?>">
                                    <option value="">Select rating</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        <?php elseif ($question['type'] === 'text'): ?>
                            <div class="mb-3">
                                <textarea class="form-control" name="question_<?php echo $question['id']; ?>" rows="3" placeholder="Your answer..."></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">Submit Evaluation</button>
            <a href="evaluations" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>