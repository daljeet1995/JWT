<?php
    require_once('constant.php');

  CLASS Rest{
        protected $request;
        protected $serviceName;
        protected $param;

    public function __construct(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $this->throwError(REQUEST_METHOD_NOT_VALID,'Method is not post.');
        }
      $handler = fopen('php://input','r');
      $this->request = stream_get_contents($handler);
      $this->validateRequest();
    }
    //  function one open

    public function validateRequest(){
      if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
          $this->throwError(REQUEST_CONTENTTYPE_NOT_VALID,'Request content type is not valid'); 
      }

       $data = json_decode($this->request, true);
       
       if(!isset($data['name']) || $data['name'] == ""){
           $this->throwError(API_NAME_REQUIRED, "API name is required.");
       }
        $this->serviceName = $data['name'];

        if(!is_array($data['param'])){
            $this->throwError(API_NAME_REQUIRED, "API PARAM is required.");
        }
         $this->param = $data['param'];
    }
      
    // function one close


    // function two open
    public function processApi(){
        $api = new API;
        $rMethod = new reflectionMethod('API', $this->serviceName); 
        if(!method_exists($api, $this->serviceName)){
            $this->throwError(API_DOES_NOT_EXIST, "Api Does not exist");
        }
        $rMethod->invoke($api);
    }
    // function two close

    // function three open
    public function validateParameter($fieldName, $value, $dataType, $required = true){
        if($required == true && empty($value) == true){
            $this->throwError(VALIDATE_PARAMETER_REQUIRED, $fieldName . "Parameter is Required.");
        }
        switch ($dataType){
            case BOOLEAN:
              if(is_bool($value)){
                  $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName. '.It should be boolean.');
              }
              break;

              case INTEGER:
              if(!is_numeric($value)){
                  $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '.It should be Numeric.');
              }
              break;

              case STRING:
              if(!is_string($value)){
                  $this->throwError(VALIDATE_PARAMETER_DATATYPE, "Datatype is not valid for " . $fieldName . '.It should be string.');
              }
              break;

              default:
                //    code
            break;
        }
         return $value;

    }
    // function three close
    
    // function four open
    public function throwError($code, $message){
      header("content-type: application/json");  
      $errorMsg =  json_encode(['error' => [ 'status'=> $code, 'message'=> $message ]]);
       echo $errorMsg;exit;
    }
     // function four close

      // function five open
    public function returnResponse(){
        
    } 
    // function five close

  }


?>