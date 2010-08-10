<?php
$auth_name = 'edit_perms';
require '../inc.php';
##

function getPostsPerms($posts) {

	$data = array();
	foreach($posts as $key => $value) : // get all the checkboxes values and put into an array

		if($value == 'on') // if checked
			$data[$key] = true; // its true
		else
			$data[$key] = false;
			
	endforeach;
	
	return $data;

} // end function getPostsPerms

if($_GET['t'] == 'add') : // add group perms

	# verify token #
	//if(!verifyFormToken('perm-group-add', $tokens))
	//	ifTokenBad('Add group');

	$name = cleanvar($_POST['g-name']);
	
	$data = getPostsPerms($_POST);
	
	$perms_list = $dbl->getPermissions(false); // get a full list of all perms (without desc)

	foreach($perms_list as $perm) : // compare the two lists and create a common list of perms
		$perm_id = $perm['id'];
		$perm_name = $perm['name'];
		
		if(array_key_exists($perm_name, $data))
			$list .= $perm_id . ',';

	endforeach;

	$perms = substr($list, 0, -1); // remove the final comma

	## get slug from name ##
	$name = strtolower(trim($name));
	$slug = preg_replace('/[^a-z0-9-]/', '', $name);
	
	$result = $dbl->addGroup($name, $slug, $perms);
	
	if(!$result)
		sendBack('There was a problem creating the new group');
	else
		sendGood('The group '. $name .' has been created!');

else : // edit group perms

	# verify token #
	if(!verifyFormToken('perm-group-edit', $tokens))
		ifTokenBad('Edit Group Permissions');

	$group_id = cleanvar($_GET['gid']); // get the group to update from the URL

	## check numeric id ##
	if(!is_numeric($group_id))
		sendBack('Invalid data sent, request aborted');

	$data = getPostsPerms($_POST);

	$perms_list = $dbl->getPermissions(false); // get a full list of all perms (without desc)

	foreach($perms_list as $perm) : // compare the two lists and create a common list of perms
		$perm_id = $perm['id'];
		$perm_name = $perm['name'];
		
		if(array_key_exists($perm_name, $data))
			$list .= $perm_id . ',';

	endforeach;

	$perms_new = substr($list, 0, -1); // remove the final comma

	$result = $dbl->setGroupPerms($group_id, $perms_new); // update the DB

	if(!$result)
		sendBack('There was an error updating the database with the new information');
	else
		sendGood('Everything has been updated and saved successfully');
	
endif;