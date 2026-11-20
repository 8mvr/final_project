<?php

namespace app;

use PDO;

class Database{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "petadoptiondb";

    public function connection():PDO{
        return new PDO("mysql:host=$this->host;dbname=$this->dbname",$this->username,$this->password,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
        ]);
    }
}

?>