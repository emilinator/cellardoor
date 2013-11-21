<?php
include_once "classes/Data.class.php";

class Request extends Data {

    public function __construct(){
        parent::__construct( $_REQUEST );
    }
    
    public function getUrl(){
        $protocol = "http://";
        if ( !empty( $_SERVER['HTTPS'] ) ) {
            $protocol = "https://";
        }
        $url = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        return $url;
    }
}
