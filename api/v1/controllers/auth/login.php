<?php
ini_set("display_errors",1);
require "./../../../../vendor/autoload.php";
include_once("./../../config/config.php");
include_once("./../../../../src/classes/Controller.php");

use \Firebase\JWT\JWT;


(new Controller(config('allow_cors')))->public_controller('GET',function($body){
  include_once("./../../../../src/modules/helpers/Validator.php");
  include_once("./../../../../src/classes/Database.php");
  include_once("./../../models/User.php");
  include_once("./../../../../src/modules/helpers/TokenAttributes.php");
  include_once("./../../../../src/modules/helpers/Serializer.php");
  include_once("./../../../../src/modules/helpers/Response.php");

  $response = new Response();
  $validator = new Validator();

  $email = $_SERVER['PHP_AUTH_USER'];
  $password = $_SERVER['PHP_AUTH_PW'];

  $user = new User((new Database(config('host'),config('username'),config('password'),config('database_name')))->connect());
  $active_user =  (new Serializer(['id','name','password','email','created_at','updated_at']))->tuple($user->get_user($email));

  if($active_user){
    if(password_verify($password,$active_user['password'])){
      $token_attr = new TokenAttributes($active_user,array('refresh_token_exp_time' => 0));
      $access_token = JWT::encode($token_attr->access_token_payload(),config('secret_key'),config('hash'));
      $refresh_token = JWT::encode($token_attr->refresh_token_payload(),config('secret_key'),config('hash'));

      $response->send_response(200,[["error",false],["data",array(
        'user' => $active_user,
        'tokens' => array(
          'access_token' => $access_token,
          'refresh_token' => $refresh_token,
        ),
      ),]]);
    }else{
      $response->send_response(400,[['error',true],['message',"invalid credentials"]]);
    }
  }else {
    $response->send_response(400,[['error',true],['message',"invalid credentials"]]);
  }
});
