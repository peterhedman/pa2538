<?php

// To protect any php page on your site, include main.php
// and create a new User object. It's that simple!

require_once 'includes/main.php';

$user = new User();

if(!$user->loggedIn()){
	redirect('index.php');
}

$thisPage = "Add Request";
$page_title = "DoYouWannaJoin - " . $thisPage;
$page_description = "En Sida för den aktiva";

/*
if (isset($_POST['remove_ip'])) {
   $unique_key = $_POST['uniqe_key'];
   $result = $user->remove_user_ip($unique_key);
}


function delete_ip_unique_key($unique_key){
	echo "<form name='delete_ip' method='post'>";
	echo "<input type='hidden' name='uniqe_key' value='$unique_key'>";
	echo "<input type='submit' class='confirm' name='remove_ip' value='Delete'</br>";
	echo "</form>";
}
*/

?>

<?php include("includes/header.php"); ?>

		<div id="protected-page">
			<img src="assets/img/lock.jpg" alt="Lock" />
			<h1>Add new Request</h1>
            <p>Email: <b><?php echo $user->email ?></b><br />
				Rank: <b style="text-transform:capitalize"><?php echo $user->rank() ?></b>
			</p>
           

		</div>

<?php include("includes/footer.php"); ?>