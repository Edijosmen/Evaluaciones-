<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Evaluation System</title>
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
        <h1>My Dashboard</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <h2>Active Evaluations</h2>
        <div class="row">
            <?php if (empty($evaluations)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No active evaluations assigned to you.</div>
                </div>
            <?php else: ?>
                <?php foreach ($evaluations as $eval): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($eval['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr($eval['description'], 0, 100)); ?>...</p>
                                <p class="card-text"><small class="text-muted">Due: <?php echo $eval['end_date']; ?></small></p>
                                <a href="evaluations/<?php echo $eval['id']; ?>" class="btn btn-primary">Take Evaluation</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>