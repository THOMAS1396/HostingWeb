<?php
// course.php — individual course detail page

session_start();
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id   = $_GET['id'];
$stmt = $pdo->prepare("SELECT c.*, u.username as creator FROM courses c LEFT JOIN users u ON c.created_by = u.id WHERE c.id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header("Location: index.php");
    exit();
}

// Check if user is already enrolled
$isEnrolled = false;
if (isset($_SESSION['user_id'])) {
    $enStmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $enStmt->execute([$_SESSION['user_id'], $id]);
    $isEnrolled = (bool)$enStmt->fetch();
}

// Count total enrollments for this course
$cntStmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
$cntStmt->execute([$id]);
$enrollCount = $cntStmt->fetchColumn();

// Convert YouTube URL to embed URL
function youtubeEmbed($url) {
    if (empty($url)) return null;
    preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m);
    return isset($m[1]) ? "https://www.youtube.com/embed/{$m[1]}" : null;
}
$embedUrl = youtubeEmbed($course['video_url']);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($course['title']); ?> - ClassPractice</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:Arial,sans-serif; background:#f5f5f5; color:#222; }

        nav { background:#1a1a1a; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; }
        nav .logo { font-size:28px; }
        nav ul { list-style:none; display:flex; gap:20px; align-items:center; }
        nav ul li a { color:white; text-decoration:none; font-size:14px; padding:6px 12px; border-radius:5px; }
        nav ul li a:hover { color:#f0a500; }
        nav ul li a.btn-nav { background:#c0622a; }
        nav ul li a.btn-nav:hover { background:#a0501f; }
        .nav-user { color:#f0a500; font-size:14px; font-weight:600; }

        .container { max-width:900px; margin:2rem auto; padding:0 30px; }

        .breadcrumb { font-size:13px; color:#999; margin-bottom:1.5rem; }
        .breadcrumb a { color:#c0622a; text-decoration:none; }
        .breadcrumb a:hover { text-decoration:underline; }

        .course-header {
            background:white;
            border-radius:12px;
            overflow:hidden;
            box-shadow:0 2px 10px rgba(0,0,0,0.08);
            margin-bottom:1.5rem;
        }

        .course-banner {
            width:100%;
            height:300px;
            object-fit:cover;
        }

        .course-info { padding:2rem; }

        .badge {
            display:inline-block;
            background:#fff3e0;
            color:#c0622a;
            font-size:11px;
            font-weight:700;
            text-transform:uppercase;
            padding:4px 10px;
            border-radius:20px;
            margin-bottom:12px;
        }

        .course-title { font-size:26px; font-weight:700; margin-bottom:12px; color:#1a1a1a; }

        .course-meta {
            display:flex;
            gap:24px;
            font-size:13px;
            color:#888;
            margin-bottom:18px;
            flex-wrap:wrap;
        }
        .course-meta span { display:flex; align-items:center; gap:5px; }

        .course-description {
            font-size:15px;
            line-height:1.8;
            color:#444;
            margin-bottom:1.5rem;
        }

        .enroll-box {
            display:flex;
            align-items:center;
            gap:16px;
            flex-wrap:wrap;
        }

        .btn-enroll {
            background:#c0622a;
            color:white;
            padding:12px 32px;
            border-radius:25px;
            text-decoration:none;
            font-size:15px;
            font-weight:700;
            transition:background 0.2s;
            border:none;
            cursor:pointer;
        }
        .btn-enroll:hover { background:#a0501f; }

        .btn-enrolled {
            background:#4caf50;
            color:white;
            padding:12px 32px;
            border-radius:25px;
            font-size:15px;
            font-weight:700;
        }

        .btn-login {
            background:#1a1a1a;
            color:white;
            padding:12px 28px;
            border-radius:25px;
            text-decoration:none;
            font-size:14px;
            font-weight:600;
        }

        /* Video section */
        .video-section {
            background:white;
            border-radius:12px;
            overflow:hidden;
            box-shadow:0 2px 10px rgba(0,0,0,0.08);
            margin-bottom:1.5rem;
        }
        .video-section h3 {
            padding:1.2rem 1.5rem;
            font-size:16px;
            color:#222;
            border-bottom:1px solid #f0f0f0;
        }
        .video-wrapper {
            position:relative;
            padding-bottom:56.25%;
            height:0;
        }
        .video-wrapper iframe {
            position:absolute;
            top:0; left:0;
            width:100%; height:100%;
            border:none;
        }

        footer { text-align:center; padding:20px; font-size:13px; color:#999; border-top:1px solid #eee; margin-top:40px; background:white; }
    </style>
</head>
<body>

<nav>
  <div class="logo">💡</div>
  <ul>
    <li><a href="index.php">Home</a></li>
    <?php if(isset($_SESSION['user_id'])): ?>
      <li><a href="my_courses.php">My Courses</a></li>
      <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <li><a href="admin/dashboard.php">Admin Panel</a></li>
      <?php endif; ?>
      <li><span class="nav-user">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    <?php else: ?>
      <li><a href="signup.php">Sign Up</a></li>
      <li><a href="login.php" class="btn-nav">Login</a></li>
    <?php endif; ?>
  </ul>
</nav>

<div class="container">

    <div class="breadcrumb">
        <a href="index.php">Home</a> &rsaquo; <a href="index.php">Courses</a> &rsaquo; <?php echo htmlspecialchars($course['title']); ?>
    </div>

    <!-- Course Header -->
    <div class="course-header">
        <img class="course-banner"
             src="<?php echo htmlspecialchars($course['image_url']?:'images/python.png'); ?>"
             alt="<?php echo htmlspecialchars($course['title']); ?>"
             onerror="this.src='images/python.png'">
        <div class="course-info">
            <div class="badge"><?php echo htmlspecialchars($course['category']); ?></div>
            <h1 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h1>
            <div class="course-meta">
                <span>👤 Instructor: <?php echo htmlspecialchars($course['creator'] ?: 'ClassPractice Team'); ?></span>
                <span>👥 <?php echo $enrollCount; ?> student<?php echo $enrollCount!=1?'s':''; ?> enrolled</span>
                <span>📅 Added <?php echo date('M d, Y', strtotime($course['created_at'])); ?></span>
            </div>
            <p class="course-description"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>

            <div class="enroll-box">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($isEnrolled): ?>
                        <span class="btn-enrolled">✅ You are enrolled</span>
                        <a href="my_courses.php">Go to My Courses →</a>
                    <?php else: ?>
                        <a href="enroll.php?id=<?php echo $course['id']; ?>" class="btn-enroll">🎓 Enroll Now — It's Free</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn-enroll">Login to Enroll</a>
                    <a href="signup.php" class="btn-login">Create Account</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Video -->
    <?php if($embedUrl): ?>
    <div class="video-section">
        <h3>▶ Course Video</h3>
        <div class="video-wrapper">
            <iframe src="<?php echo $embedUrl; ?>" allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            </iframe>
        </div>
    </div>
    <?php endif; ?>

</div>

<footer>&copy; 2024 ClassPractice &middot; crafted for curious minds</footer>
</body>
</html>
