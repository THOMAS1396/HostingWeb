<?php
// =============================================
//  DATABASE CONNECTION
//  Change these values to match your setup
// =============================================

$host   = "localhost";       // usually localhost
$dbname = "classpractice_db"; // your database name
$user   = "root";            // your MySQL username
$pass   = "";                // your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e){
    die("Database connection failed: " . $e->getMessage());
}
?>
