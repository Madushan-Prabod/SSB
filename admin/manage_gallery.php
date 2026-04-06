<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../db_connect.php';

// Handle Event Creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_event"])) {
    $category = $_POST['category'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];

    // Cover Image Upload
    $target_dir = "../assets/uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = pathinfo($_FILES["cover_image"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . "_cover." . $file_extension;
    $target_file = $target_dir . $new_filename;
    $db_path = "assets/uploads/" . $new_filename;

    if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO events (category, title, description, event_date, cover_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $category, $title, $description, $event_date, $db_path);

        if ($stmt->execute()) {
            $message = "Event created successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Error uploading cover image.";
    }
}

// Handle Event Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT cover_image FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($cover_image);

    if ($stmt->fetch()) {
        $stmt->close(); // Close before executing delete

        $file_path = "../" . $cover_image;
        if (file_exists($file_path)) {
            unlink($file_path); // Delete cover image
        }

        $del_stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        $del_stmt->execute();
        $del_stmt->close();

        header("Location: manage_gallery.php");
        exit();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Albums - Admin Panel</title>
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

        /* Navbar Styling */
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
        }

        .btn-outline-light:hover {
            background-color: var(--accent-color);
            color: var(--primary-color);
        }

        /* Page Heading */
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

        /* Form Card */
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

        .form-label {
            font-weight: 500;
            color: var(--primary-color);
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(100, 255, 218, 0.25);
        }

        /* Buttons */
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

        /* Table Styling */
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

        .img-thumbnail {
            border-radius: 8px;
            padding: 0;
            object-fit: cover;
            width: 80px;
            height: 60px;
        }

        .btn-info {
            background-color: #3498db;
            border: none;
            color: white;
        }

        .btn-danger {
            background-color: #e74c3c;
            border: none;
        }

        .alert {
            border-radius: 10px;
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
            <h2>Manage Albums</h2>
        </div>

        <?php if (isset($message)) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <i class='fas fa-check-circle me-2'></i>$message
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                  </div>";
        } ?>

        <!-- Create Event Form -->
        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fas fa-plus-circle me-2"></i>Create New Event (Album)
            </div>
            <div class="card-body p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="events">Events</option>
                                <option value="meetings">Meetings</option>
                                <option value="awards">Awards</option>
                                <option value="projects">Projects</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Event Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="Ex: Award Ceremony 2025">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="event_date" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cover Image</label>
                            <input type="file" name="cover_image" class="form-control" required accept="image/*">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description (Main Text)</label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="Enter the main description for this event page..."></textarea>
                        </div>
                    </div>
                    <button type="submit" name="create_event" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Create Event
                    </button>
                </form>
            </div>
        </div>

        <!-- Existing Events List -->
        <div class="card shadow-sm mt-5">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>Existing Events
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Cover</th>
                                <th>Category</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Photos</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='ps-4'><img src='../" . $row['cover_image'] . "' class='img-thumbnail'></td>";
                                    echo "<td><span class='badge bg-secondary'>" . ucfirst($row['category']) . "</span></td>";
                                    echo "<td><strong>" . htmlspecialchars($row['title']) . "</strong></td>";
                                    echo "<td>" . $row['event_date'] . "</td>";
                                    echo "<td><a href='manage_event_images.php?event_id=" . $row['id'] . "' class='btn btn-info btn-sm text-white'><i class='fas fa-images me-1'></i>Manage Photos</a></td>";
                                    echo "<td><a href='?delete=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure? This will delete the event and all its photos.\")'><i class='fas fa-trash'></i></a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No events found. Create one above!</td></tr>";
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
