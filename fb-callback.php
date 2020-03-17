<?php
include('DB.php');
if(!session_id()) {
    session_start();
}
require_once('Facebook/autoload.php');
$fb = new \Facebook\Facebook([
    'app_id' => '2669120756468758',
    'app_secret' => 'a1e3ace41a5c96318eb6650a828ccd0f',
    'default_graph_version' => 'v6.0',
]);
$helper = $fb->getRedirectLoginHelper();
if (isset($_GET['state'])) {
    $helper->getPersistentDataHandler()->set('state', $_GET['state']);
}
try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (! isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    exit;
}

// Logged in
$_SESSION['accessToken'] = (string) $accessToken->getValue();
$fb->setDefaultAccessToken($accessToken);

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);

if ( $accessToken->isLongLived()) {
    //echo "ehre";
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
        exit;
    }

    //echo $accessToken->getValue();
    $_SESSION['LLaccessToken'] = (string) $accessToken->getValue();
    $fb->setDefaultAccessToken($accessToken);
}

try {
    // Get the \Facebook\GraphNodes\GraphUser object for the current user.
    // If you provided a 'default_access_token', the '{access-token}' is optional.
    $response = $fb->get('/me');
    $requestPicture = $fb->get('/me/picture?redirect=false&height=300');
    $responsePages = $fb->get('/me/accounts');
} catch(\Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(\Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
$me = $response->getGraphUser();
$mepic = $requestPicture->getGraphUser();
$data = $responsePages->getDecodedBody();
$_SESSION['userid'] = $me->getId();
$_SESSION['username'] = $me->getName();
$selectQuery = "SELECT * FROM `".TABLE_PREFIX."profile` WHERE `userId` = '".$me->getId()."'";
$result = $db->query($selectQuery);
if($result->num_rows == 0){
    $insertQuery = "INSERT INTO `".TABLE_PREFIX."profile` (`name`,`userToken`,`profileImg`,`userId`) VALUES ('".$me->getName()."','".$accessToken->getValue()."','".$mepic['url']."','".$me->getId()."')";
    $db->query($insertQuery);
}else{
    $updateQuery = "UPDATE `".TABLE_PREFIX."profile` SET `userToken` = '".$accessToken->getValue()."' WHERE `userId` = '$me->getId()'";
    $db->query($updateQuery);
}
header('Location: dashboard.php');
?>