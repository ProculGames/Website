<?php
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $privatekey = '6LcaMBQTAAAAAD3u4qxlMFmHfY7yDr4Q9WJCbvxE';
		
        $response = file_get_contents($url."?secret=".$privatekey."&response=".$_POST['g-recaptcha-response']."&remoteip".$_SERVER['REMOTE_ADDR']);
		
        $data = json_decode($response);
		
        if(isset($data->success) AND ($data->success==true))
		{
			// EDIT THE 2 LINES BELOW AS REQUIRED
            $email_to = "proculgamedevelopment@gmail.com";
            $email_subject = "Procul Games Application";
			
		function died() {
        // your error code can go here
        ?>
			<meta http-equiv="refresh" content="0;url=http://www.proculgames.com/application%20fail.html" />
		<?php 
        die();
 
    }
	
        // validation expected data exists
 
        if(!isset($_POST['FullName']) ||
 
            !isset($_POST['email']) ||
		
		    !isset($_POST['Position']) ||
 
            !isset($_POST['message'])) {
 
            died();
 
        }
	
        $FullName = $_POST['FullName']; // required
        $email_from = $_POST['email']; // required
	    $Position = $_POST['Position']; // required
        $comments = $_POST['message']; // required
        $error_message = "";
        $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
 
        if(!preg_match($email_exp,$email_from)) {
    $error_message .= 'The Email Address you entered does not appear to be valid.<br />';
  }
 
    $string_exp = "/^[A-Za-z .'-]+$/";
 
  if(!preg_match($string_exp,$FullName)) {
    $error_message .= 'The Name you entered does not appear to be valid.<br />';
  }
 
  if(strlen($comments) < 2) {
    $error_message .= 'The Message you entered do not appear to be valid.<br />';
  }
 
  if(strlen($error_message) > 0) {
    died($error_message);
  }
        $email_message = "Form details below.\n\n";
 
        function clean_string($string) {
            $bad = array("content-type","bcc:","to:","cc:","href");
            return str_replace($bad,"",$string);
 
    }
 
    $email_message .= "Full Name: ".clean_string($FullName)."\n";
	$email_message .= "Position: ".clean_string($Position)."\n";
    $email_message .= "Email: ".clean_string($email_from)."\n";
    $email_message .= "Message: ".clean_string($comments)."\n";
  
    // create email headers
    $headers = 'From: '.$email_from."\r\n";
    'Reply-To: '.$email_from."\r\n" .
    'X-Mailer: PHP/' . phpversion();
    @mail($email_to, $email_subject, $email_message, $headers);   
?>
    <meta http-equiv="refresh" content="0;url=http://www.proculgames.com/application%20submitted.html" />
<?php 
    }
        else
        {
			?>
		     <meta http-equiv="refresh" content="0;url=http://www.proculgames.com/application%20fail.html" />
			<?php 
        }
?>