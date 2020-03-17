<?php
include('FB.php');
if(!session_id()) {
    session_start();
    
}
if(isset($_SESSION['LLaccessToken'])){
    $fb->setDefaultAccessToken($_SESSION['LLaccessToken']);
}else if(isset($_SESSION['accessToken'])){
    $fb->setDefaultAccessToken($_SESSION['accessToken']);
}else{
    header("Location: index.php");
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
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Dashboard | Assetz FB App</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body>
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-12 pt-5 pb-5">
                        <div class="row">
                            <div class="col-12 col-lg-3">
                                <img src="<?php echo $mepic['url']; ?>" alt="<?php echo $me->getName(); ?>" class="rounded-circle w-100">
                            </div>
                            <div class="col-12 col-lg-9">
                                <h1>Welcome <?php echo $me->getName(); ?></h1>
                                <a href="/logout.php" class="btn btn-primary">Logout</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h4 class="mt-3">Configure the pages which you are managing</h4>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Page Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i=1; foreach($data['data'] as $page){
                                            $response = $fb->get('/'.$page['id'].'?fields=subscribed_apps{id}',$page['access_token']);
                                            $decodedBody = $response->getDecodedBody();
                                        ?>
                                            <tr>
                                                <td scope="row"><?php echo $i; ?></td>
                                                <td><?php echo $page['name']; ?></td>
                                                <td class="pageID <?php echo $page['id']; ?>">
                                                <?php if (array_key_exists("subscribed_apps",$decodedBody)){ ?>
                                                    <a href="view-forms.php?id=<?php echo $page['id']; ?>" class="btn btn-success">View Forms</a>
                                                    <a href="javascript:void(0)" onClick="removeAccess('<?php echo $page['id']; ?>','<?php echo $page['access_token'] ?>','<?php echo $page['name'] ?>')" class="btn btn-danger">Remove Access</a>
                                                <?php } else{ ?>
                                                    <a href="javascript:void(0)" onClick="grantAccess('<?php echo $page['id']; ?>','<?php echo $page['access_token'] ?>','<?php echo $page['name'] ?>')" class="btn btn-warning">Grant Access</a>
                                                <?php } ?>
                                                </td>
                                            </tr>
                                        <?php $i++; } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script>
            function grantAccess(pageId,pageToken,pageName){  
                jQuery(".pageID."+pageId).html('<img width="30" height="30" src="https://media.giphy.com/media/VlJkP9Vxi4nkI/giphy.gif" class="loading"/>');
                jQuery.ajax({
                    url:"subscribe-page.php",
                    data:"pageId="+pageId+"&pageToken="+pageToken+"&pageName="+pageName,
                    method:"POST",
                    success:function(msg){
                        jQuery(".pageID."+pageId+" .loading").remove();
                        jQuery(".pageID."+pageId).html('<a href="view-forms.php?id='+pageId+'" class="btn btn-success">View Forms</a> <a href="javascript:void(0)" onClick="removeAccess(\''+pageId+'\',\''+pageToken+'\',\''+pageName+'\')" class="btn btn-danger">Remove Access</a>');
                    }
                })
            }
            function removeAccess(pageId,pageToken,pageName){          
                jQuery(".pageID."+pageId).html('<img width="30" height="30" src="https://media.giphy.com/media/VlJkP9Vxi4nkI/giphy.gif" class="loading"/>');
                jQuery.ajax({
                    url:"unsubscribe-page.php",
                    data:"pageId="+pageId+"&pageToken="+pageToken+"&pageName="+pageName,
                    method:"POST",
                    success:function(msg){
                        jQuery(".pageID."+pageId+" .loading").remove();
                        jQuery(".pageID."+pageId).html('<a href="javascript:void(0)" onClick="grantAccess(\''+pageId+'\',\''+pageToken+'\',\''+pageName+'\')" class="btn btn-warning">Grant Access</a>');
                    }
                })
            }
        </script>
    </body>
</html>