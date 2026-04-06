<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../db_connect.php';

if (!isset($_GET['event_id'])) {
    header("Location: manage_gallery.php");
    exit();
}

$event_id = $_GET['event_id'];

// Fetch Event Details
$stmt = $conn->prepare("SELECT id, title, event_date, category FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stmt->bind_result($evt_id, $evt_title, $evt_date, $evt_category);
$event = null;
if ($stmt->fetch()) {
    $event = [
        'id' => $evt_id,
        'title' => $evt_title,
        'event_date' => $evt_date,
        'category' => $evt_category
    ];
}
$stmt->close();

if (!$event) {
    die("Event not found.");
}

// Handle Multiple Image Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_photos"])) {
    $target_dir = "../assets/uploads/";

    // Count total files
    $countfiles = count($_FILES['images']['name']);
    $success_count = 0;

    for ($i = 0; $i < $countfiles; $i++) {
        $filename = $_FILES['images']['name'][$i];

        if ($filename != "") {
            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = uniqid() . "_evt_" . $i . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            $db_path = "assets/uploads/" . $new_filename;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO event_images (event_id, image_path) VALUES (?, ?)");
                $stmt->bind_param("is", $event_id, $db_path);
                $stmt->execute();
                $stmt->close();
                $success_count++;
            }
        }
    }
    $message = "$success_count images uploaded successfully!";
}

// Handle Image Deletion
if (isset($_GET['delete_img'])) {
    $img_id = $_GET['delete_img'];
    $stmt = $conn->prepare("SELECT image_path FROM event_images WHERE id = ?");
    $stmt->bind_param("i", $img_id);
    $stmt->execute();
    $stmt->bind_result($image_path);

    if ($stmt->fetch()) {
        $stmt->close(); // Close before executing delete

        $file_path = "../" . $image_path;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $del_stmt = $conn->prepare("DELETE FROM event_images WHERE id = ?");
        $del_stmt->bind_param("i", $img_id);
        $del_stmt->execute();
        $del_stmt->close();

        header("Location: manage_event_images.php?event_id=" . $event_id);
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
    <title>Manage Event Photos</title>
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

        /* Headings */
        h3,
        h4 {
            color: var(--primary-color);
            font-weight: 700;
        }

        .text-primary {
            color: #2980b9 !important;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .card-header {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 600;
            border-bottom: 3px solid var(--accent-color);
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background-color: #f8fafc;
            transition: all 0.3s ease;
            position: relative;
        }

        .upload-area:hover {
            border-color: var(--accent-color);
            background-color: #e6fffa;
        }

        .upload-icon {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }

        /* Custom File Input */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .form-control[type="file"] {
            padding: 10px;
        }

        /* Image Grid */
        .card-img-top {
            transition: transform 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-danger {
            background-color: #e74c3c;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .badge-category {
            background-color: var(--accent-color);
            color: var(--primary-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php"><i class="fas fa-layer-group me-2"></i>SSB Admin</a>
            <div>
                <a href="manage_gallery.php" class="btn btn-outline-light btn-sm me-2">Back to Albums</a>
                <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Event Header Info -->
        <div class="card mb-4 shadow-sm border-start border-4 border-primary">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3 class="mb-1">Managing Photos for: <span style="color: var(--secondary-color);"><?php echo htmlspecialchars($event['title']); ?></span></h3>
                    <div class="text-muted">
                        <i class="far fa-calendar-alt me-2"></i><?php echo $event['event_date']; ?>
                        <span class="mx-2">|</span>
                        <span class="badge badge-category"><?php echo ucfirst($event['category']); ?></span>
                    </div>
                </div>
                <a href="manage_gallery.php" class="btn btn-outline-secondary btn-sm mt-3 mt-md-0"><i class="fas fa-arrow-left me-1"></i> Back</a>
            </div>
        </div>

        <?php if (isset($message)) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <i class='fas fa-check-circle me-2'></i>$message
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                  </div>";
        } ?>

        <!-- Upload Form -->
        <div class="card shadow-sm mb-5">
            <div class="card-header">
                <i class="fas fa-cloud-upload-alt me-2"></i>Add New Photos
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="upload-area">
                        <div class="upload-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h5>Click here or drag images to upload</h5>
                        <p class="text-muted small">Supports JPG, PNG, JPEG</p>

                        <input type="file" id="fileInput" name="images[]" class="form-control mt-3 mx-auto" style="max-width: 400px;" multiple required>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit" name="upload_photos" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-upload me-2"></i>Start Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Image Grid -->
        <h4 class="mb-3 border-bottom pb-2">Gallery Images</h4>
        <div class="row g-3">
            <?php
            $result = $conn->query("SELECT * FROM event_images WHERE event_id = $event_id ORDER BY id DESC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="card h-100 shadow-sm position-relative overflow-hidden">
                            <div style="height: 200px; overflow: hidden;">
                                <img src="../<?php echo $row['image_path']; ?>" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="Event Image">
                            </div>
                            <div class="card-body text-center p-2">
                                <a href="?event_id=<?php echo $event_id; ?>&delete_img=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm w-100" onclick="return confirm('Delete this photo?')">
                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='col-12'><p class='text-muted text-center py-5'>No photos uploaded for this event yet.</p></div>";
            }
            ?>
        </div>
    </div>

    <br><br>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadArea = document.querySelector('.upload-area');
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.createElement('div');
        previewContainer.className = 'row g-2 mt-3';
        uploadArea.appendChild(previewContainer);

        // Handle click on area to trigger input
        uploadArea.addEventListener('click', function(e) {
            // If the user clicked the actual input or a child of it, don't trigger click again
            if (e.target !== fileInput) {
                fileInput.click();
            }
        });

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            uploadArea.style.borderColor = 'var(--accent-color)';
            uploadArea.style.backgroundColor = '#e6fffa';
        }

        function unhighlight(e) {
            uploadArea.style.borderColor = '#cbd5e0';
            uploadArea.style.backgroundColor = '#f8fafc';
        }

        // Handle dropped files
        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files; // Update input files
            handleFiles(files);
        }

        // Handle selected files
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            previewContainer.innerHTML = ''; // Clear previous previews
            ([...files]).forEach(previewFile);
        }

        function previewFile(file) {
            if (!file.type.startsWith('image/')) return; // Skip non-images

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';

                const img = document.createElement('img');
                img.src = reader.result;
                img.className = 'img-thumbnail w-100';
                img.style.height = '100px';
                img.style.objectFit = 'cover';

                col.appendChild(img);
                previewContainer.appendChild(col);
            }
        }
    </script>
</body>

</html>
