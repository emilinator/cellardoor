<?php

include_once "libs/magic-1.5.2.php";
include_once "views/dbConnect.php";
include_once "views/search.php";
include_once "libs/autoloader.php";
include_once "libs/formCreator.php"; 


$session = new Session();
$page = new Page();
$page->title ("Your Toob");
$header = html("header");
$header->append(html("img")->attr("src", "img/logo.png")->id("mainlogo"));
$page->body($header);


$page->body(html("div", searchForm())->attr("id", "searchForm"));


$page->css( "css/home.css" );
$page->css( "css/search.css" );
$page->css( "css/contact.css" );

$stuff = html("section")->append("img src='img/logo.png'");

$page->contentHeader("Welcome to");








$contentWrapper = html("section")->attr("id", "contentWrapper");
$page->body(include_once"views/nav.php");
$footer = html("section")->attr("id", "footer");
$request = new Request();
$view = $request->get("page") or $view = "videos";
include_once "views/$view.php";
$contentWrapper->append($view($db,$request));
$page->body($contentWrapper, $footer);


echo $page->asHTML();



//concatenation
$a = "hello";
$a .= " world";
//echo $a;