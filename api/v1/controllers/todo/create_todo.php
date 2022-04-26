<?php
ini_set('display_errors',1);//first

require "./../../../../vendor/autoload.php";
include_once("./../../config/config.php");
include_once("./../../../../src/classes/Controller.php");

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

(new Controller(config('allow_cors')))->acccess_token_controller("POST",function($jwt) {
  return JWT::decode($jwt,new Key(config('secret_key'),config('hash')));
},function($payload,$body){
  include_once("./../../../../src/modules/helpers/Validator.php");
  include_once("./../../../../src/classes/Database.php");
  include_once("./../../models/Todo.php");
  include_once("./../../../../src/modules/helpers/Response.php");
  include_once("./../../../../src/modules/helpers/Serializer.php");

  $response = new Response();
  (new Validator())->validate_body($body,['title']);
  $todo = new Todo((new Database(config('host'),config('username'),config('password'),config('database_name')))->connect());

  if($todo->create_todo($body->title,$payload->data->id)){
    $response->send_response(200,[['error',false],['message',"todo created successfully"]]);
  }else {
    $response->send_response(500,[['error',false],['message',"something went wrong"]]);
  }
});

?>
