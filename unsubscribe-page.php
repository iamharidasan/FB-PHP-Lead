<?php
include('FB.php');
if(isset($_REQUEST['pageId'])){
    if(!session_id()) {
        session_start();
        
    }if(isset($_SESSION['LLaccessToken'])){
        $fb->setDefaultAccessToken($_SESSION['LLaccessToken']);
    }else{
        $fb->setDefaultAccessToken($_SESSION['accessToken']);
    }
    try {
        // Returns a `FacebookFacebookResponse` object
        $response = $fb->delete(
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
    $graphNode = $response->getGraphNode();
}else{
    header("Location: index.php");
}