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
        $sql="SELECT * FROM pet";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        $pet=$stmt->fetchAll(PDO::FETCH_ASSOC);
        return $pet;
    }
    public function insert($created_by, $name, $species, $breed, $age, $sex, $description, $photo_url, $status){
        $sql="INSERT INTO pet(created_by,name,species,breed,age,sex,description,photo_url,status) SELECT userid,?,?,?,?,?,?,?,? FROM user WHERE email=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$name,$species,$breed,$age,$sex,$description,$photo_url,$status,$created_by]);
        if($stmt->rowCount()===0){
            return "Creator account not found";
        }
        return "Pet Added Successful";
    }
    public function ViewbyID($id){
        $sql="SELECT * FROM pet WHERE petid=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$id]);
        $pet=$stmt->fetch(PDO::FETCH_ASSOC);
        return $pet;
    }
    public function update($name, $species, $breed, $age, $sex, $description, $photo_url, $status, $id){
        $sql="UPDATE pet SET name=?,species=?,breed=?,age=?,sex=?,description=?,photo_url=?,status=? WHERE petid=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$name,$species,$breed,$age,$sex,$description,$photo_url,$status,$id]);
        return "Updated Successfully";
    }
    public function delete($id){
        $sql="DELETE FROM pet WHERE petid=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$id]);
        return "Deleted Successfully";
    }
}

?>