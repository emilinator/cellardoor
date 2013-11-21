<?php

include_once "classes/Data.class.php";

class Session extends Data {

    public function __construct(){
        @session_start();
        parent::__construct( $_SESSION );
    }

    public function set( $key, $value = null ) {
        $_SESSION[$key] = $value;
        $this->data[$key] = $value;
    }

    public function destroy() {
        session_destroy();
    }
}
