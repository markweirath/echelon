<?php
$auth_name = 'chatlogs';
$b3_conn = true;
require '../../../inc.php';

$plugin = chatlogs::getInstance();

if(isset($_REQUEST['talkback'])) :
	
	if($mem->reqLevel('chats_talk_back')) : // extra perms needed to talk to server
		if(!empty($_GET['last-id']))
			$last_id = cleanvar($_GET['last-id']);
		else
			$last_id = 0;

		$data = $plugin->talkback($_REQUEST['talkback'], $_REQUEST['srv'], $last_id); // send rcon talkback / get data for buildLine

		if(detectAJAX()) // if is AJAX request
			echo $data; // echo the built line
		else
			sendBack(''); // sendBack with no error
	endif;

endif;

if(isset($_GET['auto'])) :
	
	echo $plugin->getLastChats($_GET['table-num'], $_GET['last-id']);

endif;

if(isset($_POST['tables'])) :

	if($mem->reqLevel('chats_edit_tables')) : // extra perms needed to edit settings
	
		$tables = $_POST['tables'];
		$names = $_POST['table-names'];

		$num_tables = count(explode(',', $tables));
		$num_names = count(explode(',', $names));
		
		if($num_tables != $num_names)
			sendBack('You need to have the same number of tables listed as you do names');
		
		emptyInput($tables, 'tables');
		emptyInput($names, 'names');
		
		$results = $plugin->editSettings($tables, $names);

		if(!$results)
			sendBack('There was a problem. Maybe the table names you gave us are incorrect.'); // sendBack with no error
		else
			sendGood('The chatlog settings have been updated');
	endif;

endif;