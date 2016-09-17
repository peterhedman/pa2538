<?php

function findByToken($token){
	
	// find it in the database and make sure the timestamp is correct
	
	$user = User::withToken( $token );
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
		$user = User::withEmail( $email );
		return $user;
	}
	
	// Otherwise, create it and return it
	return create($email);
}

/**
 * Create a new user and save it to the database
 */

function create($email){
	global $db;
	
	// Write a new user to the database and return it
	$result = $db -> query("INSERT INTO `reg_users` (`email`) VALUES (" . $email . ")");
	$createdUserID = mysql_insert_id;
	//$createdUser = $db -> select("SELECT * FROM `reg_users` WHERE `email`=" . $email);
	
	return User::withID( $createdUserID );
}

/**
 * Check whether such a user exists in the database and return a boolean.
 */

function exists($email){
	global $db;
	// Does the user exist in the database?
	
	$result = $db -> select("SELECT COUNT(*) FROM `reg_users` WHERE `email`=" . $email);
	
	return $result == 1;
}



/**
 * Generates a new SHA1 login token, writes it to the database and returns it.
 */

function generateToken($email){
	// generate a token for the logged in user. Save it to the database.
	global $db;
	
	$token = sha1($email.time().rand(0, 1000000));
	
	//print_r($this->user);
	
	// Save the token to the database, 
	// and mark it as valid for the next 10 minutes only
	$result = $db -> query("UPDATE `reg_users` SET `token`='".$token."', `token_validity`=ADDTIME(NOW(),'0:10') WHERE `email`=".$email . "");
	
	if($result)
	{
		return $token;
	}
	
	return "00000";
}


?>