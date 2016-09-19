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


if (isset($_POST['remove_ip'])) {
   $unique_key = $_POST['uniqe_key'];
   $result = $user->remove_user_ip($unique_key);
}

//gets all circuts
$run_circuts = get_all_run_circuts();


// the input fomr
$nameErr = $start_timeErr = $start_dateErr = $circutErr ="";
$name = $start_time = $start_date ="";
if (isset($_POST['add_new_request'])) {
	// GET THE NAME
	if (empty($_POST["add_request_name"])) {
		$nameErr = "Name is required";
	} else {
		$name = $_POST["add_request_name"];
		// check if name only contains letters and whitespace
		if (!preg_match("/^[a-zåäöA-ZÅÄÖ ]*$/",$name)) {
			$nameErr = "Only letters and white space allowed";
			$checkerName = false;
		} else {
			$checkerName = true;	
		}
	  }
	  
	// GET THE START DATE  
	if (empty($_POST["add_request_start_date"])) {
		$start_dateErr = "Start Date is required";
	} else {
		$start_date = $_POST["add_request_start_date"];
		// check if name only contains letters and whitespace
		if (!preg_match("/^(\d{4})-(\d{2})-(\d{2})?$/",$start_date)) {
			$start_dateErr = "Date should be by: YYYY-MM-DD";
			$checkerStartDate = false;	
		} else {
			$checkerStartDate = true;	
		}
	}
	
	// GET THE START TIME
	if (empty($_POST["add_request_start_time"])) {
		$start_timeErr = "Start Time is required";
	} else {
		$start_time = $_POST["add_request_start_time"];
		// check if name only contains letters and whitespace
		if (!preg_match("/^(\d{2}):(\d{2})?$/",$start_time)) {
			$start_timeErr = "Time should be by: HH:MM";
			$checkerStartTime = false;	
		} else {
			$checkerStartTime = true;	
		}
	}
	
	$comment = $_POST["add_request_comment"];
	
	if (!isset($_POST["circut"])) {
		$circutErr = "Circut is required";
		$checkerCircut = false;
	} else {
		$run_circut_id = $_POST["circut"];
		$checkerCircut = true;
	}
	
	
	
	
	if($checkerName && $checkerStartDate && $checkerStartTime && $checkerCircut){
		$result = add_new_Request($user->id, $run_circut_id, $name, $start_date, $start_time, $comment);
	}
	
}

function create_request($nameErr, $start_timeErr, $start_dateErr, $circutErr, $run_circuts){
	if (is_array($run_circuts)) {
		echo "<form name='add_request' method='post'>";
		
		echo "Name: <input type='text' name='add_request_name' value='$add_request_name'>";
		echo "<span class='error'>* $nameErr </span> </br>";
		
		echo "Start Date: <input type='text' name='add_request_start_date' value='$add_request_start_date'>";
		echo "<span class='error'>* $start_dateErr </span> </br>";
		
		echo "Start Time: <input type='text' name='add_request_start_time' value='$add_request_start_time'>";
		echo "<span class='error'>* $start_timeErr </span> </br>";
		
		echo "Comment: <textarea name='add_request_comment' rows='5' cols='40'>$add_request_comment</textarea> </br>";
	
		echo "Circut:"; 
		foreach($run_circuts as $circut_db){
			echo "  <input type='radio' name='circut' if(isset($circut) && $circut=='" . $circut_db['title'] . "') 'checked' value='" . $circut_db['id'] . "'> " .$circut_db['title'];
		}
		echo "<span class='error'>* $circutErr </span> </br>";
		
		echo "<input type='submit' class='confirm' name='add_new_request' value='Add new Request'</br>";
		echo "</form>";
	} else {
		echo "No circuts to add request to...";
	}
}


?>

<?php include("includes/header.php"); ?>

		<div id="protected-page">
			<img src="assets/img/lock.jpg" alt="Lock" />
			<h1>Add new Request</h1>
            <?php create_request($nameErr, $start_timeErr, $start_dateErr, $circutErr, $run_circuts); ?>
            

		</div>

<?php include("includes/footer.php"); ?>