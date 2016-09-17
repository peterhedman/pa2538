<?php

/**
 * Include the libraries
 */

require_once __DIR__."/Db.class.php";
require_once __DIR__."/User.class.php";
require_once __DIR__."/functions.php";

/**
 * Creates Database
 */

$db = new Db();

//$result = $db -> query("INSERT INTO `reg_users` (`email` , `rank` , `token`) VALUES ('hsahdasd©jsdsd.se' , '0' , '123')");

//print_r($result);

/**
 * Configure the session
 */

session_name('tzreg');

// Uncomment to keep people logged in for a week
// session_set_cookie_params(60 * 60 * 24 * 7);

session_start();

/**
 * Other settings
 */

// The "from" email address that is used in the emails that are sent to users.
// Some hosting providers block outgoing email if this address
// is not registered as a real email account on their system, so put a real one here.

$fromEmail = 'noreply@topweb.se';

if(!$fromEmail){
	// This is only used if you haven't filled an email address in $fromEmail
	$fromEmail = 'noreply@'.$_SERVER['SERVER_NAME'];
}
