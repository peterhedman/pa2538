<?php

function send_email($from, $to, $subject, $message){

	// Helper function for sending email
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
	$headers .= 'From: '.$from . "\r\n";

	return mail($to, $subject, $message, $headers);
}

function get_page_url(){

	// Find out the URL of a PHP file

	$url = 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'];

	if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != ''){
		$url.= $_SERVER['REQUEST_URI'];
	}
	else{
		$url.= $_SERVER['PATH_INFO'];
	}

	return $url;
}

function rate_limit($ip, $limit_hour = 20, $limit_10_min = 10){
	global $db;
	// The number of login attempts for the last hour by this IP address
	$result = $db -> select("SELECT COUNT(*) AS countH FROM `reg_login_attempt` GROUP BY `ip`='". sprintf("%u", ip2long($ip)) . "' WHERE  `ts` > SUBTIME(NOW(),'1:00')");
	
	if (is_array($result)) {
		foreach($result as $row){
			$count_hour = $row['countH'];
		}
	}
	
	// The number of login attempts for the last 10 minutes by this IP address
	$result =  $db -> select("SELECT COUNT(*) AS countM FROM `reg_login_attempt` GROUP BY `ip`='". sprintf("%u", ip2long($ip)) . "' WHERE `ts` > SUBTIME(NOW(),'0:10')");
	
	if (is_array($result)) {
		foreach($result as $row){
			$count_10_min = $row['countM'];
		}
	}
	

	if($count_hour > $limit_hour || $count_10_min > $limit_10_min){
		throw new Exception('Too many login attempts!');
	}
}

function rate_limit_tick($ip, $email){
	global $db;
	// Create a new record in the login attempt table
	$result = $db -> query("INSERT INTO `reg_login_attempt` (`ip`,`email`) VALUES (" . sprintf("%u", ip2long($ip)) . "," . $email . ")");
}

function check_if_current_user($ip, $email){
	
	global $db;
	$ip_array=array();
	
	$ip = sprintf("%u", ip2long($ip));
	
	//Order by so that the latest login is on the last place
	$result =  $db -> select("SELECT * FROM `reg_logged_ip` WHERE `email`=" . $email . " ORDER BY `ts`");
	if (is_array($result)) {
		foreach($result as $row){
			$db_ip = $row['ip'];
			array_push($ip_array, $db_ip);
		}
	}
	
	// Gets the latest ip from database
	$last_db_ip = array_pop((array_slice($ip_array, -1)));
	
	// Checks if the database ip and the login attempt ip is the same AND if the User excists by email
	if(exists($email) && $last_db_ip == $ip){
		
		$user = new User("x",$email);
		//error_log("INNANFÖR User: " . json_encode($user) . " GETTOKEN: " . $user->getToken());
		
		// Exstends the token validity
		$result = $db -> query("UPDATE `reg_users` SET `token_validity`=ADDTIME(NOW(),'0:10') WHERE `email`=". $email);
		
		if($result){		
			$user->login();
			// Javascript checks if it's this message and if it is then reload page.
			throw new Exception('You\'re logging in...');
		}
		
	}
	
}



/**
 * find user by token
 */

function findByToken($token){
	
	// find it in the database and make sure the timestamp is correct
	
	$user = new User("x", "x",$token);
	
	if(!$user){
		return false;
	}

	return $user;
}

/**
 * Either login or register a user.
 */

function loginOrRegister($email){

	// If such a user already exists, return it

	if(exists($email)){
		$user = new User("x",$email); //gets the User by email
		$user->generateToken(); // Update Token
		$user = new User("x",$email); // Gets the new user after update
		return $user;
		
		//error_log("email: " . $email);		
	}
	
	// Otherwise, create it and return it
	return create($email);
	
	//error_log(json_encode($user));
}

/**
 * Create a new user and save it to the database
 */

function create($email){
	global $db;
	$token = sha1($email.time().rand(0, 1000000));
	
	// Write a new user to the database and return it
	$result = $db -> query("INSERT INTO `reg_users` (`email`, `token`, `token_validity`) VALUES (". $email . ", '" . $token . "', ADDTIME(NOW(),'0:10'))");
	$createdUserID = $db->insert_id;
	//$createdUserID = $db -> select("SELECT `id` FROM `reg_users` WHERE `email`=" . $email);
	//error_log("create result: " . json_encode($result) . " $createdUserID: " . $createdUserID);
		
	return new User("x",$email);
}

/**
 * Check whether such a user exists in the database and return a boolean.
 */

function exists($email){
	global $db;
	// Does the user exist in the database?
	
	$result = $db -> select("SELECT COUNT(email) AS Count FROM `reg_users` WHERE `email`=" . $email);
	
	
	if (is_array($result)) {
		foreach($result as $row){
			$count = $row['Count'];
		}
	}
	
	//error_log("result: " . $count);	
	return $count == 1;
}


function redirect($url){
	header("Location: $url");
	exit;
}