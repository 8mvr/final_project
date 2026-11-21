<?php

namespace app\Models;

use app\Database;
use PDO;

class Authentication{
    private $conn;

    public function __construct(){
        $database = new Database();
        $this->conn=$database->connection();
    }

    public function signup($full_name, $email, $password){
        $hashPassword=password_hash($password, PASSWORD_DEFAULT);
        $sql="INSERT INTO user(full_name, email, password, role) VALUES(?,?,?,?)";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$full_name, $email, $hashPassword, "user"]);

        return "Success";
    }

    public function findByEmail($email){
        $sql="SELECT userid, full_name, email, role, created_at FROM user WHERE email=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function login($email, $password){
        $sql="SELECT * FROM user where email=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$email]);
        $user=$stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user["password"])){
            return $user;   // return the full user row (includes role)
        }
        return false;
    }

}

?>