<?php
session_start();
require_once "db.php"; // connect to database

$msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

	$fname = trim($_POST["fullname"]);
	$email = trim($_POST["email"]);
	$exp   = trim($_POST["experience"]);
	$qual  = trim($_POST["qualifications"]);
	$subj  = trim($_POST["subject"]);
	$mode  = $_POST["mode"] ?? "";
	$intro = trim($_POST["intro"]);

	if(empty($fname) || empty($email) || empty($subj)){
		$msg = "please fill in the required fields";
	} else {
		// Save application to database
		$stmt = $pdo->prepare("INSERT INTO teacher_applications 
			(fullname, email, experience, qualifications, subject, mode, intro) 
			VALUES (?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$fname, $email, $exp, $qual, $subj, $mode, $intro]);
		$msg = "your application has been submitted!";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Teach</title>
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
			width: 400px;
			margin: 40px auto;
			background: white;
			padding: 30px;
		}

		.box label {
			display: block;
			margin-bottom: 6px;
			font-size: 14px;
			color: #222;
		}

		.box input[type="text"],
		.box input[type="email"],
		.box textarea {
			width: 100%;
			padding: 7px;
			margin-bottom: 18px;
			border: 1px solid #ccc;
			font-size: 13px;
			font-family: arial, sans-serif;
		}

		.box textarea {
			height: 70px;
			resize: vertical;
		}

		/* fieldset for radio group */
		fieldset {
			border: 1px solid #999;
			padding: 12px 16px;
			margin-bottom: 18px;
		}

		legend {
			font-size: 13px;
			color: #222;
			padding: 0 4px;
		}

		.radio-group {
			display: flex;
			flex-direction: column;
			gap: 10px;
			margin-top: 8px;
		}

		.radio-group label {
			font-size: 14px;
			margin: 0;
			display: flex;
			align-items: center;
			gap: 6px;
		}

		/* intro label style */
		.intro-label {
			font-size: 13px;
			color: #555;
			margin-bottom: 6px;
			display: block;
		}

		/* file input */
		.box input[type="file"] {
			margin-bottom: 20px;
			font-size: 13px;
		}

		/* submit button */
		.box input[type="submit"] {
			background-color: #3a7d44;
			color: white;
			border: none;
			padding: 10px 22px;
			font-size: 14px;
			cursor: pointer;
			border-radius: 4px;
		}

		.box input[type="submit"]:hover {
			background-color: #2e6336;
		}

		.msg {
			font-size: 13px;
			margin-bottom: 14px;
			color: green;
		}

		.msg.err {
			color: red;
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

<!-- form -->
<div class="box">

	<?php if($msg != ""): ?>
		<p class="msg <?php if(strpos($msg,'required') !== false) echo 'err'; ?>">
			<?php echo $msg; ?>
		</p>
	<?php endif; ?>

	<form method="POST" action="tech.php" enctype="multipart/form-data">

		<label>Full Name:</label>
		<input type="text" name="fullname">

		<label>Email:</label>
		<input type="email" name="email">

		<label>Teaching Experience:</label>
		<input type="text" name="experience">

		<label>Qualifications:</label>
		<input type="text" name="qualifications">

		<label>Subject To Teach:</label>
		<input type="text" name="subject">

		<fieldset>
			<legend>Preferred Teaching Mode:</legend>
			<div class="radio-group">
				<label>
					<input type="radio" name="mode" value="Online"> Online
				</label>
				<label>
					<input type="radio" name="mode" value="In-Person"> In-Person
				</label>
			</div>
		</fieldset>

		<span class="intro-label">Introduce yourself</span>
		<label>Brief Description of yourself</label>
		<textarea name="intro"></textarea>

		<input type="file" name="cv">

		<input type="submit" value="Submit">

	</form>

</div>

</body>
</html>