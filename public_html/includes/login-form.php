<?php
// config.php is included on the "include page"

//prevent users to visit this page if it's not included
if(!defined('login')) {
    die('Direct access not permitted');
}

//process login form if submitted
if(isset($_POST['submit'])){

	$username = $_POST['username'];
	$password = $_POST['password'];
	
	if($user->login($username,$password)){ 
		$_SESSION['username'] = $username;
		header('Location: memberpage.php');
		exit;
	
	} else {
		$error[] = 'Wrong username or password or your account has not been activated.';
	}

}//end if submit


function login_actions($action){
	if(isset($action)){
	
		//check the action
		switch ($action) {
			case 'active':
				echo "<h2 class='bg-success'>Your account is now active you may now log in.</h2>";
				break;
			case 'reset':
				echo "<h2 class='bg-success'>Please check your inbox for a reset link.</h2>";
				break;
			case 'resetAccount':
				echo "<h2 class='bg-success'>Password changed, you may now login.</h2>";
				break;
		}
	
	}
}

?>

	<div class="row">

	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<form role="form" method="post" action="" autocomplete="off">
				<h2>Please Login</h2>
				<!--<p><a href='./'>Back to home page</a></p> -->
				<hr>

				<?php
				//check for any errors
				if(isset($error)){
					foreach($error as $error){
						echo '<p class="bg-danger">'.$error.'</p>';
					}
				}

				login_actions($_GET['action']);

				
				?>

				<div class="form-group">
					<input type="text" name="username" id="username" class="form-control input-lg" placeholder="User Name / Email" value="<?php if(isset($error)){ echo $_POST['username']; } ?>" tabindex="1">
				</div>

				<div class="form-group">
					<input type="password" name="password" id="password" class="form-control input-lg" placeholder="Password" tabindex="3">
				</div>
				
				<div class="row">
					<div class="col-xs-9 col-sm-9 col-md-9">
						 <a href='reset.php'>Forgot your Password?</a>
					</div>
				</div>
				
				<hr>
				<div class="row">
					<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Login" class="btn btn-primary btn-block btn-lg" tabindex="5"></div>
				</div>
			</form>
		</div>
	</div>