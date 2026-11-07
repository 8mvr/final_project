<?php

namespace app\Models;
use app\Database;

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

    public function login($email, $password){
        $sql="SELECT * FROM user where email=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$email]);
        $user=$stmt->fetch();

        if($email==$user["email"] && password_verify($password, $user["password"])){
            return "Login Successful";
        }
        else{
            return "Invalid Credentials";
        }
    }

}

?>