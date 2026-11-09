<?php

namespace app\Models;
use app\Database;
use PDO;

class Pet{
    private $conn;

    public function __construct(){
        $database=new Database();
        $this->conn=$database->connection();
    }
    public function getAll(){
        $sql="SELECT pet.*, user.full_name AS creator_name FROM pet INNER JOIN user ON pet.created_by = user.userid";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        $pet=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $pet;
    }
    public function insert($creatorEmail, $name, $species, $breed, $age, $sex, $description, $photo_url, $status){
        $sql="INSERT INTO pet(created_by,name,species,breed,age,sex,description,photo_url,status) SELECT userid,?,?,?,?,?,?,?,? FROM user WHERE email=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$name,$species,$breed,$age,$sex,$description,$photo_url,$status,$creatorEmail]);
        if($stmt->rowCount()===0){
            return "Creator account not found";
        }
        return "Pet Added Successful";
    }
}

?>