<?php
ini_set("display_errors",1);

require "./../../../../vendor/autoload.php";
include_once("./../../config/config.php");
include_once("./../../../../src/classes/Controller.php");


(new Controller(config('allow_cors')))->public_controller('POST',function($body){
  include_once("./../../../../src/modules/helpers/Validator.php");
  include_once("./../../../../src/classes/Database.php");
  include_once("./../../models/User.php");
  include_once("./../../../../src/modules/helpers/Response.php");
  include_once("./../../../../src/modules/helpers/Serializer.php");

  $response = new Response();
  $validator = new Validator();

  $validator->validate_body($body,['name','email','password']);
  $validator->validate_email_with_response($body->email);
  $validator->validate_password_with_response($body->password,5);

  $user = new User((new Database(config('host'),config('username'),config('password'),config('database_name')))->connect());
  $user_exits =  (new Serializer(['email']))->tuple($user->get_user($body->email));

  if(!$user_exits){
    if($user->create_user($body->name,$body->email,$body->password)){
      $response->send_response(200,[['error',false],['message',"user created successfully"]]);
    }else{
      $response->send_response(500,[['error',false],['message',"something went wrong"]]);
    }
  }else {
    $response->send_response(400,[['error',false],['message',"email exits"]]);
  }
});
