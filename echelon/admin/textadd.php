<?php include "../ctracker.php"; ?>
<?php // Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 2;
require_once('../login/inc_authorize.php');
require_once('../Connections/b3connect.php');
require_once('../Connections/inc_config.php');
require_once('rcon.php');

$id = "0";
$comment = $_POST['comment']. " Noticed by " . $_SESSION['xlradmin'];
$redirectto = "../clients.php";

if (isset($_GET['id'])) {
	$id = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
	}
if (isset($_SERVER['HTTP_REFERER'])) {
	$redirectto = (get_magic_quotes_gpc()) ? $_SERVER['HTTP_REFERER'] : addslashes($_SERVER['HTTP_REFERER']);
	}

// Insert code to update databasefield comment with the comment posted in previous page
// old statement $sql = "UPDATE clients SET comment = '$comment' WHERE id = '$id'";
$sql = "INSERT INTO `penalties` (`id`, `type`, `duration`, `inactive`, `admin_id`, `time_add`, `time_edit`, `time_expire`, `reason`, `keyword`, `client_id`) VALUES ('', 'NOTICE', '0', '0', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '-1', '". $comment ."', '', $id)";
mysql_select_db($database_b3connect, $b3connect);
mysql_query($sql, $b3connect);

$redirect = "Location: " .  $redirectto;
header ($redirect);

?>
