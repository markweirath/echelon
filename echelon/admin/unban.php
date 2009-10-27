<?php include "../ctracker.php"; ?>
<?php // Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 2;
require_once('../login/inc_authorize.php');
require_once('../Connections/b3connect.php');
require_once('../Connections/inc_config.php');
require_once('rcon.php');

//global $b3connect;

$banid = "0";
$pbid = "0";
$type = "";
$redirectto = "../clients.php";

if (isset($_GET['banid'])) {
	$banid = (get_magic_quotes_gpc()) ? $_GET['banid'] : addslashes($_GET['banid']);
	}
if (isset($_GET['pbid'])) {
	$pbid = (get_magic_quotes_gpc()) ? $_GET['pbid'] : addslashes($_GET['pbid']);
	}
if (isset($_GET['type'])) {
	$type = (get_magic_quotes_gpc()) ? $_GET['type'] : addslashes($_GET['type']);
	}
if (isset($_SERVER['HTTP_REFERER'])) {
	$redirectto = (get_magic_quotes_gpc()) ? $_SERVER['HTTP_REFERER'] : addslashes($_SERVER['HTTP_REFERER']);
	}

// Insert code to update databasefield inactive in banstable to 1
$sql = "UPDATE `penalties` SET `inactive` = '1' WHERE `id` = $banid LIMIT 1";
mysql_select_db($database_b3connect, $b3connect);
mysql_query($sql, $b3connect);


if ((($type == "Ban") || ($type == "TempBan") ) && ( $PBactive == "1" )) {
	$command = "pb_sv_unbanguid " . $pbid;
	rcon ($command);
	sleep(2);
	$command = "pb_sv_updbanfile";
	rcon ($command);
	}

$redirect = "Location: " .  $redirectto;
header ($redirect);

?>
