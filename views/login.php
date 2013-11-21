<?php

include_once "classes/user.class.php";

function login(Database $db, Session $session){
	$formData = new Request("post");
	$formSubmitted = $formData->get("email");
	if ($formSubmitted){
		tryLogin($db, $session, $formData);
	} else {
		$out = showLogin("login");
	}
	return $out;
} 

function tryLogin(Database $db, Session $session, Request $formData){
	$u = $formData->get("email");
	$pw =$formData->get("password");

	$user =new User ($db);
	if ($user->isUserValid($u, $pw)){
		$name = $user->name();
		$session->add("logged_in", true)->add("name", $name);
	}
	header("Location:admin.php");
}


function showLogin($message=""){
	$out = html("section")->attr("id", "login");
	$form = form("post", "admin.php")->append(
		html("h2", $message),
		label("e-mail"),
		input()->name("email"),
		label("password"),
		password()->name("password"),
		submitBtn("login")
	);
	$out->append($form);
	return $out;
}

function loggedIn (Session $session){
	return $session->get("logged_in");
}
