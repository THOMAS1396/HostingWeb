<?php
session_start();
require_once "db.php"; // connect to database

$msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

	$uname = trim($_POST["username"]);
	$email = trim($_POST["email"]);
	$pword = $_POST["password"];

	if(empty($uname) || empty($email) || empty($pword)){
		$msg = "please fill in all fields";
	} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$msg = "please enter a valid email";
	} else if(strlen($pword) < 6){
		$msg = "password must be at least 6 characters";
	} else {
		// Check if username already exists
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
		$stmt->execute([$uname]);
		if($stmt->fetch()){
			$msg = "username already taken";
		} else {
			// Check if email already exists
			$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
			$stmt->execute([$email]);
			if($stmt->fetch()){
				$msg = "email already registered";
			} else {
				// Save new user to database
				$hashed = password_hash($pword, PASSWORD_DEFAULT);
				$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
				$stmt->execute([$uname, $email, $hashed]);
				$msg = "account created! you can now login";
			}
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Sign Up</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: arial, sans-serif;
			background-image: url('images/nature.png');
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			min-height: 100vh;
		}

		/* navbar */
		nav {
			background-color: #1a1a1a;
			padding: 15px 30px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		nav .logo {
			font-size: 28px;
		}

		nav ul {
			list-style: none;
			display: flex;
			gap: 30px;
		}

		nav ul li a {
			color: white;
			text-decoration: none;
			font-size: 15px;
		}

		nav ul li a:hover {
			color: #f0a500;
		}

		/* form box */
		.box {
			width: 300px;
			margin: 120px auto;
			background: white;
			padding: 30px;
		}

		.box h3 {
			text-align: center;
			margin-bottom: 20px;
			font-size: 18px;
			color: #222;
		}

		.box label {
			display: block;
			margin-bottom: 5px;
			font-size: 14px;
		}

		.box input[type="text"],
		.box input[type="email"],
		.box input[type="password"] {
			width: 100%;
			padding: 6px;
			margin-bottom: 15px;
			border: 1px solid #ccc;
			box-sizing: border-box;
			font-size: 13px;
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
			font-size: 13px;
			margin-bottom: 12px;
			color: red;
		}

		.msg.ok {
			color: green;
		}

		.links {
			margin-top: 14px;
			font-size: 12px;
			color: #666;
			text-align: center;
		}

		.links a {
			color: #c0622a;
			text-decoration: none;
		}

		.links a:hover {
			text-decoration: underline;
		}
	</style>
</head>
<body>

<!-- navbar -->
<nav>
	<div class="logo">💡</div>
	<ul>
		<li><a href="index.php">Home</a></li>
		<li><a href="signup.php">Sign Up</a></li>
		<li><a href="login.php">Login</a></li>
		<li><a href="tech.php">Teach with US</a></li>
	</ul>
</nav>

<!-- signup form -->
<div class="box">

	<h3>Sign Up</h3>

	<?php if($msg != ""): ?>
		<p class="msg <?php if(strpos($msg,'created') !== false) echo 'ok'; ?>">
			<?php echo $msg; ?>
		</p>
	<?php endif; ?>

	<form method="POST" action="signup.php">

		<label>Username:</label>
		<input type="text" name="username"
			value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

		<label>Email:</label>
		<input type="email" name="email"
			value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

		<label>Password:</label>
		<input type="password" name="password">

		<input type="submit" value="Sign Up">

	</form>

	<div class="links">
		already have an account? <a href="login.php">login here</a>
	</div>

</div>

</body>
</html>