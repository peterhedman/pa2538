<?php
	require_once 'main.php';
	$user = new User();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//SV" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sv" lang="sv">
    <head>
        <title><?php echo $page_title; ?></title>
        <meta http-equiv="description" content="<?php echo $page_description; ?>" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        
        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="assets/js/script.js"></script>
        
        <!-- Costum Google Fonts -->
		<link href="https://fonts.googleapis.com/css?family=ABeeZee" rel="stylesheet">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        
        <!-- Icon Library -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

        <!-- The main CSS file -->
        <link href="assets/css/style.css" rel="stylesheet" />

        <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <meta charset="utf-8"/>
        <title>JoinTheRun</title>
        
    </head>
    
    <body>
    
    <div id="wrapper">
        <div id="header">
        <h1><div id="toppen">Do You</div>Wanna Join?</h1>
        <!--<img src="site_logo.png" height="100" width="150" alt="site name" />-->
        </div>
        
        <nav id="nav" role="navigation">
        <a href="#nav" title="Show navigation">Show navigation</a>
        <a href="#" title="Hide navigation">Hide navigation</a>
        <ul>
            <li><a href="/">Home</a></li>
            <li>
                <a href="/" aria-haspopup="true">Run Paths</a>
                <ul>
                    <li><a href="/">Kungsmarken</a></li>
                    <li><a href="/">Galgamarken</a></li>
                    <li><a href="/">Trossö</a></li>
                    <li><a href="/">Saltö</a></li>
                </ul>
            </li>
            <li><a href="/">Schema</a></li>
           
            <?php
            if($user->loggedIn()){
				echo '<li><a href="protected-addrequest.php">Add Run Request</a></li>';
				echo '<li><a href="protected-setup.php">Setup</a></li>';
				echo '<li><a href="login-form.php?logout=1" class="logout-button">Logout</a></li>';
			} else {
			 	echo '<li><a href="login.php">login</a></li>';
			}
			?>
            
            <li><a href="/">Contact</a></li>
        </ul>
    </nav>