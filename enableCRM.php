<?php
include("FB.php");
include("DB.php");
if(isset($_POST['formid'])){
    try {
        // Returns a `FacebookFacebookResponse` object
        $response = $fb->get(
          '/'.$_POST['formid'],
          $_POST['pageToken']
        );
    } catch(FacebookExceptionsFacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(FacebookExceptionsFacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    $data = $response->getBody();
    $formdata = json_decode($data,true);
    $selectQuery = "SELECT * FROM `".TABLE_PREFIX."forms` WHERE `formID` = '".$_POST['formid']."'";
    $result = $db->query($selectQuery);
    $formname = $db->real_escape_string($formdata['name']);
    if($result->num_rows==0){
        $insertQuery = "INSERT INTO `".TABLE_PREFIX."forms` (`formID`,`formName`,`accessToken`,`projectName`) VALUES ('".$_POST['formid']."','".$formname."','".$_POST['pageToken']."','".$_POST['projectName']."')";
        $db->query($insertQuery);
    }else{
        $updateQuery = "UPDATE `".TABLE_PREFIX."forms` SET `formName` = '".$formname."', `accessToken` = '".$_POST['pageToken']."', `projectName` = '".$_POST['projectName']."' WHERE `formID` = '".$_POST['formid']."')";
        $db->query($updateQuery);
    }
    echo "{status:'Success'}";
}else{
    header('Location: index.php');
}