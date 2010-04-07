<?php
$auth_name = 'add_user';
require '../inc.php';

## Check that the form was posted and that the user did not just stumble here ##
if(!$_POST['settings-sub']) :
	set_error('Please do not call that page directly, thank you.');
	send('../index.php');
endif;

## Check Token ##
if(verifyFormToken('settings', $tokens) == false) // verify token
	ifTokenBad('Settings Edit');

## Get Vars ##
$name = cleanvar($_POST['name']);
$num_games  = cleanvar($_POST['num_games']);
$limit_rows = cleanvar($_POST['limit_rows']);
$min_pw_len = cleanvar($_POST['min_pw_len']);
$user_key_expire = cleanvar($_POST['user_key_expire']);
$email = cleanvar($_POST['email']);
$admin_name = cleanvar($_POST['admin_name']);
$https = cleanvar($_POST['https']);
$allow_ie = cleanvar($_POST['allow_ie']);
$time_format  = cleanvar($_POST['time_format']);
$time_zone = cleanvar($_POST['time_zone']);     
$email_header = cleanvar($_POST['email_header']);
$email_footer = cleanvar($_POST['email_footer']);

## Check for empty vars ##
emptyInput($name, 'site name');
emptyInput($num_games, 'number of games');
emptyInput($limit_rows, 'no of rows per table page');
emptyInput($min_pw_len, 'minimum password length');
emptyInput($user_key_expire, 'user registration key length');
emptyInput($email, 'site email');
emptyInput($admin_name, 'admin name');
emptyInput($https, 'data not sent');
emptyInput($allow_ie, 'data not sent');
emptyInput($time_format, 'time format');
emptyInput($time_zone, 'time zone');
emptyInput($email_header, 'email header text');
emptyInput($email_footer, 'email footer text');

## Check no. ##
if(!is_numeric($num_games) || !is_numeric($limit_rows) || !is_numeric($min_pw_len) || !is_numeric($user_key_expire) )
	sendBack('That some of information is suppose to be a number!');
	
## Check Email is valid ##
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
	sendBack('The email supplied is not valid');

## What needs updating ##

## Update DB ##

## Return with succss/failure msg ##
	