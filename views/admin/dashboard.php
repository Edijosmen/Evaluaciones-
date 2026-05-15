<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Evaluation System</title>
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
        <h1>Admin Dashboard</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Users</h5>
                    </div>
                    <div class="card-body">
                        <p>Manage system users.</p>
                        <a href="users" class="btn btn-primary">View Users</a>
                        <a href="users/create" class="btn btn-secondary">Create User</a>
                        <button type="button" class="btn btn-info mt-2" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">Bulk Upload Users</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Evaluations</h5>
                    </div>
                    <div class="card-body">
                        <p>Manage evaluations and questions.</p>
                        <a href="evaluations" class="btn btn-primary">View Evaluations</a>
                        <a href="evaluations/create" class="btn btn-secondary">Create Evaluation</a>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-4">Recent Evaluations</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
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
                            <td><?php echo ucfirst($eval['status']); ?></td>
                            <td><?php echo $eval['start_date']; ?></td>
                            <td><?php echo $eval['end_date']; ?></td>
                            <td>
                                <a href="evaluations/<?php echo $eval['id']; ?>" class="btn btn-sm btn-info">View</a>
                                <a href="evaluations/<?php echo $eval['id']; ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                <a href="evaluations/<?php echo $eval['id']; ?>/assign" class="btn btn-sm btn-success">Assign</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>