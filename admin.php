<?php


include_once "libs/magic-1.5.3.php";
include_once "libs/form-creator.php";
include_once "libs/autoloader.php";
include_once "views/login.php";

$db = new Database ( "pete604p" );
$db ->user( "root" ) ->password( "" )->connect();





$session = new Session();
$page = new Page();


$page->title( "Rowntrees Sour Pastilles" );
$page->body( login($db, $session) );
	if( loggedIn($session) ){
		$page->body( "<h1>Welcome to the restricted area</h1>" );
	} else {
		die("access denied - you must log-in to access the admin module");
	}

	$page->css("styles/adminDesign.css");


$request = new Request();

$view = $request->get ( "page" ) or $view = "adminHome";
include_once "views/admin/$view.php";
$page->body ( "<div id= \"content_wrapper\">" . $view($db, $request) . "</div>" );


echo $page->asHTML();