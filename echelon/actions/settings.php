<?php
$auth_name = 'add_user';
require '../inc.php';

## Check that the form was posted and that the user did not just stumble here ##
if(!$_POST['settings-sub']) :
	set_error('Please do not call that page directly, thank you.');
	send('../index.php');
endif;

## Check Token ##
//if(verifyFormToken('settings', $tokens) == false) // verify token
//	ifTokenBad('Settings Edit');

## Get Vars ##
$f_name = cleanvar($_POST['name']);
$f_num_games  = cleanvar($_POST['num_games']);
$f_limit_rows = cleanvar($_POST['limit_rows']);
$f_min_pw_len = cleanvar($_POST['min_pw_len']);
$f_user_key_expire = cleanvar($_POST['user_key_expire']);
$f_email = cleanvar($_POST['email']);
$f_admin_name = cleanvar($_POST['admin_name']);
$f_https = cleanvar($_POST['https']);
$f_allow_ie = cleanvar($_POST['allow_ie']);
$f_time_format  = cleanvar($_POST['time_format']);
$f_time_zone = cleanvar($_POST['time_zone']);     
$f_email_header = cleanvar($_POST['email_header']);
$f_email_footer = cleanvar($_POST['email_footer']);

if($f_https == 'on')
	$f_https = 1;
else
	$f_https = 0;
	
if($f_allow_ie == 'on')
	$f_allow_ie = 1;
else
	$f_allow_ie = 0;

## Check for empty vars ##
emptyInput($f_name, 'site name');
emptyInput($f_num_games, 'number of games');
emptyInput($f_limit_rows, 'no of rows per table page');
emptyInput($f_min_pw_len, 'minimum password length');
emptyInput($f_user_key_expire, 'user registration key length');
emptyInput($f_email, 'site email');
emptyInput($f_admin_name, 'admin name');
emptyInput($f_time_format, 'time format');
emptyInput($f_time_zone, 'time zone');
emptyInput($f_email_header, 'email header text');
emptyInput($f_email_footer, 'email footer text');

## Check no. ##
if(!is_numeric($f_num_games) || !is_numeric($f_limit_rows) || !is_numeric($f_min_pw_len) || !is_numeric($f_user_key_expire) )
	sendBack('That some of information is suppose to be a number!');
	
## Check Email is valid ##
if(!filter_var($f_email, FILTER_VALIDATE_EMAIL)) 
	sendBack('The email supplied is not valid');
	
## Create arr ay of sent vars ##
$sent_settings = array(
	'name' => $f_name,
	'num_games' => $f_num_games,
	'limit_rows' => $f_limit_rows,
	'min_pw_len' => $f_min_pw_len,
	'user_key_expire' => $f_user_key_expire,
	'email' => $f_email,
	'admin_name' => $f_admin_name,
	'https' => $f_https,
	'allow_ie' => $f_allow_ie,
	'time_format' => $f_time_format,
	'time_zone' => $f_time_zone,    
	'email_header' => $f_email_header,
	'email_footer' => $f_email_footer
);


## What needs updating ##
$settings_table = $dbl->getSettings('cosmos'); // get the values of the settings from the config db table

$updates = array_diff($sent_settings, $settings_table); // find the differences (thoses differs are the only things we need to update)

//var_dump($updates);
//exit;
## Update DB ##
foreach($updates as $key => $value) :
	if($key == 'num_games' || $key == 'limit_rows' || $key == 'min_pw_len' || $key == 'user_key_expire')
		$value_type = 'i';
	else
		$value_type = 's';
		
	$result = $dbl->setSettings($value, $key, $value_type); /// update the settings in the DB
	
	if($result == false)
		sendBack('Something did not update');

endforeach;

## Return ##
sendGood('Your settings have been updated');
	