<?php

function Userlogin( Database $db, Session $session ) {
	$formData = new Request( "post" );
	$formSubmitted = $formData->get( "Userlogin-submitted" );
//first check: is user trying to login through form
	if ( $formSubmitted ) {
		tryUserLogin( $db, $session, $formData );

//second check: is user trying to log out through form
	} else if ( $formData->has("logout") ){
		Userlogout( $session );

//third check: is user logged in
	} else if ( $session->get( "logged_in" ) ) {
		$out = showUserLogout( $session );

//if not condition is met, show the login form
	} else {
		$out = showUserLogin( "Your Journey Starts Here" );
	}
	return $out;
}




function showUserLogin( $message ) {
	$out = html( "section" )->attr( "id", "Userlogin" );
	$form = form("post", "index.php")->append(
	html( "h2", $message ),
	label( "e-mail" ),
	input()->name( "e-mail" ),
	label( "password" ),
	password()->name( "password" ),
	submitBtn( "login" )->name("Userlogin-submitted")
	);
		$out->append( $form );
		return $out;
}

function tryUserLogin( Database $db, Session $session, Request $formData ) {
	$u = $formData->get( "e-mail" );
	$pw = $formData->get( "password" );
	$name = $formData->get( "name" );
	$privilege = $formData->get( "privlidge" );
	$user = new User( $db );
	if ( $user->isUserValid( $u, $pw, $name, $privilege ) ) {
		$name = $user->name();



		$session->add( "logged_in", true )->add( "name", $name );

			//if ($privilege === "admin") // add if statement - if user has admin privlidges, go to admin, if user has userpriv, go to user.
			header( "Location:UserIndex.php" );
		} else {
			header("Location:index.php");
		}

}


function UserloggedIn( Session $session ){
	return $session->get( "logged_in" );
}

function showUserLogout( Session $session ){
//notice that "name" is retrieved from session
	$name = $session->get("name");
	$out = html( "section" )->attr( "id", "login" );
	$form = form("post", "admin.php")->append(
		html( "h2", "Logged in as $name" ),
		submitBtn("log out")->name("logout")
		);
	$out->append( $form );
	return $out;
}

function Userlogout( Session $session ){
	$session->clear();
	header( "Location: index.php" );
}

