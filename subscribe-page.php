<?php
include('FB.php');
if(isset($_REQUEST['pageId'])){    
    if(!session_id()) {
        session_start();
        
    }
    if(isset($_SESSION['LLaccessToken'])){
        $fb->setDefaultAccessToken($_SESSION['LLaccessToken']);
        $accessToken = $_SESSION['LLaccessToken'];
    }else if(isset($_SESSION['accessToken'])){
        $fb->setDefaultAccessToken($_SESSION['accessToken']);
        $accessToken = $_SESSION['accessToken'];
    }
    try {
        // Returns a `FacebookFacebookResponse` object
        $response = $fb->post(
            '/'.$_REQUEST['pageId'].'/subscribed_apps',
            array (
            'subscribed_fields' => 'leadgen'
            ),
            $_REQUEST['pageToken']
        );
    } catch(FacebookExceptionsFacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(FacebookExceptionsFacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    echo $graphNode = $response->getGraphNode();
}else{
    header("Location: index.php");
}