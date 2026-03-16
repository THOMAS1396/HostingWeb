<?php
session_start();
require_once 'db.php';

$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');

// Build query with optional filters
$where  = [];
$params = [];
if ($search !== '') {
    $where[]  = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category !== '') {
    $where[]  = "category = ?";
    $params[] = $category;
}
$sql = "SELECT * FROM courses";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get distinct categories from DB for filter dropdown
$catStmt    = $pdo->query("SELECT DISTINCT category FROM courses ORDER BY category");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Get user's enrolled course IDs (if logged in)
$enrolled = [];
if (isset($_SESSION['user_id'])) {
    $enStmt = $pdo->prepare("SELECT course_id FROM enrollments WHERE user_id = ?");
    $enStmt->execute([$_SESSION['user_id']]);
    $enrolled = $enStmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>ClassPractice</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,sans-serif; background:#f5f5f5; color:#222; }

nav { background:#1a1a1a; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; }
nav .logo { font-size:28px; }
nav ul { list-style:none; display:flex; gap:20px; align-items:center; }
nav ul li a { color:white; text-decoration:none; font-size:14px; padding:6px 12px; border-radius:5px; transition:background 0.2s; }
nav ul li a:hover { color:#f0a500; }
nav ul li a.btn-nav { background:#c0622a; color:white; }
nav ul li a.btn-nav:hover { background:#a0501f; }
.nav-user { color:#f0a500; font-size:14px; font-weight:600; }

.hero { background:linear-gradient(135deg,#1a1a1a,#3a1a0a); color:white; padding:50px 30px; text-align:center; }
.hero h1 { font-size:32px; margin-bottom:10px; }
.hero p  { color:#ccc; font-size:15px; margin-bottom:24px; }

.search-bar { display:flex; justify-content:center; gap:10px; flex-wrap:wrap; }
.search-bar input[type="text"] { padding:10px 16px; border-radius:25px; border:none; font-size:14px; width:300px; outline:none; }
.search-bar select { padding:10px 14px; border-radius:25px; border:none; font-size:14px; outline:none; }
.search-bar button { padding:10px 24px; background:#c0622a; color:white; border:none; border-radius:25px; font-size:14px; cursor:pointer; }
.search-bar button:hover { background:#a0501f; }
.clear-link { color:#ccc; font-size:13px; text-decoration:none; align-self:center; }
.clear-link:hover { color:white; }

.main { padding:40px 30px; max-width:1200px; margin:0 auto; }
.section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
.section-header h2 { font-size:22px; }
.result-count { font-size:13px; color:#999; }
.underline { width:60px; height:3px; background:#c0622a; margin-bottom:28px; }

.courses { display:grid; grid-template-columns:repeat(3,1fr); gap:30px; }

.card { background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08); display:flex; flex-direction:column; transition:transform 0.2s,box-shadow 0.2s; }
.card:hover { transform:translateY(-4px); box-shadow:0 6px 16px rgba(0,0,0,0.12); }
.card img { width:100%; height:180px; object-fit:cover; }
.card-body { padding:16px; flex:1; display:flex; flex-direction:column; }
.card-category { font-size:11px; font-weight:700; color:#c0622a; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; }
.card h3 { font-size:17px; font-weight:bold; margin-bottom:10px; color:#222; }
.card p { font-size:13px; color:#444; line-height:1.6; margin-bottom:14px; flex:1; }
.card-actions { display:flex; gap:8px; flex-wrap:wrap; }

.btn-watch { display:inline-block; background:#c0622a; color:white; padding:10px 20px; border-radius:25px; text-decoration:none; font-size:13px; font-weight:600; }
.btn-watch:hover { background:#a0501f; }
.btn-detail { display:inline-block; background:#1a1a1a; color:white; padding:10px 16px; border-radius:25px; text-decoration:none; font-size:13px; font-weight:600; }
.btn-detail:hover { background:#333; }
.btn-enroll { display:inline-block; background:#2196f3; color:white; padding:10px 16px; border-radius:25px; text-decoration:none; font-size:13px; font-weight:600; }
.btn-enroll:hover { background:#1976d2; }
.btn-enrolled { display:inline-block; background:#4caf50; color:white; padding:10px 16px; border-radius:25px; font-size:13px; font-weight:600; }

.empty { grid-column:1/-1; text-align:center; padding:3rem; background:white; border-radius:10px; color:#888; }
.empty h3 { margin-bottom:8px; color:#444; }

footer { text-align:center; padding:20px; font-size:13px; color:#666; border-top:1px solid #eee; margin-top:50px; background:white; }
</style>
</head>
<body>

<!-- Navbar -->
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
      <li><a href="tech.php">Teach with Us</a></li>
    <?php endif; ?>
  </ul>
</nav>

<!-- Hero + Search -->
<div class="hero">
  <h1>Available Courses</h1>
  <p>Browse our courses and start building your skills</p>
  <form method="GET" action="index.php" class="search-bar">
    <input type="text" name="search" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>">
    <select name="category">
      <option value="">All Categories</option>
      <?php foreach($categories as $cat): ?>
        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($category===$cat)?'selected':''; ?>>
          <?php echo htmlspecialchars($cat); ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Search</button>
    <?php if($search || $category): ?>
      <a href="index.php" class="clear-link">✕Clear</a>
    <?php endif; ?>
  </form>
</div>

<!-- Courses -->
<div class="main">
  <div class="section-header">
    <h2><?php echo ($search||$category) ? 'Search Results' : 'Popular Courses'; ?></h2>
    <span class="result-count"><?php echo count($courses); ?> course<?php echo count($courses)!=1?'s':''; ?></span>
  </div>
  <div class="underline"></div>

  <div class="courses">
    <?php if(count($courses) > 0): ?>
      <?php foreach($courses as $course): ?>
      <div class="card">
        <img src="<?php echo htmlspecialchars($course['image_url'] ?: 'images/python.png'); ?>"
             alt="<?php echo htmlspecialchars($course['title']); ?>"
             onerror="this.src='images/python.png'">
        <div class="card-body">
          <div class="card-category"><?php echo htmlspecialchars($course['category']); ?></div>
          <h3><?php echo htmlspecialchars($course['title']); ?></h3>
          <p><?php echo htmlspecialchars($course['description']); ?></p>
          <div class="card-actions">
            <?php if(!empty($course['video_url'])): ?>
              <a href="<?php echo htmlspecialchars($course['video_url']); ?>" class="btn-watch" target="_blank">&#9658; Watch</a>
            <?php endif; ?>
            <a href="course.php?id=<?php echo $course['id']; ?>" class="btn-detail">Details</a>
            <?php if(isset($_SESSION['user_id'])): ?>
              <?php if(in_array($course['id'], $enrolled)): ?>
                <span class="btn-enrolled">✅ Enrolled</span>
              <?php else: ?>
                <a href="enroll.php?id=<?php echo $course['id']; ?>" class="btn-enroll">Enroll</a>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty">
        <h3>No courses found</h3>
        <p><?php echo ($search||$category) ? 'Try different keywords or <a href="index.php" style="color:#c0622a;">clear the filter</a>.' : 'No courses yet. Add some from the <a href="admin/dashboard.php" style="color:#c0622a;">Admin Panel</a>.'; ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>

<footer>&copy; 2024 ClassPractice &middot; crafted for curious minds</footer>
</body>
</html>
