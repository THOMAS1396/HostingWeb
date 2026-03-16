<?php
// admin/edit_course.php
// Form to edit an existing course

session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get the course ID from URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
$msg = "";

// Handle form submission (update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title       = trim($_POST['title']);
    $category    = trim($_POST['category']);
    $description = trim($_POST['description']);
    $image_url   = trim($_POST['image_url']);
    $video_url   = trim($_POST['video_url']);

    if (empty($title) || empty($category)) {
        $msg = "error:Title and Category are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE courses SET title=?, category=?, description=?, image_url=?, video_url=? WHERE id=?");
        $stmt->execute([$title, $category, $description, $image_url, $video_url, $id]);
        header("Location: dashboard.php?msg=updated");
        exit();
    }
}

// Fetch current course data
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - ClassPractice Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f4f4; }

        .navbar {
            background: linear-gradient(135deg, #1a1a1a, #0f0f0f);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h2 { font-size: 22px; }
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s;
        }
        .nav-links a:hover { background: rgba(255,255,255,0.1); }
        .nav-links a.logout { background: #c0622a; }
        .nav-links a.logout:hover { background: #a0501f; }

        .container { max-width: 700px; margin: 2.5rem auto; padding: 0 2rem; }

        .breadcrumb { font-size: 13px; color: #999; margin-bottom: 1rem; }
        .breadcrumb a { color: #c0622a; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .form-box {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        }
        .form-box h2 { margin-bottom: 1.5rem; color: #222; font-size: 20px; }

        .form-group { margin-bottom: 1.2rem; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #444;
        }
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            transition: border 0.2s;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2196f3;
        }
        .form-group textarea { height: 110px; resize: vertical; }

        .required { color: #f44336; }

        .preview-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 8px;
            border: 1px solid #eee;
        }

        .btn-row { display: flex; gap: 1rem; margin-top: 1.5rem; }

        .submit-btn {
            background: #2196f3;
            color: white;
            border: none;
            padding: 0.75rem 1.8rem;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .submit-btn:hover { background: #1976d2; }

        .cancel-btn {
            background: #eee;
            color: #444;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .cancel-btn:hover { background: #ddd; }

        .message {
            padding: 0.8rem 1rem;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 1rem;
        }
        .message.error   { background: #fdecea; color: #c62828; border: 1px solid #f5c6c6; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>💡 ClassPractice Admin</h2>
    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="../index.php">View Site</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a> &rsaquo; Edit Course
    </div>

    <div class="form-box">
        <h2>✏️ Edit Course</h2>

        <?php if($msg): ?>
            <div class="message error">
                <?php echo htmlspecialchars(str_replace('error:', '', $msg)); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit_course.php?id=<?php echo $id; ?>">

            <div class="form-group">
                <label>Course Title <span class="required">*</span></label>
                <input type="text" name="title"
                    value="<?php echo htmlspecialchars($_POST['title'] ?? $course['title']); ?>">
            </div>

            <div class="form-group">
                <label>Category <span class="required">*</span></label>
                <select name="category">
                    <?php
                    $cats = ['Programming','Web Development','Data Science','Design','Database','Networking','Other'];
                    foreach($cats as $cat):
                        $current = $_POST['category'] ?? $course['category'];
                        $sel = ($current == $cat) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $cat; ?>" <?php echo $sel; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($_POST['description'] ?? $course['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Image URL</label>
                <input type="text" name="image_url" id="imageUrl"
                    value="<?php echo htmlspecialchars($_POST['image_url'] ?? $course['image_url']); ?>"
                    oninput="previewImage(this.value)">
                <img id="imgPreview" class="preview-img"
                    src="<?php echo htmlspecialchars($course['image_url']); ?>"
                    alt="Course image"
                    onerror="this.style.display='none'">
            </div>

            <div class="form-group">
                <label>Video URL (YouTube link)</label>
                <input type="text" name="video_url"
                    value="<?php echo htmlspecialchars($_POST['video_url'] ?? $course['video_url']); ?>">
            </div>

            <div class="btn-row">
                <button type="submit" class="submit-btn">💾 Save Changes</button>
                <a href="dashboard.php" class="cancel-btn">Cancel</a>
            </div>

        </form>
    </div>
</div>

<script>
function previewImage(url) {
    const img = document.getElementById('imgPreview');
    if (url.trim() !== '') {
        img.src = url;
        img.style.display = 'block';
        img.onerror = () => img.style.display = 'none';
    } else {
        img.style.display = 'none';
    }
}
</script>

</body>
</html>
