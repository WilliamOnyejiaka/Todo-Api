<?php
ini_set("display_errors",1);

class Controller
{

  private $allow_cors;

  public function __construct($allow_cors)
  {
    $this->allow_cors = $allow_cors;
  }

  public static function get_jwt($token)
  {
    $check_token = preg_match('/Bearer\s(\S+)/', $token, $matches);
    return $check_token == 0 ? false : $matches[1];
  }

  private function activate_cors(){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header("Access-Control-Allow-Credentials: true");
    // header('Content-Type: application/json');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "OPTIONS") {
      header('Access-Control-Allow-Origin: *');
      header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
      header("HTTP/1.1 200 OK");
      die();
    }
  }

  public function protected_controller($request_method, $func)
  {
    ($this->allow_cors && $this->activate_cors());
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] == $request_method) {

      $token = isset((getallheaders())['Authorization']) ? (getallheaders())['Authorization'] : false;
      $body = json_decode(file_get_contents("php://input"));

      if ($token) {

        $jwt = Controller::get_jwt($token);

        if ($jwt) {
          $func($jwt, $body);
        } else {
          http_response_code(400);
          echo json_encode(array(
            'error' => true,
            'message' => "invalid jwt"
          ));
        }
      } else {
        http_response_code(401);
        echo json_encode(array(
          'error' => true,
          'message' => "Authorization header missing"
        ));
      }
    } else {
      http_response_code(405);
      echo json_encode(array(
        'error' => true,
        'message' => "Access Denied"
      ));
    }
  }

  public function public_controller($request_method, $func)
  {
    ($this->allow_cors && $this->activate_cors());
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] == $request_method) {

      $body = json_decode(file_get_contents("php://input"));

      $func($body);
    } else {
      http_response_code(405);
      echo json_encode(array(
        'error' => true,
        'message' => "Access Denied"
      ));
    }
  }

  public function acccess_token_controller($request_method,$decode_jwt, $func)
  {

    ($this->allow_cors && $this->activate_cors());
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] == $request_method) {

      $token = isset((getallheaders())['Authorization']) ? (getallheaders())['Authorization'] : false;
      $body = json_decode(file_get_contents("php://input"));

      if ($token) {

        $jwt = Controller::get_jwt($token);

        if ($jwt) {
          $payload = null;

          try{
            $payload = $decode_jwt($jwt);

          }catch(\Firebase\JWT\ExpiredException $ex){
            http_response_code(400);
            echo json_encode(array(
              'error' => true,
              'message' => $ex->getMessage()
            ));
            exit();
          }

          if($payload->aud == "users"){
            $func($payload,$body);
          }else {
            http_response_code(400);
            echo json_encode(array(
              'error' => true,
              'message' => "access token needed",
            ));
          }
        } else {
          http_response_code(400);
          echo json_encode(array(
            'error' => true,
            'message' => "invalid jwt"
          ));
        }
      } else {
        http_response_code(401);
        echo json_encode(array(
          'error' => true,
          'message' => "Authorization header missing"
        ));
      }
    } else {
      http_response_code(405);
      echo json_encode(array(
        'error' => true,
        'message' => "Access Denied"
      ));
    }
  }
}
