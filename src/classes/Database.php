<?php
class Database {

  private $hostname;
  private $db_name;
  private $username;
  private $password;

  public function __construct($hostname,$username,$password,$db_name){
    $this->hostname = $hostname;
    $this->username = $username;
    $this->password = $password;
    $this->db_name = $db_name;

  }

  public function connect(){
    $conn = new mysqli($this->hostname,$this->username,$this->password,$this->db_name);
    if($conn->connect_errno){
      print_r($conn->error);
      exit;
    }else {return $conn;}
  }
}
?>