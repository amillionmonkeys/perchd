<?php
    require '../tmhOAuth/tmhOAuth.php';
    require '../tmhOAuth/tmhUtilities.php';

    $HTML = $API->get('HTML');
    $Form = $API->get('Form');
    
    $TwitterSettings = new PerchTwitter_Settings($API);
    $message = false;
    
    $CurrentSettings = $TwitterSettings->find();
    
     if(!is_object($CurrentSettings)) {
        $TwitterSettings->attempt_install();
        $CurrentSettings = $TwitterSettings->find();
    }
    
    

    if (is_object($CurrentSettings) && $CurrentSettings->settingTwitterKey()) {

        $tmhOAuth = new tmhOAuth(array(
          'consumer_key'    => $CurrentSettings->settingTwitterKey(),
          'consumer_secret' => $CurrentSettings->settingTwitterSecret()
        ));


        if (isset($_REQUEST['oauth_verifier'])) :
            access_token($tmhOAuth);
        elseif (isset($_REQUEST['verify'])) :
            verify_credentials($tmhOAuth);
        elseif (isset($_REQUEST['wipe'])) :
            wipe();
        endif;
    }else{
      $tmhOAuth = false;
    }


    if (isset($_SESSION['access_token'])) {
        $data = array();
        $data['settingTwitterToken'] = $_SESSION['access_token']['oauth_token'];
        $data['settingTwitterTokenSecret'] = $_SESSION['access_token']['oauth_token_secret'];

        $CurrentSettings->update($data);

        //print_r($_SESSION);

        unset($_SESSION['access_token']);
    }

    $details = array('settingTwitterKey'=>'', 'settingTwitterSecret'=>'', 'settingTwitterID'=>'', 'settingUpdateInterval'=>'');
    if (is_object($CurrentSettings)) $details = $CurrentSettings->to_array();

    /* ------------------------------------ Twitter Auth stuff ---------------------------------------------- */

        function outputError($tmhOAuth) {
          echo 'There was an error: ' . $tmhOAuth->response['response'] . PHP_EOL;
        }

        function wipe() {
          session_destroy();
          header('Location: ' . tmhUtilities::php_self());
        }


        // Step 1: Request a temporary token
        function request_token($tmhOAuth) {
          $code = $tmhOAuth->request(
            'POST',
            $tmhOAuth->url('oauth/request_token', ''),
            array(
              'oauth_callback' => tmhUtilities::php_self()
            )
          );



          if ($code == 200) {
            $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
            authorize($tmhOAuth);
          } else {
            outputError($tmhOAuth);
          }
        }


        // Step 2: Direct the user to the authorize web page
        function authorize($tmhOAuth) {
          $authurl = $tmhOAuth->url("oauth/authorize", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}";
          header("Location: {$authurl}");

          // in case the redirect doesn't fire
          echo '<p>To complete the OAuth flow please visit URL: <a href="'. $authurl . '">' . $authurl . '</a></p>';
        }


        // Step 3: This is the code that runs when Twitter redirects the user to the callback. Exchange the temporary token for a permanent access token
        function access_token($tmhOAuth) {
          $tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
          $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

          $code = $tmhOAuth->request(
            'POST',
            $tmhOAuth->url('oauth/access_token', ''),
            array(
              'oauth_verifier' => $_REQUEST['oauth_verifier']
            )
          );

          if ($code == 200) {
            $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
            unset($_SESSION['oauth']);
            header('Location: ' . tmhUtilities::php_self());
          } else {
            outputError($tmhOAuth);
          }
        }


        // Step 4: Now the user has authenticated, do something with the permanent token and secret we received
        function verify_credentials($tmhOAuth) {
          $tmhOAuth->config['user_token']  = $_SESSION['access_token']['oauth_token'];
          $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

          $code = $tmhOAuth->request(
            'GET',
            $tmhOAuth->url('1/account/verify_credentials')
          );

          if ($code == 200) {
            $resp = json_decode($tmhOAuth->response['response']);
            echo '<h1>Hello ' . $resp->screen_name . '</h1>';
            echo '<p>The access level of this token is: ' . $tmhOAuth->response['headers']['x_access_level'] . '</p>';
          } else {
            outputError($tmhOAuth);
          }
        }




    /* ------------------------------------ / Twitter Auth stuff ---------------------------------------------- */


    
    $Form->require_field('settingTwitterKey', 'Required');
    $Form->require_field('settingTwitterSecret', 'Required');
    $Form->require_field('settingTwitterID', 'Required');
    
    if ($Form->submitted()) {
       $postvars = array('settingTwitterID', 'settingUpdateInterval', 'settingTwitterKey', 'settingTwitterSecret');
    	 $data = $Form->receive($postvars);
    	 
       if (isset($_POST['reauth']) && $_POST['reauth'] == '1') {
          $data['settingTwitterToken'] = '';
          $data['settingTwitterTokenSecret'] = '';
       }

      $result = $CurrentSettings->update($data);  

        
    	
        if ($result) {
        	
            if (!$tmhOAuth) {
                $tmhOAuth = new tmhOAuth(array(
                  'consumer_key'    => $CurrentSettings->settingTwitterKey(),
                  'consumer_secret' => $CurrentSettings->settingTwitterSecret()
                ));

            }

        	$Twitter = new PerchTwitter();	   

            if (!$CurrentSettings->settingTwitterToken()){
                request_token($tmhOAuth);
            }

            $message = $HTML->success_message('Your settings have been updated. <a href="../">Return to listing &raquo;</a>');  
        }else{
            $message = $HTML->failure_message('Sorry, your settings could not be updated.');
        }
        
        $CurrentSettings = $TwitterSettings->find();
    	$details = $CurrentSettings->to_array();
        
        
    }
