<?php

require_once 'includes/main.php';


/*--------------------------------------------------
	Handle visits with a login token. If it is
	valid, log the person in.
---------------------------------------------------*/


if(isset($_GET['tkn'])){
	
	$tkn = $db -> quote($_GET['tkn']);
	
	// Is this a valid login token?
	$user = findByToken($tkn);
	
	if($user){
		
		// Adds the ip of loggin session to db
		$user->create_logged_in_ip_log($_SERVER['REMOTE_ADDR']);
		
		// Yes! Login the user and redirect to the protected page.

		$user->login();
		
		redirect('protected.php');
			
		
	} else {
		// Invalid token. Redirect back to the login form.
		
		redirect('index.php');
		
	}

	
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
		
		$email_input = $db -> quote($_POST['email']);
		
		rate_limit($_SERVER['REMOTE_ADDR']);

		// Record this login attempt
		rate_limit_tick($_SERVER['REMOTE_ADDR'], $email_input);
		
		check_if_current_user($_SERVER['REMOTE_ADDR'], $email_input);
		
		//error_log("indexUser: " . json_encode($user));	
		// Send the message to the user

		$message = '';
		$email = $_POST['email'];
		$subject = 'Your Login Link';
		
		//error_log("excists: " . exists($email_input));
		
		if(!exists($email_input)){
			$subject = "Thank You For Registering!";
			$message = "Thank you for registering at our site!\n\n";
		}
		
		// Attempt to login or register the person
		$user = loginOrRegister($email_input);
		$token = $user->getToken();
		
		//error_log("user: " . json_encode($user));	

		$message.= "You can login from this URL:\n";
		$message.= get_page_url()."?tkn=".$token."\n\n";

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


<form id="login-register" method="post" action="login-form.php">

    <h1>Login or Register</h1>

    <input type="text" placeholder="your@email.com" name="email" autofocus />
    <p>Enter your email address above and we will send <br />you a login link.<br />Or if you are a recognized user then you will log in.</p>

    <button type="submit">Login / Register</button>

    <span></span>

</form>
        
		