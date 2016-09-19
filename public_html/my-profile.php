<?php

// To protect any php page on your site, include main.php
// and create a new User object. It's that simple!

require_once 'includes/main.php';

$user = new User();

if(!$user->loggedIn()){
	redirect('index.php');
}

$thisPage = "My profile";
$page_title = "DoYouWannaJoin - " . $thisPage;
$page_description = "En Sida för den aktiva";

/*** 
 The recived values via post
 ***/

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

$nameErr = $lengthErr = "";
$name = $length = "";
if (isset($_POST['new_run_circuts'])) {
	if (empty($_POST["new_run_circuts_name"])) {
		$nameErr = "Name is required";
	} else {
		$name = $_POST["new_run_circuts_name"];
		// check if name only contains letters and whitespace
		if (!preg_match("/^[a-zåäöA-ZÅÄÖ ]*$/",$name)) {
			$nameErr = "Only letters and white space allowed";
			$checkerName = false;
		} else {
			$checkerName = true;	
		}
	  }
	  
	if (empty($_POST["new_run_circuts_length"])) {
		$lengthErr = "Length is required";
	} else {
		$length = $_POST["new_run_circuts_length"];
		// check if name only contains letters and whitespace
		if (!preg_match("/^[1-9][0-9]{0,10}$/",$length)) {
			$lengthErr = "Length should be in meters, only numbers allowed";
			$checkerLength = false;	
		} else {
			$checkerLength = true;	
		}
	}
	
	if($checkerName && $checkerLength){
		$result = add_new_run_circuts($name, $length);
	}
	
}
  

/***
 The output forms
***/


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
	echo "<input type='submit' class='confirm' name='remove_ip' value='Delete'</br>";
	echo "</form>";
}

function new_run_circuts($nameErr, $lengthErr){
	echo "<form name='add_new_run_circuts' method='post'>";
	echo "Name: <input type='text' name='new_run_circuts_name' value='$new_run_circuts_name'>";
	echo "<span class='error'>* $nameErr </span> </br>";
	echo "Length: <input type='text' name='new_run_circuts_length' value='$new_run_circuts_length'>";
	echo "<span class='error'>* $lengthErr </span> </br>";
	echo "<input type='submit' class='confirm' name='new_run_circuts' value='Add new Circut'</br>";
	echo "</form>";
	echo "</br>";
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
            
            <?php 
			
			if($user->isAdmin()){
				echo '<h2>Add Run circuts </h2>';
				new_run_circuts($nameErr, $lengthErr);
				
			}
			
			
			?>
            <h2>Your registred IP addresses</h2>
            <p>Current IP Adress: <?php echo $_SERVER['REMOTE_ADDR']?>
            </br>
              
			<?php 
				if($user->prefered_ip == 1)
				{
					echo "Current login setting: Login from all logged ip addresses.";
					login_from_none();
					
				} else if($user->prefered_ip != 0){
					echo "Login from " . long2ip($user->prefered_ip) . " only.";
					login_from_all();
					login_from_none();
				} else {
					echo "You get a activation link on mail everytime you want to login.";
					login_from_all();
				}
			?>
            </p>
            </br>
            <h3>Your logged ip addresses</h3>
			
            <?php
			
			$addresses = $user->get_logged_ipadresses();
			$counter = 1;
			echo '</br><p>';
			foreach($addresses as $address){
				$ip_key = $address['ip'];
				echo "IP Address" . $counter++ . ": " . long2ip($ip_key);
				
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
			<input type='submit' class='confirm' name='remove_user' value='Delete'</br>
			</form>

		</div>

<?php include("includes/footer.php"); ?>