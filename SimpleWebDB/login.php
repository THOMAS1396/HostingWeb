<?php
session_start();
require_once "db.php"; // connect to database

$msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	$uname = trim($_POST["username"]);
	$pword = $_POST["password"];
	
	if(empty($uname) || empty($pword)){
		$msg = "please fill in all fields";
	} else {
		// Look up user in the database
		$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->execute([$uname]);
		$user = $stmt->fetch();

		if($user && password_verify($pword, $user["password"])){
			$_SESSION["username"] = $uname;
			$_SESSION["user_id"]  = $user["id"];
			$_SESSION["role"]     = $user["role"] ?? "user";
			if($_SESSION["role"] === "admin"){
				header("Location: admin/dashboard.php");
			} else {
				header("Location: index.php");
			}
			exit();
		} else {
			$msg = "wrong username or password";
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login Form</title>
	<style>
		body {
			margin: 0;
			padding: 0;
			font-family: arial, sans-serif;
			background-image: url('images/nature.png');
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			min-height: 100vh;
		}

		.box {
			width: 250px;
			margin: 150px auto;
			background: white;
			padding: 30px;
		}

		.box label {
			display: block;
			margin-bottom: 5px;
			font-size: 14px;
		}

		.box input[type="text"],
		.box input[type="password"] {
			width: 100%;
			padding: 6px;
			margin-bottom: 15px;
			border: 1px solid #ccc;
			box-sizing: border-box;
		}

		.box input[type="submit"] {
			background-color: #c0622a;
			color: white;
			border: none;
			padding: 8px 18px;
			cursor: pointer;
			font-size: 14px;
		}

		.box input[type="submit"]:hover {
			background-color: #a0501f;
		}

		.msg {
			color: red;
			font-size: 13px;
			margin-bottom: 10px;
		}
	</style>
</head>
<body>

<div class="box">

	<?php if($msg != ""){ ?>
		<p class="msg"><?php echo $msg; ?></p>
	<?php } ?>

	<form method="POST" action="login.php">

		<label>Username:</label>
		<input type="text" name="username">

		<label>Password:</label>
		<input type="password" name="password">

		<input type="submit" value="Login">

	</form>

</div>

</body>
</html>
