<?php

include_once "classes/User.class.php";
include_once "classes/User_Table.class.php";


function register( PDO $db, Request $request ) {
    $newUserSubmitted = $request->get( "register-user" );
    if ( $newUserSubmitted ) {
        $out = createNewUser( $db, $request );
    } else {
        $out = showRegistrationForm();
    }
    return $out;
}

function showRegistrationForm(){
    $out = "<form method='post' action='index.php?page=register'>
                <fieldset><legend>Create an account</legend>
                    <label>Write a username</label>
                    <input type='text' name='name' />
                    <label>Write your email</label>
                    <input type='email' name='email' />
                    <label>Write a password</label>
                    <input type='password' name='password' />
                    <input type='submit' name='register-user' value='register'/>
                </fieldet>
            </form>";
    return $out;
}


function createNewUser( PDO $db, Request $request ) {
    $userData['name']=$request->get("name");
    $userData['email']=$request->get("email");
    $userData['password']=$request->get("password");
    $user = new User($userData);
    $userTable = new User_Table($db);
    
    if ( $userTable->insert($user) ){
        $out = "<p>New user profile created - login to the forum start using your profile</p>";
    }else{
        $href = "index.php?page=register";
        $out = "<p>Something went wrong, user profile not created. <a href='$href'>Try again</a><p>";
    }
    return $out;
}
