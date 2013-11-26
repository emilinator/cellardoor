<?php

include_once "libs/magic-1.5.3.php"; //magic library
include_once "libs/form-creator.php"; //Loads form class
include_once "libs/autoloader.php"; //automatically loads classes
include_once "views/login.php"; //loads login to admin section -> shows login form
include_once "views/Userlogin.php"; //loads login to user section -> shows login form


######################################################################
############### CONNECT TO A DATEBASE ################################
######################################################################

$db = new Database ( "pete604p.keaweb.dk" );
$db ->user( "pete604p" ) ->password( "cgcgce1010" )->connect();
######################################################################

$page = new Page(); //uses Magic php to define the page variable
$session = new Session(); //uses magic php to define a session variable

$page->title( "XI " ); // page title
$page->css("styles/style.css"); // adds a style sheet to page
$page->body( login($db, $session) ); 
	
$page->body( Userlogin($db, $session) ); 
	


############################################################################
############################ Page attributes ###############################
############################################################################

$background= "<img id = \"background\" src = \"images/background_bs_cards1.jpg\" alt = \"background\" />"; //loads the image as a variable
$page->body($background); // appends the image to the body

$key_copy= "<img id = \"key\" src = \"images/icons/key_copy.png\" alt = \"key\" />"; //loads the image as a variable
$page->body($key_copy); // appends the image to the body

$about= "<img id = \"about\" src = \"images/icons/about.png\" alt = \"about\" />"; //loads the image as a variable
$page->body($about); // appends the image to the body

$aboutDiv= "<div id = \"aboutDiv\"> <h1>about</h1> <p>Maecenas sed jnsdkn diam eget risus varius blandit sit amet non magna. Maecenas faucibus mollis interdum. Nulla vitae elit libero, a pharetra augue. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Aenean lacinia bibendum nulla sed consectetur.</p>

<p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Aenean lacinia bibendum nulla sed consectetur. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Donec id elit non mi porta gravida at eget metus.</p>

<p>Donec id elit non mi porta gravida at eget metus. Etiam porta sem malesuada magna mollis euismod. Curabitur blandit tempus porttitor. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas sed diam eget risus varius blandit sit amet non magna.</p> </div>"; //loads the image as a variable
$page->body($aboutDiv); // appends the div to the body

############################################################################
############################ FOOTER ########################################
############################################################################

$loginButton = "<div id = \"loginButton\">Admin</div>"; //creates the div for admin login button
$page->body($loginButton); // appends the login div to the page

$hidelogin = "<div id = \"hideLoginButton\">Hide</div>"; //as above
$page->body($hidelogin); // as above

$footer= "<div id= \"footer\">Design by CellarDoor © 2013</div>"; // creates the div that is the footer
$page->body($footer); // appends footer to screen

############################################################################


$page->addScript( "libs/jquery-1.9.1.min.js") // adds jQuery library to head of HTML document
      ->addScript("libs/jquery-ui-1.10.3.custom.js") // adds jQuery UI library to head of HTML document
      ->addScript("libs/script.js"); // add OUR jQuery script to the head of the document

echo $page->asHTML();