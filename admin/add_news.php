<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../db_connect.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_news"])) {
    $title_en = trim($_POST['title_en']);
    $title_sin = trim($_POST['title_sin']);
    $title_tam = trim($_POST['title_tam']);
    $content_en = trim($_POST['content_en']);
    $content_sin = trim($_POST['content_sin']);
    $content_tam = trim($_POST['content_tam']);
    $date = $_POST['date'];
    $status = $_POST['status'];

    // Validate required fields
    if (empty($title_en) || empty($content_en) || empty($date)) {
        $error = "English title, content, and date are required!";
    } else {
        // Insert news article
        $stmt = $conn->prepare("INSERT INTO news (title_en, title_sin, title_tam, content_en, content_sin, content_tam, date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $title_en, $title_sin, $title_tam, $content_en, $content_sin, $content_tam, $date, $status);

        if ($stmt->execute()) {
            $news_id = $stmt->insert_id;
            $stmt->close();

            // Handle image uploads
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $upload_dir = "../uploads/news/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $caption_en = $_POST['caption_en'] ?? [];
                $caption_sin = $_POST['caption_sin'] ?? [];
                $caption_tam = $_POST['caption_tam'] ?? [];

                $total_files = count($_FILES['images']['name']);
                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['images']['error'][$i] == 0) {
                        $file_extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                        $new_filename = uniqid() . "_news_" . $news_id . "." . $file_extension;
                        $target_file = $upload_dir . $new_filename;
                        $db_path = "uploads/news/" . $new_filename;

                        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file)) {
                            // Insert image record
                            $cap_en = $caption_en[$i] ?? '';
                            $cap_sin = $caption_sin[$i] ?? '';
                            $cap_tam = $caption_tam[$i] ?? '';
                            $display_order = $i + 1;

                            $img_stmt = $conn->prepare("INSERT INTO news_images (news_id, image_path, caption_en, caption_sin, caption_tam, display_order) VALUES (?, ?, ?, ?, ?, ?)");
                            $img_stmt->bind_param("issssi", $news_id, $db_path, $cap_en, $cap_sin, $cap_tam, $display_order);
                            $img_stmt->execute();
                            $img_stmt->close();
                        }
                    }
                }
            }

            header("Location: manage_news.php?msg=added");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add News - Admin Panel</title>
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
            border: 1px solid #ced4da;
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
            transition: all 0.3s ease;
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
            border-color: var(--primary-color);
        }

        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .image-preview {
            position: relative;
            width: 150px;
            height: 150px;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
        <h2 class="text-center mb-4" style="color: var(--primary-color);">Add New News Article</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                    <ul class="nav nav-tabs mb-3" id="languageTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="english-tab" data-bs-toggle="tab" data-bs-target="#english" type="button">
                                <i class="fas fa-flag me-1"></i>English
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sinhala-tab" data-bs-toggle="tab" data-bs-target="#sinhala" type="button">
                                <i class="fas fa-flag me-1"></i>Sinhala
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tamil-tab" data-bs-toggle="tab" data-bs-target="#tamil" type="button">
                                <i class="fas fa-flag me-1"></i>Tamil
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="languageTabContent">
                        <!-- English -->
                        <div class="tab-pane fade show active" id="english" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label required-field">Title (English)</label>
                                <input type="text" name="title_en" class="form-control" required placeholder="Enter news title in English">
                            </div>
                            <div class="mb-3">
                                <label class="form-label required-field">Content (English)</label>
                                <textarea name="content_en" class="form-control" rows="8" required placeholder="Enter news content in English"></textarea>
                            </div>
                        </div>

                        <!-- Sinhala -->
                        <div class="tab-pane fade" id="sinhala" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Title (Sinhala)</label>
                                <input type="text" name="title_sin" class="form-control" placeholder="සිංහල මාතෘකාව ඇතුළත් කරන්න">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content (Sinhala)</label>
                                <textarea name="content_sin" class="form-control" rows="8" placeholder="සිංහල අන්තර්ගතය ඇතුළත් කරන්න"></textarea>
                            </div>
                        </div>

                        <!-- Tamil -->
                        <div class="tab-pane fade" id="tamil" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label">Title (Tamil)</label>
                                <input type="text" name="title_tam" class="form-control" placeholder="தமிழ் தலைப்பை உள்ளிடவும்">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content (Tamil)</label>
                                <textarea name="content_tam" class="form-control" rows="8" placeholder="தமிழ் உள்ளடக்கத்தை உள்ளிடவும்"></textarea>
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
                            <input type="date" name="date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-images me-2"></i>Images
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Upload Images (Multiple)</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*" id="imageInput">
                        <small class="text-muted">You can select multiple images at once</small>
                    </div>
                    <div id="imagePreviewContainer" class="image-preview-container"></div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mb-4">
                <button type="submit" name="add_news" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Add News Article
                </button>
                <a href="manage_news.php" class="btn btn-secondary btn-lg ms-2">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = '';

            const files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'image-preview';
                        div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                        container.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
</body>

</html>
