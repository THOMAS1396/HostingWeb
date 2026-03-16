<?php
// my_courses.php — shows all courses a user is enrolled in

session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch enrolled courses
$stmt = $pdo->prepare("
    SELECT c.*, e.enrolled_at
    FROM courses c
    JOIN enrollments e ON c.id = e.course_id
    WHERE e.user_id = ?
    ORDER BY e.enrolled_at DESC
");
$stmt->execute([$user_id]);
$myCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle unenroll
if (isset($_GET['unenroll'])) {
    $cid = $_GET['unenroll'];
    $del = $pdo->prepare("DELETE FROM enrollments WHERE user_id = ? AND course_id = ?");
    $del->execute([$user_id, $cid]);
    header("Location: my_courses.php?msg=unenrolled");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Courses - ClassPractice</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#f5f5f5; color:#222; }

        nav { background:#1a1a1a; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; }
        nav .logo { font-size:28px; }
        nav ul { list-style:none; display:flex; gap:20px; align-items:center; }
        nav ul li a { color:white; text-decoration:none; font-size:14px; padding:6px 12px; border-radius:5px; }
        nav ul li a:hover { color:#f0a500; }
        nav ul li a.btn-nav { background:#c0622a; }
        .nav-user { color:#f0a500; font-size:14px; font-weight:600; }

        .container { max-width:1100px; margin:2rem auto; padding:0 30px; }

        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
        .page-header h1 { font-size:24px; }

        .underline { width:60px; height:3px; background:#c0622a; margin-bottom:28px; }

        .message { padding:0.8rem 1.2rem; border-radius:8px; margin-bottom:1.2rem; font-size:14px; font-weight:600; }
        .message.success { background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; }

        .courses { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:24px; }

        .card { background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); display:flex; flex-direction:column; transition:transform 0.2s; }
        .card:hover { transform:translateY(-3px); }
        .card img { width:100%; height:170px; object-fit:cover; }
        .card-body { padding:16px; flex:1; display:flex; flex-direction:column; }
        .card-category { font-size:11px; font-weight:700; color:#c0622a; text-transform:uppercase; margin-bottom:5px; }
        .card h3 { font-size:15px; font-weight:700; margin-bottom:8px; }
        .card p { font-size:13px; color:#555; line-height:1.5; flex:1; margin-bottom:12px; }
        .enrolled-date { font-size:12px; color:#aaa; margin-bottom:12px; }

        .card-actions { display:flex; gap:8px; }
        .btn-view { background:#1a1a1a; color:white; padding:7px 14px; border-radius:20px; text-decoration:none; font-size:12px; font-weight:600; }
        .btn-view:hover { background:#333; }
        .btn-unenroll { background:#f5f5f5; color:#888; padding:7px 14px; border-radius:20px; text-decoration:none; font-size:12px; font-weight:600; border:1px solid #ddd; }
        .btn-unenroll:hover { background:#fee; color:#c62828; border-color:#f5c6c6; }

        .empty { text-align:center; padding:3rem; background:white; border-radius:10px; color:#888; }
        .empty h3 { margin-bottom:8px; color:#444; }
        .empty a { color:#c0622a; font-weight:600; text-decoration:none; }

        footer { text-align:center; padding:20px; font-size:13px; color:#999; border-top:1px solid #eee; margin-top:40px; background:white; }
    </style>
</head>
<body>

<nav>
  <div class="logo">💡</div>
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="my_courses.php">My Courses</a></li>
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
      <li><a href="admin/dashboard.php">Admin Panel</a></li>
    <?php endif; ?>
    <li><span class="nav-user">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
    <li><a href="logout.php" class="btn-nav">Logout</a></li>
  </ul>
</nav>

<div class="container">

    <div class="page-header">
        <h1>My Courses</h1>
        <a href="index.php" style="font-size:14px; color:#c0622a; text-decoration:none;">← Browse More Courses</a>
    </div>
    <div class="underline"></div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="message success">
            <?php
                if($_GET['msg']==='enrolled')   echo "🎉 You've successfully enrolled!";
                if($_GET['msg']==='unenrolled') echo "You've been unenrolled from the course.";
            ?>
        </div>
    <?php endif; ?>

    <?php if(count($myCourses) > 0): ?>
        <div class="courses">
            <?php foreach($myCourses as $course): ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($course['image_url']?:'images/python.png'); ?>"
                     alt="<?php echo htmlspecialchars($course['title']); ?>"
                     onerror="this.src='images/python.png'">
                <div class="card-body">
                    <div class="card-category"><?php echo htmlspecialchars($course['category']); ?></div>
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($course['description'],0,80)).'...'; ?></p>
                    <div class="enrolled-date">📅 Enrolled <?php echo date('M d, Y', strtotime($course['enrolled_at'])); ?></div>
                    <div class="card-actions">
                        <a href="course.php?id=<?php echo $course['id']; ?>" class="btn-view">▶ View Course</a>
                        <a href="my_courses.php?unenroll=<?php echo $course['id']; ?>" class="btn-unenroll"
                           onclick="return confirm('Unenroll from this course?')">Unenroll</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <h3>No courses yet</h3>
            <p>You haven't enrolled in any courses. <a href="index.php">Browse courses →</a></p>
        </div>
    <?php endif; ?>

</div>

<footer>&copy; 2024 ClassPractice &middot; crafted for curious minds</footer>
</body>
</html>
