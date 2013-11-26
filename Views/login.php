<?php

function login( Database $db, Session $session ) {
	$formData = new Request( "post" );
	$formSubmitted = $formData->get( "login-submitted" );
//first check: is user trying to login through form
	if ( $formSubmitted ) {
		tryLogin( $db, $session, $formData );

//second check: is user trying to log out through form
	} else if ( $formData->has("logout") ){
		logout( $session );

//third check: is user logged in
	} else if ( $session->get( "logged_in" ) ) {
		$out = showLogout( $session );

//if not condition is met, show the login form
	} else {
		$out = showLogin( "login" );
	}
	return $out;
}




function showLogin( $message ) {
	$out = html( "section" )->attr( "id", "login" );
	$form = form("post", "index.php")->append(
	html( "h2", $message ),
	label( "e-mail" ),
	input()->name( "e-mail" ),
	label( "password" ),
	password()->name( "password" ),
	submitBtn( "login" )->name("login-submitted")
	);
		$out->append( $form );
		return $out;
}

function tryLogin( Database $db, Session $session, Request $formData ) {
	$u = $formData->get( "e-mail" );
	$pw = $formData->get( "password" );
	$user = new User( $db );
	if ( $user->isUserValid( $u, $pw ) ) {
		$name = $user->name();

		$session->add( "logged_in", true )->add( "name", $name );
			header( "Location:admin.php" );
		} else {
			header("Location:index.php");
		}

}


function loggedIn( Session $session ){
	return $session->get( "logged_in" );
}

function showLogout( Session $session ){
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

function logout( Session $session ){
	$session->clear();
	header( "Location: index.php" );
}

