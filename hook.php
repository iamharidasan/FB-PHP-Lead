<?php
$challenge = $_REQUEST['hub_challenge'];
$verify_token = $_REQUEST['hub_verify_token'];

if ($verify_token === 'new23') {
  echo $challenge;
}
include("DB.php");
include("FB.php");
$input_decoded = json_decode(file_get_contents('php://input'),true);
//file_put_contents("requests.log",print_r($input_decoded,true),FILE_APPEND);

$leadid = $input_decoded['entry'][0]['changes'][0]['value']['leadgen_id'];
$formid = $input_decoded['entry'][0]['changes'][0]['value']['form_id'];
if(!empty($formid)){
  $selectQuery = "SELECT * FROM `".TABLE_PREFIX."forms` WHERE `formID` = '".$formid."'";
  $result = $db->query($selectQuery);
  if($result->num_rows==1){
    $formRow = $result->fetch_assoc();
    $projectNameToPass = $formRow['projectName'];
    $accessToken = $formRow['accessToken'];
    try {
      // Returns a `FacebookFacebookResponse` object
      $response = $fb->get(
        '/'.$leadid,
        $accessToken
      );
    } catch(FacebookExceptionsFacebookResponseException $e) {
      $error = 'Graph returned an error: ' . $e->getMessage();
      file_put_contents("errors.log",print_r($error,true),FILE_APPEND);
      exit;
    } catch(FacebookExceptionsFacebookSDKException $e) {
      $error = 'Facebook SDK returned an error: ' . $e->getMessage();
      file_put_contents("errors.log",print_r($error,true),FILE_APPEND);
      exit;
    }
    $data = $response->getDecodedBody();
    file_put_contents("requests.log",print_r($data,true),FILE_APPEND);
    foreach($data['field_data'] as $field){
      if($field['name']=='first_name'){
        $firstName = $field['values'][0];
      }else if($field['name']=='last_name'){
        $lastName = $field['values'][0];
      }else if($field['name']=='email'){
        $email = $field['values'][0];
      }else if($field['name']=='phone_number'){
        $phoneNumber = $field['values'][0];
      }
    }

    $dataArr = array(
      "firstName"=>$firstName,
      "lastName"=> $lastName,
      "email"=> $email,
      "mobilePhone"=> $phoneNumber,
    );
    
    $jsondata = json_encode($dataArr);

    try{
      $curl = curl_init();
      
      curl_setopt_array($curl, array(
        CURLOPT_URL => "URL",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>$jsondata,
        CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "Cache-Control: no-cache"
        ),
        CURLOPT_ENCODING=> "gzip"
      ));
      echo $json_response = curl_exec($curl);
      curl_close($curl);
      file_put_contents("responses.log",$jsondata,FILE_APPEND);
      file_put_contents("responses.log",PHP_EOL.$json_response,FILE_APPEND);
    } catch(Exception $e){
      file_put_contents("error.log",$e->getMessage(),FILE_APPEND);
    }
  }
}