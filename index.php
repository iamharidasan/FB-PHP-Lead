<?php
require_once('Facebook/autoload.php');
$fb = new \Facebook\Facebook([
    'app_id' => '2669120756468758',
    'app_secret' => 'a1e3ace41a5c96318eb6650a828ccd0f',
    'default_graph_version' => 'v6.0',
]);
$helper = $fb->getRedirectLoginHelper();
$permissions = array('manage_pages','leads_retrieval'); // Optional permissions
$loginUrl = $helper->getLoginUrl('https://www.assetzpropertybangalore.com/fbapp/fb-callback.php', $permissions);
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Login | Assetz FB App</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
    <style>
      .login-holder{
        border:1px solid #4267B2;
      }
      .login-holder a{
        background:#4267B2;
        color:#fff;
        padding:7.5px 15px;
        display:inline-block;
        text-decoration:none
      }
    </style>
  </head>
  <body>
    <section class="login">
      <div class="container">
        <div class="row vh-100 align-items-center">
          <div class="col-12 col-lg-6 offset-lg-3 login-holder text-center pt-3 pb-3">
            <h2 class="text-center mb-3">
              Assetz Properties CRM to Facebook
            </h2>
            <a href="<?php echo $loginUrl ?>"><i class="fab fa-facebook"></i> Login with Facebook</a>
          </div>
        </div>
      </div>
    </section>    
  </body>
</html>