<?php
	include_once "libs/magic-1.5.2.php";
	include_once "libs/autoloader.php";
	//I have created my user table in my blog database
	//you should connect to whichever database you have used
	$db = new Database( "2nd_sem_video" );
	$db->user("root")->password("")->connect();
	$goodUser = new User( $db );
	if ( $goodUser->isUserValid( "no@where.net", "test") ){
		echo "<br>Welcome " . $goodUser->name();
	} else {
		echo "<br>e-mail and password do not match a record in the system! ";
	}

	// $badUser = new User( $db );
	// if ( $badUser->isUserValid("no@where.net", "wrong password") ){
	// 	echo "<br>Welcome " . $badUser->name();
	// } else {
	// 	echo "<br>e-mail and password do not match a record in the system! ";
	// }