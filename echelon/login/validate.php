<?php include "../ctracker.php"; ?>
<?php require_once('../Connections/inc_config.php'); ?>
<?php 
require_once('../Connections/wwwvalidate.php');

// Make sure we don't cache this...
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: post-check=0, pre-check=0",false);
session_cache_limiter("must-revalidate");

// Start the session... (Must be after session_cache_limiter!)
session_start(); 

// Check if the number of attempts before processing the request..
if ($_SESSION['xlrwrong'] >= ("3"))
  {
  header ("Location: <?php echo $path; ?>/login/locked.html");
  exit;
  }

// Check if any of the form-fields is empty...
if (($_POST['loginname'] == NULL) || ($_POST['password'] == NULL))
  {
  $_SESSION['xlrwrong']++;
  header ("Location: $path/index.php");
  exit;
  }

// Query the userbase...
$colname_rs_validate = "0";
if (isset($_POST['loginname'])) {
  $colname_rs_validate = (get_magic_quotes_gpc()) ? $_POST['loginname'] : addslashes($_POST['loginname']);
}
mysql_select_db($database_wwwvalidate, $wwwvalidate);
$query_rs_validate = sprintf("SELECT * FROM users WHERE username = '%s'", $colname_rs_validate);
$rs_validate = mysql_query($query_rs_validate, $wwwvalidate) or die(mysql_error());
$row_rs_validate = mysql_fetch_assoc($rs_validate);
$totalRows_rs_validate = mysql_num_rows($rs_validate);

// Check if the given username/password match against the userbase...
$upass = "";
if (isset($_POST['password']))
  {
  $upass = (get_magic_quotes_gpc()) ? $_POST['password'] : addslashes($_POST['password']);
  }
if (md5($upass) == $row_rs_validate['password'])
  {
  // Seems that the data is correct...
  $MM_UserName = $row_rs_validate['username'];
  $MM_UserLevel = $row_rs_validate['ech_level'];
  // Store the results in a sessionvar...
    $_SESSION['xlradmin'] = $MM_UserName;
    $_SESSION['xlradminlevel'] = $MM_UserLevel;
	  $_SESSION['xlrsesid'] = session_id();
    $_SESSION['xlrwrong'] = 0;
	//$_SESSION['xlrsignature'] = $_SERVER['SERVER_SIGNATURE']; //Removed... not understood by IIS

  // No need to keep the queryresults now...
  mysql_free_result($rs_validate);
  header ("Location: $path/clients.php");
  exit;
  }

// The thread of wrong answers, first clear our query
mysql_free_result($rs_validate);

// Number of false logins...
$_SESSION['xlrwrong']++;
header ("Location: $path/index.php");
exit;
?>
