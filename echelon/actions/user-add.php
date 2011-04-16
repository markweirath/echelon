<?php
$auth_name = 'add_user';
require '../inc.php';

## if form is submitted ##	
if(!isset($_POST['add-user'])) // if this was not a post request then send back with error 
	sendBack('Please do not access that page directly');

## check that the sent form token is corret
if(!verifyFormToken('adduser', $tokens)) // verify token
	ifTokenBad('Add User');

// set email and comment and clean
$email = cleanvar($_POST['email']);
$comment = cleanvar($_POST['comment']);
$group = cleanvar($_POST['group']);

// check the new email address is a valid email address
if(!filter_var($email,FILTER_VALIDATE_EMAIL))
	sendBack('That email is not valid');

// Create a unique key for the user
$text = $admin_id.$email.uniqid(microtime(), true).$group; // take sent data and some random data to create a random string
$rand_text = str_shuffle($text); // shuffle the string to make more random
$user_key = genHash($rand_text); // hash the random string to get the user hash
	
## run query to add key to the DB ##
$add_user = $dbl->addEchKey($user_key, $email, $comment, $group, $mem->id);
if(!$add_user)
	sendBack('There was a problem adding the key into the database');

//send the email or message after adding to the DB
if(USE_MAIL) {
	## email user about the key ##
	$body = '<html><body>';
	$body .= '<h2>Echelon User Key</h2>';
	$body .= $config['cosmos']['email_header'];
	$body .= 'This is the key you will need to use to register on Echelon. 
				<a href="http://'.$_SERVER['SERVER_NAME'].PATH.'register.php?key='.$user_key.'&amp;email='.$email.'">Register here</a>.<br />';
	$body .= 'Registration Key: '.$user_key;
	$body .= $config['cosmos']['email_footer'];
	$body .= '</body></html>';
	
	// replace %ech_name% in body of email with var from config
	$body = preg_replace('#%ech_name%#', $config['cosmos']['name'], $body);
	// replace %name%
	$body = preg_replace('#%name%#', 'new user', $body);
	
	$headers = "From: echelon@".$_SERVER['HTTP_HOST']."\r\n";
	$headers .= "Reply-To: ". EMAIL ."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$subject = "Echelon User Registration";
	
	// send email
	if(!mail($email, $subject, $body, $headers))
		sendback('There was a problem sending the email.');
	sendGood('Key Setup and Email has been sent to user');
}
// remind the admin to add the new user as they did not recieve an email
sendGood('Don\'t forget to give the user their registration key, since it was not mailed');