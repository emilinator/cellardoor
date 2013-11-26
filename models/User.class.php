<?php

class User {
	function __construct( Database $db ) {
		$this->db = $db;
	}

	function isUserValid( $username, $password, $name, $privilege ) {
		$result = $this->getUser( $username, $password, $name, $privilege );
			if ( $result->count() == 1 ) {
				$userIsValid = true;
				$row = $result->getAt( 0 );
				$this->name = $row->get( "name" );
			} else {
				$userIsValid = false;
			}
				return $userIsValid;
	}		

	private function getUser( $username, $password, $name, $privilege ){
		$user = $this->db->escapeString( $username );
		$pw = $this->db->escapeString( $password );
		$name = $this->db->escapeString( $name );
		$privilege = $this->db->escapeString( $privilege );
		$sql = "SELECT email, password, name, privilege FROM user
				WHERE email = '$user' and password = SHA1('$pw') ";
		return $this->db->getData( $sql );
	}

	function name() {
		return $this->name;
	}
}