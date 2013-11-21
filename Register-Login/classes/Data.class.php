<?php

class Data {

    protected $data;

    public function __construct( $receivedData ) {
        $this->data = $receivedData;
    }

    public function get( $key ) {
        if ( $this->has( $key ) ) {
            $out = $this->data[$key];
        } else {
            $out = false;
        }
        return $out;
    }

    public function has( $key ) {
        return isset( $this->data[$key] );
    }

}
