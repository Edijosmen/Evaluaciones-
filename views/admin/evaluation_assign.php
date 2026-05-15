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
            <div class="mb-3">
                <label class="form-label">Select Users to Assign</label>
                <div class="row">
                    <?php
                    $assignedUserIds = isset($assignedUsers) ? array_column($assignedUsers, 'id') : [];
                    foreach ($users as $user):
                    ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="user_<?php echo $user['id']; ?>" name="user_ids[]" value="<?php echo $user['id']; ?>" <?php echo in_array($user['id'], $assignedUsers) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="user_<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Assign Evaluation</button>
            <a href="evaluations/<?php echo $evaluation['id']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>