<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../db_connect.php';

$error = '';
$news = null;
$images = [];


/* ================= GET NEWS ID ================= */

if (!isset($_GET['id'])) {
    header("Location: manage_news.php");
    exit();
}

$news_id = (int)$_GET['id'];


/* ================= FETCH NEWS (NO get_result) ================= */

$stmt = $conn->prepare("
    SELECT id, title_en, title_sin, title_tam,
           content_en, content_sin, content_tam,
           date, status
    FROM news
    WHERE id = ?
");

$stmt->bind_param("i", $news_id);
$stmt->execute();

$stmt->bind_result(
    $id,
    $title_en,
    $title_sin,
    $title_tam,
    $content_en,
    $content_sin,
    $content_tam,
    $date,
    $status
);

if ($stmt->fetch()) {

    $news = [
        'id' => $id,
        'title_en' => $title_en,
        'title_sin' => $title_sin,
        'title_tam' => $title_tam,
        'content_en' => $content_en,
        'content_sin' => $content_sin,
        'content_tam' => $content_tam,
        'date' => $date,
        'status' => $status
    ];
}

$stmt->close();

if (!$news) {
    header("Location: manage_news.php");
    exit();
}



/* ================= FETCH IMAGES (NO get_result) ================= */

$stmt = $conn->prepare("
    SELECT id, image_path
    FROM news_images
    WHERE news_id = ?
    ORDER BY display_order
");

$stmt->bind_param("i", $news_id);
$stmt->execute();
$stmt->bind_result($img_id, $img_path);

while ($stmt->fetch()) {
    $images[] = [
        'id' => $img_id,
        'image_path' => $img_path
    ];
}

$stmt->close();



/* ================= DELETE IMAGE (POST ONLY) ================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {

    $image_id = (int)$_POST['delete_image'];

    // Get path
    $stmt = $conn->prepare("SELECT image_path FROM news_images WHERE id=?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $stmt->bind_result($image_path);

    if ($stmt->fetch()) {

        $file = "../" . $image_path;

        if (!empty($image_path) && file_exists($file)) {
            unlink($file);
        }
    }

    $stmt->close();

    // Delete record
    $stmt = $conn->prepare("DELETE FROM news_images WHERE id=?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $stmt->close();

    header("Location: edit_news.php?id=$news_id");
    exit();
}



/* ================= UPDATE NEWS ================= */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_news"])) {

    $title_en = trim($_POST['title_en']);
    $title_sin = trim($_POST['title_sin']);
    $title_tam = trim($_POST['title_tam']);
    $content_en = trim($_POST['content_en']);
    $content_sin = trim($_POST['content_sin']);
    $content_tam = trim($_POST['content_tam']);
    $date = $_POST['date'];
    $status = $_POST['status'];

    if (empty($title_en) || empty($content_en) || empty($date)) {

        $error = "English title, content, and date are required!";
    } else {

        $stmt = $conn->prepare("
            UPDATE news 
            SET title_en=?, title_sin=?, title_tam=?,
                content_en=?, content_sin=?, content_tam=?,
                date=?, status=?
            WHERE id=?
        ");

        $stmt->bind_param(
            "ssssssssi",
            $title_en,
            $title_sin,
            $title_tam,
            $content_en,
            $content_sin,
            $content_tam,
            $date,
            $status,
            $news_id
        );

        if (!$stmt->execute()) {
            die("Update failed: " . $stmt->error);
        }

        $stmt->close();



        /* ================= IMAGE UPLOAD ================= */

        if (!empty($_FILES['images']['name'][0])) {

            $upload_dir = "../uploads/news/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Get max order
            $stmt = $conn->prepare("
                SELECT COALESCE(MAX(display_order),0)
                FROM news_images
                WHERE news_id=?
            ");

            $stmt->bind_param("i", $news_id);
            $stmt->execute();
            $stmt->bind_result($max_order);
            $stmt->fetch();
            $stmt->close();


            $total = count($_FILES['images']['name']);

            for ($i = 0; $i < $total; $i++) {

                if ($_FILES['images']['error'][$i] == 0) {

                    $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));

                    // allow images only
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        continue;
                    }

                    $new_name = uniqid("news_", true) . "." . $ext;

                    $target = $upload_dir . $new_name;
                    $db_path = "uploads/news/" . $new_name;

                    if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target)) {

                        $display_order = $max_order + $i + 1;

                        $img_stmt = $conn->prepare("
                            INSERT INTO news_images (news_id, image_path, display_order)
                            VALUES (?, ?, ?)
                        ");

                        $img_stmt->bind_param("isi", $news_id, $db_path, $display_order);
                        $img_stmt->execute();
                        $img_stmt->close();
                    }
                }
            }
        }

        header("Location: manage_news.php?msg=updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit News - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #0a192f;
            --secondary-color: #112240;
            --accent-color: #64ffda;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: var(--accent-color) !important;
            font-weight: 700;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
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
            padding: 10px 15px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(100, 255, 218, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .nav-tabs .nav-link {
            color: var(--primary-color);
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .existing-image {
            position: relative;
            display: inline-block;
            margin: 10px;
        }

        .existing-image img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        .delete-image-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
        }

        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php"><i class="fas fa-user-shield me-2"></i>SSB Admin</a>
            <div>
                <a href="manage_news.php" class="btn btn-outline-light btn-sm me-2">Back to News</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center mb-4" style="color: var(--primary-color);">Edit News Article</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Language Tabs -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-language me-2"></i>Multilingual Content
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="languageTabs">
                        <li class="nav-item">
                            <button class="nav-link active" id="english-tab" data-bs-toggle="tab" data-bs-target="#english" type="button">
                                <i class="fas fa-flag me-1"></i>English
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="sinhala-tab" data-bs-toggle="tab" data-bs-target="#sinhala" type="button">
                                <i class="fas fa-flag me-1"></i>Sinhala
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tamil-tab" data-bs-toggle="tab" data-bs-target="#tamil" type="button">
                                <i class="fas fa-flag me-1"></i>Tamil
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- English -->
                        <div class="tab-pane fade show active" id="english">
                            <div class="mb-3">
                                <label class="form-label required-field">Title (English)</label>
                                <input type="text" name="title_en" class="form-control" required value="<?php echo htmlspecialchars($news['title_en']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">Content (English)</label>
                                <textarea name="content_en" class="form-control" rows="8" required><?php echo htmlspecialchars($news['content_en']); ?></textarea>
                            </div>
                        </div>

                        <!-- Sinhala -->
                        <div class="tab-pane fade" id="sinhala">
                            <div class="mb-3">
                                <label class="form-label">Title (Sinhala)</label>
                                <input type="text" name="title_sin" class="form-control" value="<?php echo htmlspecialchars($news['title_sin']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content (Sinhala)</label>
                                <textarea name="content_sin" class="form-control" rows="8"><?php echo htmlspecialchars($news['content_sin']); ?></textarea>
                            </div>
                        </div>

                        <!-- Tamil -->
                        <div class="tab-pane fade" id="tamil">
                            <div class="mb-3">
                                <label class="form-label">Title (Tamil)</label>
                                <input type="text" name="title_tam" class="form-control" value="<?php echo htmlspecialchars($news['title_tam']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content (Tamil)</label>
                                <textarea name="content_tam" class="form-control" rows="8"><?php echo htmlspecialchars($news['content_tam']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date and Status -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar me-2"></i>Publication Details
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Publication Date</label>
                            <input type="date" name="date" class="form-control" required value="<?php echo $news['date']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="published" <?php echo $news['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="draft" <?php echo $news['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Images -->
            <?php if (!empty($images)): ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-images me-2"></i>Existing Images
                    </div>
                    <div class="card-body">
                        <?php foreach ($images as $img): ?>
                            <div class="existing-image">
                                <img src="../<?php echo $img['image_path']; ?>" alt="News Image">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this image?')">
                                    <input type="hidden" name="delete_image" value="<?php echo $img['id']; ?>">
                                    <button type="submit" class="delete-image-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Add New Images -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-2"></i>Add New Images
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Upload Additional Images</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">Select multiple images to add</small>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mb-4">
                <button type="submit" name="update_news" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Update News Article
                </button>
                <a href="manage_news.php" class="btn btn-secondary btn-lg ms-2">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>