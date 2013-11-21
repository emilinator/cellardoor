<?php
include_once "classes/User.class.php";
include_once "classes/User_Table.class.php";


function login( PDO $db, Request $request ) {
    $user = new User();
    $action = $request->get( "action" );
    if ( $action ) {
        if ( $action === "logging-in" ) {
            $user = loggingIn( $db, $request, $user );
        } else if ( $action === "logging-out" ) {
            $user->logout();
        }
        header( "Location:index.php?page=forum" );
    }

    if ( $user->isLoggedIn() ) {
        $out = showLogoutForm( $user->getName() );
    } else {
        $out = showLoginForm( $user->getLoginError() );
    }
    return $out;
}


//declare new function
function loggingIn( PDO $db, Request $request, User $user ) {
    $userTable = new User_Table( $db );
    $email = $request->get( "email" );
    $pw = $request->get( "password" );
    $userLoggedIn = $userTable->loginUser( $email, $pw );
    if ( $userLoggedIn ) {
        $user->login( $userLoggedIn );
    } else {
        $user->setLoginError( "Wrong email or password - try again" );
    }
    return $user;
}


function showLoginForm( $errorMsg = "" ) {
    $out = '<form method="post" action="index.php?page=forum&amp;action=logging-in"> 
            <label>e-mail</label>
            <input type="email" name="email" required />  
            <label>password</label>
            <input type="password" name="password" required/> 
            <input type="submit" name="login-attempt" value="login" />
            <p class="error-message">' . $errorMsg . '</p>
        </form>';
    return $out;
}


//declare new function
function showLogoutForm( $username ) {
    $out = '<form method="post" action="index.php?page=forum&amp;action=logging-out">
            <p>Logged in as '. $username .'</p><input type="submit" value="log out" />
        </form>';
    return $out;
}