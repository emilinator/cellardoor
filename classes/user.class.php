<?php

class User{
	
	function __construct(Database $db){
		$this->db = $db;
	}
 
	function isUserValid($username, $password){
		$result = $this->getUser("$username", $password);
	if ($result->count() == 1){
		$userIsValid = true;
		$row = $result->getAt(0);
		$this->name = $row->get("name");
	} else {
		$userIsValid = false;
	}
	return $userIsValid;
	
}

	private function getUser($username, $password){
		$user = $this->db->escapeString($username);
		$pw = $this->db->escapeString ($password);
		$sql = "SELECT email, name, password FROM user
				WHERE email = '$user' and password = SHA1('$pw')";
		return $this->db->getData($sql);
	}

	function name(){
		return $this->name;
	}

}