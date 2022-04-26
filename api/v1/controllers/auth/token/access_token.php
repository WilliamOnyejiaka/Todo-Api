<?php
ini_set("display_errors",1);

require "./../../../../../vendor/autoload.php";
include_once("./../../../config/config.php");
include_once("./../../../../../src/classes/Controller.php");

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

(new Controller(config('allow_cors')))->protected_controller('GET',function($jwt,$body){
  include_once("./../../../../../src/modules/helpers/Validator.php");
  include_once("./../../../../../src/modules/helpers/TokenAttributes.php");
  include_once("./../../../../../src/modules/helpers/Response.php");


  $validator = new Validator();
  $response = new Response();
  $payload = null;

  try {
    $payload = (JWT::decode($jwt,new Key(config('secret_key'),config('hash'))));
  }catch(\Firebase\JWT\ExpiredException $ex){
    $response->send_response(400,[["error",true],["message",$ex->getMessage()]]);
    exit();
  }

  if($payload->aud == "users"){
    $response->send_response(400,[["error",true],["message","refresh token needed"]]);

  }else {
    $active_user = array('id' => $payload->data->id);
    $access_token = JWT::encode((new TokenAttributes($active_user))->access_token_payload(),config('secret_key'),config('hash'));
    $response->send_response(200,[["error",false],["access_token",$access_token]]);

  }


});
