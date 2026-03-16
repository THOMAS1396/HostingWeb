<?php
// admin/dashboard.php
// Admin dashboard to view, add, edit, and delete courses

session_start();
require_once '../db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle course deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = :id");
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=deleted");
        exit();
    }
}

// Fetch all courses with creator name
$query = "SELECT c.*, u.username as creator 
          FROM courses c 
          LEFT JOIN users u ON c.created_by = u.id 
          ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ClassPractice</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }

        body { background: #f4f4f4; }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #1a1a1a, #0f0f0f);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
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
        .nav-links a.logout {
            background: #c0622a;
        }
        .nav-links a.logout:hover { background: #a0501f; }

        /* Container */
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .header h1 { font-size: 24px; color: #222; }

        .add-btn {
            background: #c0622a;
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.3s;
        }
        .add-btn:hover { background: #a0501f; }

        /* Message */
        .message {
            padding: 0.9rem 1.2rem;
            border-radius: 8px;
            margin-bottom: 1.2rem;
            font-size: 14px;
            font-weight: 600;
        }
        .message.success { background: #4caf50; color: white; }
        .message.error   { background: #f44336; color: white; }

        /* Courses grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
        }

        .course-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .course-card:hover { transform: translateY(-3px); }

        .course-image {
            width: 100%;
            height: 190px;
            object-fit: cover;
        }

        .course-content { padding: 1.2rem; }

        .course-title { font-size: 1.1rem; margin-bottom: 0.4rem; color: #222; font-weight: 700; }

        .course-category {
            color: #c0622a;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 0.5rem;
        }

        .course-description {
            color: #666;
            font-size: 13px;
            margin-bottom: 0.8rem;
            line-height: 1.5;
        }

        .course-meta {
            font-size: 12px;
            color: #999;
            margin-bottom: 1rem;
        }

        .actions { display: flex; gap: 0.8rem; }

        .edit-btn, .delete-btn {
            text-decoration: none;
            padding: 0.45rem 1rem;
            border-radius: 5px;
            font-weight: 600;
            font-size: 13px;
            transition: background 0.3s;
        }
        .edit-btn { background: #2196f3; color: white; }
        .edit-btn:hover { background: #1976d2; }
        .delete-btn { background: #f44336; color: white; }
        .delete-btn:hover { background: #d32f2f; }

        /* Empty state */
        .no-courses {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 12px;
            color: #666;
        }
        .no-courses h2 { margin-bottom: 0.5rem; color: #333; }

        /* Stats bar */
        .stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-box {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
            flex: 1;
            text-align: center;
        }
        .stat-box .num { font-size: 28px; font-weight: 700; color: #c0622a; }
        .stat-box .label { font-size: 12px; color: #999; margin-top: 2px; }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h2>💡 ClassPractice Admin</h2>
    <div class="nav-links">
        <a href="applications.php">Applications</a>
        <a href="../index.php">View Site</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <!-- Header -->
    <div class="header">
        <h1>Course Management</h1>
        <a href="add_course.php" class="add-btn">+ Add New Course</a>
    </div>

    <!-- Stats -->
    <div class="stats">
        <div class="stat-box">
            <div class="num"><?php echo count($courses); ?></div>
            <div class="label">Total Courses</div>
        </div>
        <div class="stat-box">
            <div class="num">
                <?php
                    $cats = array_unique(array_column($courses, 'category'));
                    echo count($cats);
                ?>
            </div>
            <div class="label">Categories</div>
        </div>
        <div class="stat-box">
            <div class="num"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
            <div class="label">Logged in as</div>
        </div>
    </div>

    <!-- Flash Message -->
    <?php if(isset($_GET['msg'])): ?>
        <div class="message success">
            <?php
                if($_GET['msg'] == 'added')   echo "✅ Course added successfully!";
                if($_GET['msg'] == 'updated') echo "✅ Course updated successfully!";
                if($_GET['msg'] == 'deleted') echo "🗑️ Course deleted successfully!";
            ?>
        </div>
    <?php endif; ?>

    <!-- Courses Grid -->
    <?php if(count($courses) > 0): ?>
        <div class="courses-grid">
            <?php foreach($courses as $course): ?>
                <div class="course-card">
                    <img 
                        src="<?php echo htmlspecialchars($course['image_url'] ?: '../images/python.png'); ?>" 
                        alt="<?php echo htmlspecialchars($course['title']); ?>" 
                        class="course-image"
                        onerror="this.src='../images/python.png'"
                    >
                    <div class="course-content">
                        <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <div class="course-category"><?php echo htmlspecialchars($course['category']); ?></div>
                        <p class="course-description">
                            <?php echo substr(htmlspecialchars($course['description']), 0, 100) . '...'; ?>
                        </p>
                        <div class="course-meta">
                            Added by: <?php echo htmlspecialchars($course['creator'] ?: 'Admin'); ?><br>
                            <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
                        </div>
                        <div class="actions">
                            <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="edit-btn">✏️ Edit</a>
                            <a href="?delete=<?php echo $course['id']; ?>" class="delete-btn" 
                               onclick="return confirm('Delete this course? This cannot be undone.')">🗑️ Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="no-courses">
            <h2>No courses yet</h2>
            <p>Click <strong>"+ Add New Course"</strong> to create your first course.</p>
        </div>
    <?php endif; ?>

</div>
</body>
</html>
