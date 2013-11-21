<?php

include_once "classes/Table.class.php";

class User_Table extends Table{
    
    public function insert(User $user){
        $sql = "INSERT INTO user (name, email, password) VALUES( :name, :email, SHA1(:pw) )";
        $query = $this->db->prepare( $sql );
        
        $name = $user->get("name");
        $email = $user->get("email");
        $pw = $user->get("password");
        $query->bindParam( ':name', $name );
        $query->bindParam( ':email', $email );
        $query->bindParam( ':pw', $pw );
        
        try {
            $out = $query->execute();
        } catch (Exception $e) {
            $out = false;
        }
        return $out;
        
        
    }
    
    public function loginUser( $email, $password ) {
        $sql = "SELECT * FROM user WHERE email = :email AND password = SHA1(:pw)";
        $query = $this->db->prepare( $sql );
        $query->bindParam( ':email', $email );
        $query->bindParam( ':pw', $password );
        $query->execute();
        $data = $query->fetchObject();
        if ( $data != false ) { 
            $out = $data->name;
        }else{
            $out = false;
        }
        return $out;
    }

    
}