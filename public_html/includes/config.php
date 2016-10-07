<?php
ob_start();
session_start();

//set timezone
date_default_timezone_set('Europe/London');

$db_host = 'pa2538-165476.mysql.binero.se';
$db_name = '165476-pa2538';
$db_user = '165476_bb23761';
$db_pass = 'Svante123456';

$site_url = 'http://pa2538.topweb.se/';
$site_email = 'noreply@topweb.se';


//database credentials
define('DBHOST',$db_host);
define('DBUSER',$db_user);
define('DBPASS',$db_pass);
define('DBNAME',$db_name);

//application address
define('DIR',$site_url);
define('SITEEMAIL',$site_email);

try {

	//create PDO connection
	$db = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
	//show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}

//include the user class, pass in the database connection
include('classes/user.php');
include('classes/phpmailer/mail.php');
include('classes/training.php');
$user = new User($db);

//Gets all the sessions from db
$trainings = $db->query('SELECT * FROM trainingsession')->fetchAll(PDO::FETCH_CLASS, 'Training');
function sortFunction( $a, $b ) {
	return strtotime($a->getDate()) - strtotime($b->getDate());
}
usort($trainings, "sortFunction");

date_default_timezone_set('Europe/Stockholm');

?>
