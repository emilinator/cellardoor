<?php


include_once "libs/magic-1.5.3.php";
include_once "libs/form-creator.php";
include_once "libs/autoloader.php";
include_once "views/Userlogin.php"; //loads login to user section -> shows login form

$db = new Database ( "pete604p" );
$db ->user( "root" ) ->password( "" )->connect();


$session = new Session();
$page = new Page();


$page->title( "Your Journey Starts Here" );
$page->body( Userlogin($db, $session) );
	if( UserloggedIn($session) ){
		$page->body( "<h1>Welcome to the User area</h1>" );
	} else {
		die("access denied - you must log-in to access the User Content");
	}

	$page->css("styles/userDesign.css");


$request = new Request();

$view = $request->get ( "page" ) or $view = "userHome";
include_once "views/User/$view.php";
$page->body ( "<div id= \"content_wrapper\">" . $view($db, $request) . "</div>" );


echo $page->asHTML();