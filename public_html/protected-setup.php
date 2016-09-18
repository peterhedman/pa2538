<?php

// To protect any php page on your site, include main.php
// and create a new User object. It's that simple!

require_once 'includes/main.php';

$user = new User();

if(!$user->loggedIn()){
	redirect('index.php');
}

$page_title = "DoYouWannaJoin - Setup";
$page_description = "En Sida för den aktiva";

if (isset($_POST['remove_ip'])) {
   $unique_key = $_POST['uniqe_key'];
   $result = $user->remove_user_ip($unique_key);
}

if (isset($_POST['login_from'])) {
	
	if(isset($_POST['login_from_all'])){
		$ip_choise = $_POST['login_from_all'];
	} else if(isset($_POST['login_from_none'])){
		$ip_choise = $_POST['login_from_none'];
	} else if(isset($_POST['login_from_prefered'])){
		$ip_choise = $_POST['login_from_prefered'];
	}
	
	$result = $user->set_prefered_ip($ip_choise);
	
   error_log("ip_choise: " . $ip_choise);
   
   //$result = $user->remove_user_ip($unique_key);
}

if (isset($_POST['remove_user'])) {
   $result = $user->remove_user();
   redirect('index.php');
}

function login_from_all(){
	echo "<form name='login_from_all' method='post'>";
	echo "<input type='hidden' name='login_from_all' value='1'>";
	echo "<input type='submit' name='login_from' value='Log in from all activated ip'</br>";
	echo "</form>";
}

function login_from_none(){
	echo "<form name='login_from_none' method='post'>";
	echo "<input type='hidden' name='login_from_none' value='0'>";
	echo "<input type='submit' name='login_from' value='Log in from no ip'</br>";
	echo "</form>";
}

function login_from_prefered($ip_key){
	echo "<form name='login_from_prefered' method='post'>";
	echo "<input type='hidden' name='login_from_prefered' value='$ip_key'>";
	echo "<input type='submit' name='login_from' value='Log in from this ip only'</br>";
	echo "</form>";
}

function delete_ip_unique_key($unique_key){
	echo "<form name='delete_ip' method='post'>";
	echo "<input type='hidden' name='uniqe_key' value='$unique_key'>";
	echo "<input type='submit' name='remove_ip' value='Delete'</br>";
	echo "</form>";
}


?>

<?php include("includes/header.php"); ?>

		<div id="protected-page">
			<img src="assets/img/lock.jpg" alt="Lock" />
			<h1>Account Setup</h1>
            <p>Email: <b><?php echo $user->email ?></b><br />
				Rank: <b style="text-transform:capitalize"><?php echo $user->rank() ?></b>
			</p>
            </br>
            <h2>Your registred IP addresses</h2>
            <p>Current IP Adress: <?php echo $_SERVER['REMOTE_ADDR']?>
            </br>
            Login from: 
			<?php 
				if($user->prefered_ip == 1)
				{
					echo "All logged ip";
					login_from_none();
					
				} else if($user->prefered_ip != 0){
					echo long2ip($user->prefered_ip);
					login_from_all();
					login_from_none();
				} else {
					echo "You get a activation link on mail everytime you want to login";
					login_from_all();
				}
			?>
            </p>
            
            
			
            <?php
			
			$addresses = $user->get_logged_ipadresses();
			$counter = 1;
			echo '</br><p>';
			foreach($addresses as $address){
				$ip_key = $address['ip'];
				echo "IP Adress" . $counter++ . ": " . long2ip($ip_key);
				
				$unique_key = $address['uniquekey'];
				login_from_prefered($ip_key);
				delete_ip_unique_key($unique_key);
				echo "</br>";
			}
			echo '</p>';
			
			?>
            <h2>Delete Your account</h2>
            <p>Delete your account by clicking the button below</p>
            <form name='delete_user' method='post'>
			<input type='submit' name='remove_user' value='Delete'</br>
			</form>

		</div>

<?php include("includes/footer.php"); ?>