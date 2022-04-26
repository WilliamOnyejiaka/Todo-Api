<?php
declare(strict_types=1);
ini_set('display_errors',1);

class Todo {

  private $connection;
  private $tbl_name;

  public function __construct($connection){
    $this->connection = $connection;
    $this->tbl_name = "todos";
  }

  public function create_todo(string $title,int $user_id) {
    $query = "INSERT INTO $this->tbl_name(title,user_id) VALUES(?,?)";
    $stmt = $this->connection->prepare($query);

    $title = htmlspecialchars(strip_tags($title));

    $stmt->bind_param("si",$title,$user_id);
    return $stmt->execute() ? true : false;
  }
}
?>
