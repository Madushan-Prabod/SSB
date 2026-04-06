<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../db_connect.php';


/* ================= DELETE NEWS (SECURE POST) ================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {

    $id = (int)$_POST['delete'];

    if ($id > 0) {

        // Fetch images safely (NO get_result)
        $stmt = $conn->prepare("SELECT image_path FROM news_images WHERE news_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($image_path);

        while ($stmt->fetch()) {

            $file_path = "../" . $image_path;

            if (!empty($image_path) && file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $stmt->close();

        // Delete news
        $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            die("Delete failed: " . $stmt->error);
        }

        $stmt->close();

        header("Location: manage_news.php?msg=deleted");
        exit();
    }
}


/* ================= FETCH NEWS (FAST QUERY) ================= */

$sql = "
SELECT 
    n.*, 
    COUNT(ni.id) as image_count
FROM news n
LEFT JOIN news_images ni ON n.id = ni.news_id
GROUP BY n.id
ORDER BY n.date DESC, n.created_at DESC
";

$news_result = $conn->query($sql);

if (!$news_result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #0a192f;
            --secondary-color: #112240;
            --accent-color: #64ffda;
            --text-light: #e6f1ff;
            --text-muted: #8892b0;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: var(--accent-color) !important;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .btn-outline-light {
            border-color: var(--accent-color);
            color: var(--accent-color);
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background-color: var(--accent-color);
            color: var(--primary-color);
            border-color: var(--accent-color);
        }

        h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: var(--accent-color);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 1rem 1.5rem;
            font-weight: 600;
            border-bottom: 3px solid var(--accent-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table th {
            font-weight: 500;
            padding: 15px;
            border: none;
        }

        .table td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.875rem;
        }

        .alert {
            border-radius: 10px;
        }

        .news-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php"><i class="fas fa-user-shield me-2"></i>SSB Admin</a>
            <div>
                <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm me-2">Back to Dashboard</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="text-center mb-5">
            <h2>Manage News</h2>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'added'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>News article added successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'updated'): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fas fa-info-circle me-2"></i>News article updated successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>News article deleted successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="mb-4">
            <a href="add_news.php" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add New News Article
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>All News Articles
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Title (English)</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Images</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($news_result->num_rows > 0) {
                                while ($row = $news_result->fetch_assoc()) {

                                    $img_count = $row['image_count'];

                                    echo "<tr>";
                                    echo "<td class='ps-4'><strong>#" . $row['id'] . "</strong></td>";
                                    echo "<td><div class='news-preview'>" . htmlspecialchars($row['title_en']) . "</div></td>";
                                    echo "<td>" . date('M d, Y', strtotime($row['date'])) . "</td>";

                                    $status_class = $row['status'] == 'published' ? 'bg-success' : 'bg-secondary';
                                    echo "<td><span class='badge $status_class status-badge'>" . ucfirst($row['status']) . "</span></td>";

                                    echo "<td><span class='badge bg-info'>" . $img_count . " image(s)</span></td>";
                                    echo "<td>";
                                    echo "<a href='edit_news.php?id=" . $row['id'] . "' class='btn btn-sm btn-info text-white me-1'><i class='fas fa-edit'></i></a>";

                                    // SECURE DELETE BUTTON (same look)
                                    echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure? This will delete the news article and all its images.\")'>
                                            <input type='hidden' name='delete' value='" . $row['id'] . "'>
                                            <button class='btn btn-sm btn-danger'><i class='fas fa-trash'></i></button>
                                          </form>";

                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No news articles found. Add one above!</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <br><br>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
