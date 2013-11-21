<?php

class Table{
    protected $db;
    
    public function __construct(PDO $db) {
        $this->db=$db;
    }
    
}
