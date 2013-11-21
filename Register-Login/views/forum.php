<?php
//new code starts here
include_once "views/login.php";
include_once "classes/User.class.php";

//edit exiting
function forum( PDO $db, Request $request ){
    $user = new User();
    $out = login(  $db, $request );
    $out .= "<section>";
    if( $user->isLoggedIn() ){       
        $out .= showForum( $db, $request );
    }else{
        $out .= showWarning();
    }
    $out .= "</section>";
    return $out;
}

function showForum(){
    return "<p>You are succesfully logged in</p>";
}

function showWarning(){
    return "<p>You must login to see restricted content</p>";
}
