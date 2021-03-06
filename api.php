<?php

  class Api extends Rest{
    public $dbConn;  
    public function __construct(){
        parent:: __construct();
        $db = new DbConnect;
        $this->dbConn = $db->connect();

    }
    public function generateToken(){
      // print_r($this->param); 
      $email = $this->validateParameter('email', $this->param['email'], STRING);
      $pass = $this->validateParameter('pass', $this->param['pass'], STRING);

      $stmt = $this->dbConn->prepare("select * from users Where email = :email AND password = :pass");
      $stmt->bindParam(":email", $email);
      $stmt->bindParam(":pass", $pass);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      print_r($user);
      if(!is_array($user)){
          $this->returnResponse(INVALID_USER_PASS,"Email or Password is incorrect.");
      }
        if( $user['active'] == 0){
           $this->returnResponse(USER_NOT_ACTIVE, "USER is not activated. Please contact to admin.");
        }

        $payload = [
            'iat' => time(),
            'iss' => 'localhost',
            'exp' =>  time() + (60),
            'userId' => $user['id']
        ]
        $token = JWT::encode($payload, SECRETE_KEY);
        echo $token;
    }

  }

?>