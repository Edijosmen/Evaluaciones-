<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Evaluations - Evaluation System</title>
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
        <h1>My Evaluations</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="list-group">
            <?php if (empty($evaluations)): ?>
                <div class="alert alert-info">No evaluations assigned to you.</div>
            <?php else: ?>
                <?php foreach ($evaluations as $eval): ?>
                    <a href="evaluations/<?php echo $eval['id']; ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo htmlspecialchars($eval['title']); ?></h5>
                            <small><?php echo $eval['end_date']; ?></small>
                        </div>
                        <p class="mb-1"><?php echo htmlspecialchars($eval['description']); ?></p>
                        <small>Status: <?php echo ucfirst($eval['status']); ?></small>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>