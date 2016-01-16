<?php

require_once 'assets/php/main.php';


/*--------------------------------------------------
	Handle visits with a login token. If it is
	valid, log the person in.
---------------------------------------------------*/


if(isset($_GET['tkn'])){

	// Is this a valid login token?
	$user = User::findByToken($_GET['tkn']);

	if($user){

		// Yes! Login the user and redirect to the protected page.

		$user->login();
		redirect('protected.php');
	}

	// Invalid token. Redirect back to the login form.
	redirect('index.php');
}



/*--------------------------------------------------
	Handle logging out of the system. The logout
	link in protected.php leads here.
---------------------------------------------------*/


if(isset($_GET['logout'])){

	$user = new User();

	if($user->loggedIn()){
		$user->logout();
	}

	redirect('index.php');
}


/*--------------------------------------------------
	Don't show the login page to already 
	logged-in users.
---------------------------------------------------*/


$user = new User();

if($user->loggedIn()){
	redirect('protected.php');
}



/*--------------------------------------------------
	Handle submitting the login form via AJAX
---------------------------------------------------*/


try{

	if(!empty($_POST) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])){

		// Output a JSON header

		header('Content-type: application/json');

		// Is the email address valid?

		if(!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			throw new Exception('Please enter a valid email.');
		}

		// This will throw an exception if the person is above 
		// the allowed login attempt limits (see functions.php for more):
		rate_limit($_SERVER['REMOTE_ADDR']);

		// Record this login attempt
		rate_limit_tick($_SERVER['REMOTE_ADDR'], $_POST['email']);

		// Send the message to the user

		$message = '';
		$email = $_POST['email'];
		$subject = 'Your Login Link';
		
		if(!User::exists($email)){
			$subject = "Thank You For Registering!";
			$message = "Thank you for registering at our site!\n\n";
		}

		// Attempt to login or register the person
		$user = User::loginOrRegister($_POST['email']);


		$message.= "You can login from this URL:\n";
		$message.= get_page_url()."?tkn=".$user->generateToken()."\n\n";

		$message.= "The link is going expire automatically after 10 minutes.";

		$result = send_email($fromEmail, $_POST['email'], $subject, $message);

		if(!$result){
			throw new Exception("There was an error sending your email. Please try again.");
		}

		die(json_encode(array(
			'message' => 'Thank you! We\'ve sent a link to your inbox. Check your spam folder as well.'
		)));
	}
}
catch(Exception $e){

	die(json_encode(array(
		'error'=>1,
		'message' => $e->getMessage()
	)));
}

/*--------------------------------------------------
	Output the login form
---------------------------------------------------*/

?>
<!DOCTYPE HTML>
<!--
	Solid State by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
        <link rel="icon" 
      type="image/png" 
      href="images/Procul-Games.png">
		<title>Procul Games - Login/Signup</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="assets/css/main.css" />
		<!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
                        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                        <script src="assets/js/jquery.min.js"></script>
	</head>
	<body>

		<!-- Page Wrapper -->
			<div id="page-wrapper">

				<!-- Header -->
					<header id="header">
						<h1><a href="index.html">Procul Games</a></h1>
						<nav>
							<a href="#menu">Menu</a>
						</nav>
					</header>

				<!-- Menu -->
					<nav id="menu">
						<div class="inner">
							<h2>Menu</h2>
							<ul class="links">
								<li><a href="index.html">Home</a></li>
								<li><a href="generic.html">About us</a></li>
								<li><a href="elements.html">Images</a></li>
							</ul>
							<a href="#" class="close">Close</a>
						</div>
					</nav>

				<!-- Wrapper -->
					<section id="wrapper">
						<header>
							<div class="inner">
								<h2>Login/Signup</h2>
								<p>Enter your email below and you will be sent a link that you can use to signup or login if you are already in the database.</p></div></header>

					</section>

				<!-- Footer -->
				<section id="footer">
					<div class="inner">
					  <form action="index.html" method="post" id="Login/Signup">
					    <div class="field">
							  <label for="email">Email</label>
									<input type="email" name="email" id="email" />
					    </div>
					    <ul class="actions">
									<li><input name="Submit" type="submit" id="Submit" value="Login/Signup" /></li></ul></form>
</div>
					</section>
			</div>
		<!-- Scripts -->
                <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-71910907-1', 'auto');
  ga('send', 'pageview');

</script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/util.js"></script>
			<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
			<script src="assets/js/main.js"></script>

	</body>
</html>