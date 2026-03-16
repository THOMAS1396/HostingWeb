<?php
// admin/applications.php — review teacher applications

session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Delete application
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM teacher_applications WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: applications.php?msg=deleted");
    exit();
}

// Fetch all applications
$stmt = $pdo->query("SELECT * FROM teacher_applications ORDER BY submitted_at DESC");
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Applications - Admin</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
        body { background:#f4f4f4; }

        .navbar { background:linear-gradient(135deg,#1a1a1a,#0f0f0f); color:white; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:100; }
        .navbar h2 { font-size:22px; }
        .nav-links a { color:white; text-decoration:none; margin-left:1.5rem; padding:0.5rem 1rem; border-radius:5px; font-size:14px; transition:background 0.3s; }
        .nav-links a:hover { background:rgba(255,255,255,0.1); }
        .nav-links a.logout { background:#c0622a; }

        .container { max-width:1100px; margin:2rem auto; padding:0 2rem; }

        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; }
        .header h1 { font-size:24px; color:#222; }
        .badge-count { background:#c0622a; color:white; padding:4px 12px; border-radius:20px; font-size:13px; font-weight:700; }

        .message { padding:0.8rem 1.2rem; border-radius:8px; font-size:14px; margin-bottom:1.2rem; }
        .message.success { background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; }

        .app-card { background:white; border-radius:12px; padding:1.5rem; margin-bottom:1.2rem; box-shadow:0 2px 8px rgba(0,0,0,0.07); display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:1rem; align-items:start; }

        .app-field label { font-size:11px; font-weight:700; color:#aaa; text-transform:uppercase; display:block; margin-bottom:3px; }
        .app-field span  { font-size:14px; color:#222; }

        .intro-field { grid-column:1 / -1; }
        .intro-field span { font-size:13px; color:#555; line-height:1.6; display:block; background:#fafafa; padding:10px; border-radius:6px; border:1px solid #eee; }

        .app-date { font-size:12px; color:#aaa; margin-top:6px; grid-column:1 / -1; }

        .actions { display:flex; flex-direction:column; gap:8px; }
        .btn-delete { background:#f44336; color:white; padding:7px 14px; border-radius:5px; text-decoration:none; font-size:13px; font-weight:600; text-align:center; }
        .btn-delete:hover { background:#d32f2f; }
        .btn-email  { background:#1a73e8; color:white; padding:7px 14px; border-radius:5px; text-decoration:none; font-size:13px; font-weight:600; text-align:center; }
        .btn-email:hover { background:#1557b0; }

        .no-apps { text-align:center; padding:3rem; background:white; border-radius:12px; color:#888; }
        .no-apps h2 { margin-bottom:6px; color:#444; }

        footer { text-align:center; padding:20px; font-size:13px; color:#999; border-top:1px solid #eee; margin-top:40px; background:white; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>💡 ClassPractice Admin</h2>
    <div class="nav-links">
        <a href="dashboard.php">Courses</a>
        <a href="../index.php">View Site</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <div class="header">
        <h1>Teacher Applications</h1>
        <span class="badge-count"><?php echo count($apps); ?> application<?php echo count($apps)!=1?'s':''; ?></span>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg']==='deleted'): ?>
        <div class="message success">🗑️ Application removed.</div>
    <?php endif; ?>

    <?php if(count($apps) > 0): ?>
        <?php foreach($apps as $app): ?>
        <div class="app-card">

            <div class="app-field">
                <label>Full Name</label>
                <span><?php echo htmlspecialchars($app['fullname']); ?></span>
            </div>

            <div class="app-field">
                <label>Email</label>
                <span><?php echo htmlspecialchars($app['email']); ?></span>
            </div>

            <div class="app-field">
                <label>Subject to Teach</label>
                <span><?php echo htmlspecialchars($app['subject']); ?></span>
            </div>

            <div class="actions">
                <a href="mailto:<?php echo htmlspecialchars($app['email']); ?>" class="btn-email">✉️ Email</a>
                <a href="?delete=<?php echo $app['id']; ?>" class="btn-delete"
                   onclick="return confirm('Remove this application?')">🗑️ Remove</a>
            </div>

            <div class="app-field">
                <label>Experience</label>
                <span><?php echo htmlspecialchars($app['experience'] ?: '—'); ?></span>
            </div>

            <div class="app-field">
                <label>Qualifications</label>
                <span><?php echo htmlspecialchars($app['qualifications'] ?: '—'); ?></span>
            </div>

            <div class="app-field">
                <label>Teaching Mode</label>
                <span><?php echo htmlspecialchars($app['mode'] ?: '—'); ?></span>
            </div>

            <div></div><!-- spacer for grid -->

            <div class="app-field intro-field">
                <label>Introduction</label>
                <span><?php echo htmlspecialchars($app['intro'] ?: 'No introduction provided.'); ?></span>
            </div>

            <div class="app-date">
                📅 Submitted: <?php echo date('M d, Y \a\t g:i A', strtotime($app['submitted_at'])); ?>
            </div>

        </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="no-apps">
            <h2>No applications yet</h2>
            <p>Teacher applications will appear here once submitted.</p>
        </div>
    <?php endif; ?>

</div>

<footer>&copy; 2024 ClassPractice &middot; admin panel</footer>
</body>
</html>
