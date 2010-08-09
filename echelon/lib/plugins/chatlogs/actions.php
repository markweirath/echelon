<?php
$auth_name = 'chatlogs';
$b3_conn = true;
require '../../../inc.php';

$plugin = chatlogs::getInstance();

if(isset($_REQUEST['talkback'])) :

	if(!empty($_GET['last-id']))
		$last_id = cleanvar($_GET['last-id']);
	else
		$last_id = 0;

	$data = $plugin::talkback($_REQUEST['talkback'], $_REQUEST['srv'], $last_id); // send rcon talkback / get data for buildLine

	if(detectAJAX()) // if is AJAX request
		echo $data; // echo the built line
	else
		sendBack(''); // sendBack with no error

endif;

if(isset($_GET['auto'])) :
	
	echo $plugin::getLastChats($_GET['table-num'], $_GET['last-id']);

endif;