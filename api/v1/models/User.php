<?php

class User {

  private $connection;
  private $tbl_name;

  public function __construct($connection){
    $this->connection = $connection;
    $this->tbl_name = "users";
  }

  public function create_user($name,$email,$password){
    $query = "INSERT INTO $this->tbl_name(name,email,password) VALUES(?,?,?)";
    $stmt = $this->connection->prepare($query);

    $name = htmlspecialchars(strip_tags($name));
    $email = htmlspecialchars(strip_tags($email));
    $password = htmlspecialchars(strip_tags(password_hash($password,PASSWORD_DEFAULT)));
    
    $stmt->bind_param("sss",$name,$email,$password);
    return $stmt->execute() ? true : false;
  }
}
