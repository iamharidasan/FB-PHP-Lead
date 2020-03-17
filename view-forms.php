<?php
include('FB.php');
include('DB.php');
if(isset($_GET['id'])){
    if(!session_id()) {
        session_start();
    }
    if(isset($_SESSION['LLaccessToken'])){
        $fb->setDefaultAccessToken($_SESSION['LLaccessToken']);
    }else if(isset($_SESSION['accessToken'])){
        $fb->setDefaultAccessToken($_SESSION['accessToken']);
    }
    try {
        // Get the \Facebook\GraphNodes\GraphUser object for the current user.
        // If you provided a 'default_access_token', the '{access-token}' is optional.
        $response = $fb->get('/me');
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
    $data = $responsePages->getDecodedBody();
    foreach($data['data'] as $page){
        if($page['id']==$_GET['id']){
            $pageToken = $page['access_token'];
            $pageName = $page['name'];
            $pageId = $_GET['id'];
        }
    }
    try {
        // Returns a `FacebookFacebookResponse` object
        $response = $fb->get(
          '/'.$pageId.'/leadgen_forms',
          $pageToken
        );
    } catch(FacebookExceptionsFacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(FacebookExceptionsFacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
    $graphNode = $response->getBody();
    $forms = json_decode($graphNode,true);
    //echo "<pre>";
    //print_r(json_decode($graphNode,true));
    //echo "</pre>";
?>
<!doctype html>
<html lang="en">
  <head>
    <title><?php echo $pageName ?> | Subscribe Forms | Assetz FB App</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
  </head>
  <body>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center">Forms Associated with <?php echo $pageName ?></h1>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Form Name</th>
                                    <th>Form ID</th>
                                    <th>Project to be Linked</th>
                                    <th>Status</th>
                                    <th>CRM Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($forms['data'] as $form){
                                $selectQuery = "SELECT * FROM `".TABLE_PREFIX."forms` WHERE `formID` = '".$form['id']."'";
                                $result = $db->query($selectQuery);                                
                                ?>
                                <tr>
                                    <td scope="row"><?php echo $form['name'] ?></td>
                                    <td><?php echo $form['id'] ?></td>
                                    <td>
                                        <select name="project" id="projectID-<?php echo $form['id'] ?>">
                                        <?php if($result->num_rows==0){ ?>
                                            <option value="">Select a Project</option>
                                            <option value="63DEGREEEAST">63 DEGREE EAST</option>
                                            <option value="HERENOW">HERE & NOW</option>
                                            <option value="MARQ2">MARQ 2</option>
                                            <option value="SUNSANCTUM">SUN & SANCTUM</option>
                                            <option value="38BANYAN">38 & BANYAN</option>
                                            <option value="EARTHESSENCE">EARTH & ESSENCE</option>
                                            <option value="LEAVESLIVES">LEAVES & LIVES</option>
                                            <option value="SOULSOIL">SOUL & SOIL</option>
                                            <option value="ATMOSAURA">ATMOS & AURA</option>
                                        <?php }else{
                                        $row = $result->fetch_assoc();
                                        ?>
                                            <option value="">Select a Project</option>
                                            <option value="63DEGREEEAST"<?php if($row['projectName']=="63DEGREEEAST"){echo ' selected="selected"';} ?>>63 DEGREE EAST</option>
                                            <option value="HERENOW"<?php if($row['projectName']=="HERENOW"){echo ' selected="selected"';} ?>>HERE & NOW</option>
                                            <option value="MARQ2"<?php if($row['projectName']=="MARQ2"){echo ' selected="selected"';} ?>>MARQ 2</option>
                                            <option value="SUNSANCTUM"<?php if($row['projectName']=="SUNSANCTUM"){echo ' selected="selected"';} ?>>SUN & SANCTUM</option>
                                            <option value="38BANYAN"<?php if($row['projectName']=="38BANYAN"){echo ' selected="selected"';} ?>>38 & BANYAN</option>
                                            <option value="EARTHESSENCE"<?php if($row['projectName']=="EARTHESSENCE"){echo ' selected="selected"';} ?>>EARTH & ESSENCE</option>
                                            <option value="LEAVESLIVES"<?php if($row['projectName']=="LEAVESLIVES"){echo ' selected="selected"';} ?>>LEAVES & LIVES</option>
                                            <option value="SOULSOIL"<?php if($row['projectName']=="SOULSOIL"){echo ' selected="selected"';} ?>>SOUL & SOIL</option>
                                            <option value="ATMOSAURA"<?php if($row['projectName']=="ATMOSAURA"){echo ' selected="selected"';} ?>>ATMOS & AURA</option>
                                        <?php } ?>
                                        </select>
                                    </td>
                                    <td><?php echo $form['status'] ?></td>
                                    <td class="text-center status-<?php echo $form['id'] ?>"><?php if($result->num_rows==0){echo '<i class="fas fa-times text-danger" title="CRM Disabled"></i>';}else{echo '<i class="fas fa-check text-success" title="CRM Enabled"></i>';} ?></td>
                                    <td class="action-<?php echo $form['id'] ?>">
                                        <?php
                                        if($result->num_rows==0){
                                        ?>
                                        <a href="javascript:void(0)" class="btn btn-warning" onClick="enableCRM('<?php echo $form['id'] ?>','<?php echo $pageToken; ?>')">Enable CRM</a>
                                        <?php
                                        }else{
                                        ?>
                                        <a href="javascript:void(0)" class="btn btn-danger" onClick="disableCRM('<?php echo $form['id'] ?>','<?php echo $pageToken; ?>')">Disable CRM</a>
                                        <?php
                                        }
                                        ?>
                                        
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script>
        function enableCRM(formID,pageToken){
            if(jQuery("#projectID-"+formID).val()==null || jQuery("#projectID-"+formID).val()==''){
                jQuery("#projectID-"+formID).next('div').remove();
                jQuery('<div class="text-danger">Please choose the project</div>').insertAfter("#projectID-"+formID);
            }else{
                jQuery(".action-"+formID).html('<img width="30" height="30" src="https://media.giphy.com/media/VlJkP9Vxi4nkI/giphy.gif" class="loading"/>');
                jQuery("#projectID-"+formID).next('div').remove();
                jQuery.ajax({
                    url:"enableCRM.php",
                    data:"formid="+formID+"&pageToken="+pageToken+"&projectName="+jQuery("#projectID-"+formID).val(),
                    method:"POST",
                    success:function(msg){
                        jQuery(".action-"+formID).html('<a href="javascript:void(0)" class="btn btn-danger" onClick="disableCRM(\''+formID+'\',\''+pageToken+'\')">Disable CRM</a>');
                        jQuery(".status-"+formID).html('<i class="fas fa-check text-success" title="CRM Enabled"></i>');
                    }
                })
            }
        }
        function disableCRM(formID,pageToken){
            if(jQuery("#projectID-"+formID).val()==null || jQuery("#projectID-"+formID).val()==''){
                jQuery("#projectID-"+formID).next('div').remove();
                jQuery('<div class="text-danger">Please choose the project</div>').insertAfter("#projectID-"+formID);
            }else{
                jQuery(".action-"+formID).html('<img width="30" height="30" src="https://media.giphy.com/media/VlJkP9Vxi4nkI/giphy.gif" class="loading"/>');
                jQuery("#projectID-"+formID).next('div').remove();
                jQuery.ajax({
                    url:"disableCRM.php",
                    data:"formid="+formID,
                    method:"POST",
                    success:function(msg){
                        jQuery(".action-"+formID).html('<a href="javascript:void(0)" class="btn btn-warning" onClick="enableCRM(\''+formID+'\',\''+pageToken+'\')">Enable CRM</a>');
                        jQuery(".status-"+formID).html('<i class="fas fa-times text-danger" title="CRM Disabled"></i>');
                    }
                })
            }
        }
    </script>
  </body>
</html>
<?php
}else{
    header('Location: index.php');
}