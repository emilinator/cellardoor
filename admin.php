<?php
include_once "libs/magic-1.5.2.php";
include_once "views/login.php";
include_once "libs/formCreator.php";


$db = new Database ("2nd_sem_video");
$db->user("root")->password("")->connect();
$page = new Page();
$page->title("administration module");
$page->css("css/adminHome.css");

$session = new Session();
// $goodUser = new User($db);
// if ($goodUser->isUserValid("no@where.net", "test")){
// 	echo "<br>Welcome" . $goodUser->name();
// }else {
// 	echo "<br>e-mail and password do not match a record in the system!"; 
// }

// $badUser = new User($db);
// if ($badUser->isUserValid("no@where.net", "test")){
// 	echo "<br>Welcome" . $badUser->name();
// } else {
// 	echo "<br>e-mail and password do not match a record in the system!"; 
// };

if(loggedIn($session)){
	$request = new Request();

	$view = $request->get("adminPage") or $view = "adminVideos";
	include_once "views/admin/$view.php";
	$page->body ($view($db, $request) );
} else {
	$page->body(login($db, $session) );
}


echo $page->asHTML();