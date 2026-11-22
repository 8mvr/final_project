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
        $check=$this->conn->prepare("SELECT userid FROM user WHERE email=?");
        $check->execute([$email]);
        if($check->fetch()){
            return "Email is already registered";
        }

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

    public function getAllUsers(){
        $sql="SELECT userid, full_name, email, role, created_at FROM user ORDER BY created_at DESC, userid DESC";
        $stmt=$this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($userid){
        $sql="SELECT userid, full_name, email, role, created_at FROM user WHERE userid=?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$userid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($userid, $fullName, $email, $role){
        $allowed=["admin", "user"];
        if(!in_array($role, $allowed, true)){ return false; }

        $check=$this->conn->prepare("SELECT userid FROM user WHERE email=? AND userid<>?");
        $check->execute([$email, $userid]);
        if($check->fetch()){ return false; }

        $sql="UPDATE user SET full_name=?, email=?, role=? WHERE userid=?";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$fullName, $email, $role, $userid]);
    }

    public function deleteUser($userid){
        $tables=["appointment", "placement", "adoption", "foster"];
        foreach($tables as $table){
            $stmt=$this->conn->prepare("DELETE FROM `{$table}` WHERE userid=?");
            $stmt->execute([$userid]);
        }

        $stmt=$this->conn->prepare("DELETE FROM user WHERE userid=?");
        return $stmt->execute([$userid]);
    }

    public function updateRole($userid, $role){
        $allowed=["admin", "user"];
        if(!in_array($role, $allowed, true)){ return false; }

        $sql="UPDATE user SET role=? WHERE userid=?";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$role, $userid]);
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