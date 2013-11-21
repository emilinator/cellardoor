<?php
include_once "classes/Page.class.php";
include_once "classes/Request.class.php";


$page = new Page( "Demo forum" );

$page->css( "styles/layout.css" );
$page->css( "styles/typography.css" );

$page->body( include_once "views/navigation.php" );


$page->body( "<h1>Hello world!</h1>" );
$page->body( "<p>How are you today?</p>" );

$page->addScript( "js/main.js" );
$page->addScript( "js/secondary.js" );

$host = 'localhost';
$dbName = 'php3rdsem';
$dbUser = 'root';
$dbPassword = ''; 
$db = $DBH = new PDO("mysql:host=$host;dbname=$dbName", $dbUser, $dbPassword); 
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$request = new Request();
$view = $request->get("page");
if ($view === false){
    $view = "forum";
}

include_once "views/$view.php";
$page->body( $view( $db, $request ) );


echo $page->asHTML();



