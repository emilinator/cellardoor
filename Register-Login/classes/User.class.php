<?php

include_once "classes/Data.class.php";
include_once "classes/Session.class.php";

class User extends Data {
    private $session;

   
    public function __construct( $receivedData = null ) {
        parent::__construct( $receivedData );
        $this->session = new Session();
    }

    public function isLoggedIn() {
        return $this->session->get("logged-in");
    }

    public function getName() {
        return $this->session->get( "name" );
    }

    public function logout() {
        $this->session->destroy();
    }

    public function login( $name ) {
        $this->session->set( "name", $name );
        $this->session->set( "logged-in", true );
        $this->session->set( "login-error", "" );
    }

    public function setLoginError( $msg ) {
        $this->session->set( "login-error", $msg );
    }

    public function getLoginError() {
        return $this->session->get( "login-error" );
    }

    
}
