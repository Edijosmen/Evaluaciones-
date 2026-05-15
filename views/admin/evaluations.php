<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluations - Admin</title>
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
        <div class="d-flex justify-content-between align-items-center">
            <h1>Evaluations</h1>
            <a href="evaluations/create" class="btn btn-primary">Create Evaluation</a>
        </div>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluations as $eval): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($eval['title']); ?></td>
                            <td><?php echo htmlspecialchars(substr($eval['description'], 0, 50)); ?>...</td>
                            <td><?php echo ucfirst($eval['status']); ?></td>
                            <td><?php echo $eval['start_date']; ?></td>
                            <td><?php echo $eval['end_date']; ?></td>
                            <td>
                                <a href="evaluations/<?php echo $eval['id']; ?>" class="btn btn-sm btn-info">View</a>
                                <a href="evaluations/<?php echo $eval['id']; ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                <a href="evaluations/<?php echo $eval['id']; ?>/assign" class="btn btn-sm btn-success">Assign</a>
                                <a href="evaluations/<?php echo $eval['id']; ?>/delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>