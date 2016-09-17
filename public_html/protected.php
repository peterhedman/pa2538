<?php

// To protect any php page on your site, include main.php
// and create a new User object. It's that simple!

require_once 'includes/main.php';

$user = new User();

if(!$user->loggedIn()){
	redirect('index.php');
}

$page_title = "DoYouWannaJoin - Protectd";
$page_description = "En Sida för den aktiva";

?>

<?php include("includes/header.php"); ?>

		<div id="protected-page">
			<img src="assets/img/lock.jpg" alt="Lock" />
			<h1>You are logged in!</h1>

			<p>Email: <b><?php echo $user->email ?></b><br />
				Rank: <b style="text-transform:capitalize"><?php echo $user->rank() ?></b>
			</p>

			<a href="login-form.php?logout=1" class="logout-button">Logout</a>

		</div>

<?php include("includes/footer.php"); ?>