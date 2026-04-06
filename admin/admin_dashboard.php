<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Social Security Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .dashboard-card {
            transition: transform 0.3s;
            cursor: pointer;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .icon-wrapper {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #0a192f;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark mb-5">
        <div class="container">
            <span class="navbar-brand mb-0 h1">SSB Admin Portal</span>
            <a href="../index.html" class="btn btn-outline-light btn-sm me-2">Visit Website</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center mb-5">Welcome, Admin</h2>

        <div class="row g-4 justify-content-center">
            <!-- Manage Gallery -->
            <div class="col-md-4">
                <div class="card p-4 text-center dashboard-card" onclick="window.location.href='manage_gallery.php'">
                    <div class="card-body">
                        <div class="icon-wrapper">
                            <i class="fas fa-images"></i>
                        </div>
                        <h4 class="card-title">Manage Gallery</h4>
                        <p class="card-text text-muted">Upload photos, organize categories (Events, Meetings, Awards).</p>
                        <a href="manage_gallery.php" class="btn btn-primary mt-2">Go to Gallery</a>
                    </div>
                </div>
            </div>

            <!-- Manage News -->
            <div class="col-md-4">
                <div class="card p-4 text-center dashboard-card" onclick="window.location.href='manage_news.php'">
                    <div class="card-body">
                        <div class="icon-wrapper">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h4 class="card-title">Manage News</h4>
                        <p class="card-text text-muted">Post news updates, press releases, and announcements.</p>
                        <a href="manage_news.php" class="btn btn-primary mt-2">Go to News</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
