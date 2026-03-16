<?php
// enroll.php — handles course enrollment

session_start();
require_once 'db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$course_id = $_GET['id'];
$user_id   = $_SESSION['user_id'];

// Check course exists
$stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
if (!$stmt->fetch()) {
    header("Location: index.php");
    exit();
}

// Check not already enrolled
$stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
$stmt->execute([$user_id, $course_id]);
if (!$stmt->fetch()) {
    // Enroll the user
    $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $course_id]);
}

header("Location: my_courses.php?msg=enrolled");
exit();
?>
