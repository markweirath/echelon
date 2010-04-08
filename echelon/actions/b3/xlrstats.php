<?php
$auth_name = 'edit_xlrstats';
$b3_conn = true; // this page needs to connect to the B3 database
require '../../inc.php';

if(!isset($_POST['xlrstats-sub'])) : // if the form is submitted
	set_error('Please do not call that page directly');
	send('../../');
endif;

## check that the sent form token is corret
//if(verifyFormToken('xlrstats', $tokens) == false) // verify token
//	ifTokenBad('XLRStats Edit');

// Gets vars from form
$cid = cleanvar($_POST['cid']);
$name = cleanvar($_POST['fixed-name']);
$hide = cleanvar($_POST['hidden']);

if($hide == 'on')
	$hide = 1;
else
	$hide = 0;


// NOTE: empty fixed name means acts as disabling it, so do not check for an empty input

## Check sent client_id is a number ##
if(!is_numeric($cid))
	sendBack('Invalid data sent, ban not added');

// set common vars	
$type = 'XLRStats';
$user_id = $_SESSION['user_id'];
$comment = 'XLRStats information changed';

## LOG Query ##
$results = $dbl->addEchLog($type, $comment, $cid, $user_id);

## Update XLRStats table ##
$query = "UPDATE xlr_playerstats SET hide = ?, fixed_name = ? WHERE client_id = ? LIMIT 1";
$stmt = $db->mysql->prepare($query) or die('DB Error');
$stmt->bind_param('isi', $hide, $name, $cid);
$stmt->execute();

if($stmt->affected_rows > 0)
	sendGood('XLRStats information edited');
else
	sendBack('There is a problem, changes were not saved');