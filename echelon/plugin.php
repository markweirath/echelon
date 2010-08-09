<?php
$page = "plugin";
$page_title = "Plugin Page";
$auth_name = 'login';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = false; // this page requires the pagination part of the footer
$query_normal = false;
require 'inc.php';

if(!isset($_GET['pl']) || $_GET['pl'] == '')
	//sendError('plug'); // send to error page with no plugin specified error
	exit;
else
	$plugin = addslashes(cleanvar($_GET['pl']));
	
$varible = NULL;
if(isset($_GET['v']))
	$varible = cleanvar($_GET['v']);
	
$page = $plugin; // name of the page is the plugin name

$Cplug = $plugin::getInstance();

$page_title = $Cplug->getTitle(); // get the page title from the title of the plugin

## Require Header ##	
require 'inc/header.php';

if($mem->reqLevel($Cplug->getPagePerm())) // name of the plugin is also the name of the premission associated with it
	echo $Cplug->returnPage($varible); // return the relevant page information for this plugin

require 'inc/footer.php'; 
?>