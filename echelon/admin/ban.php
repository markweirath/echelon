<?php include "../ctracker.php"; ?>
<?php // Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 2;
require_once('../login/inc_authorize.php');
require_once('../Connections/b3connect.php');
require_once('../Connections/inc_config.php');
require_once('rcon.php');

$id = "0";
$pbid = "0";
$clientname = "Unknown";
$client_ip = "0.0.0.0";
$reason = "Banned by an Echelon WebAdmin. (" . $_SESSION['xlradmin'] . ")";
$type = "Ban";
$redirectto = "../clients.php";

if (isset($_GET['id'])) {
	$id = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
	}
if (isset($_GET['pbid'])) {
	$pbid = (get_magic_quotes_gpc()) ? $_GET['pbid'] : addslashes($_GET['pbid']);
	}
if (isset($_GET['clientname'])) {
	$clientname = (get_magic_quotes_gpc()) ? $_GET['clientname'] : addslashes($_GET['clientname']);
	}
if (isset($_GET['client_ip'])) {
	$client_ip = (get_magic_quotes_gpc()) ? $_GET['client_ip'] : addslashes($_GET['client_ip']);
	}
if (isset($_GET['type'])) {
	$type = (get_magic_quotes_gpc()) ? $_GET['type'] : addslashes($_GET['type']);
	}
if (isset($_POST['reason'])) {
	$reason = (get_magic_quotes_gpc()) ? $_POST['reason'] : addslashes($_POST['reason']);
  $reason .= " (by " . $_SESSION['xlradmin'] . ")";
	}
if (isset($_SERVER['HTTP_REFERER'])) {
	$redirectto = (get_magic_quotes_gpc()) ? $_SERVER['HTTP_REFERER'] : addslashes($_SERVER['HTTP_REFERER']);
	}

// Insert code to update databasefield inactive in banstable to 1
$sql = "INSERT INTO `penalties` (`id`, `type`, `duration`, `inactive`, `admin_id`, `time_add`, `time_edit`, `time_expire`, `reason`, `keyword`, `client_id`) VALUES ('', 'Ban', '0', '0', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '-1', '$reason', '', $id)";
mysql_select_db($database_b3connect, $b3connect);
mysql_query($sql, $b3connect);


// PB_SV_BanGuid [guid] [player_name] [IP_Address] [reason]
if (( $type == "Ban" ) && ( $PBactive == "1" )) {
	$command = "pb_sv_banguid " . $pbid . " " . $clientname . " " . $client_ip . " " . $reason;
	rcon ($command);
	sleep(2);
	$command = "pb_sv_updbanfile";
	rcon ($command);
	}

$redirect = "Location: " .  $redirectto;
header ($redirect);

?>
