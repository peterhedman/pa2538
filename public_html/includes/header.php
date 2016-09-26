<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="utf-8">
    <title><?php if(isset($title)){ echo $title; }?></title>
   
   
    
    <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC2P69gjw6Wg4GxD8z-VKvWgx5b78rucf0&signed_in=true&callback=initMap"></script> -->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC2P69gjw6Wg4GxD8z-VKvWgx5b78rucf0"></script>

		<script src="js/script.js"></script>
        <script src="js/google-maps-add-training.js"></script>
        <script src="js/google-maps-join-training.js"></script>
        <script src="js/jquery.datetimepicker.full.min.js"></script>
        
        
        <!-- Costum Google Fonts -->
		<link href="https://fonts.googleapis.com/css?family=ABeeZee" rel="stylesheet">
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        
        <!-- Icon Library -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

        <!-- The main CSS file -->
        <link href="css/style.css" rel="stylesheet">
        <link href="css/jquery.datetimepicker.min.css" rel="stylesheet">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">

        <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <meta charset="utf-8"/>
    
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
            <li <?php if ($title=="Home") echo " id=\"currentpage\""; ?>><a href="/">Home</a></li>
           
            <?php /*?><li <?php if ($title=="Run Paths") echo " id=\"currentpage\""; ?>><a href="/" aria-haspopup="true">Run Paths</a>
                <ul>
                    <li><a href="/">Kungsmarken</a></li>
                    <li><a href="/">Galgamarken</a></li>
                    <li><a href="/">Trossö</a></li>
                    <li><a href="/">Saltö</a></li>
                </ul>
            </li><?php */?>
            
            <li <?php if ($thisPage=="Training Sessions") echo " id=\"currentpage\""; ?>><a href="training-sessions.php">Training Sessions</a></li>
           
            <?php
            if($user->is_logged_in()){?>
            	<li <?php if ($thisPage=="Add New Training") echo " id=\"currentpage\""; ?>><a href="training-add-session.php">Add New Training</a></li>
                <li <?php if ($thisPage=="Training History") echo " id=\"currentpage\""; ?>><a href="training-history.php">Training History</a></li>
				<li <?php if ($title=="Members Page") echo " id=\"currentpage\""; ?>><a href="memberpage.php">Members Page</a></li>
				<li <?php if ($title=="Logout") echo " id=\"currentpage\""; ?>><a href="logout.php">Logout</a></li>
			<?php } else { ?>
			 	<li <?php if ($title=="Login") echo " id=\"currentpage\""; ?>><a href="login.php">Login</a></li>
			<?php } ?>
            
            <li <?php if ($title=="Contact") echo " id=\"currentpage\""; ?>><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

