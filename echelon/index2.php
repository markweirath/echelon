<?php
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');

$colname_rs_links = "1";
if (isset($_SESSION['xlradminlevel'])) {
  $colname_rs_links = (get_magic_quotes_gpc()) ? $_SESSION['xlradminlevel'] : addslashes($_SESSION['xlradminlevel']);
}
mysql_select_db($database_wwwvalidate, $wwwvalidate);
$query_rs_links = sprintf("SELECT * FROM links WHERE `level` >= %s ORDER BY name ASC", $colname_rs_links);
$rs_links = mysql_query($query_rs_links, $wwwvalidate) or die(mysql_error());
$row_rs_links = mysql_fetch_assoc($rs_links);
$totalRows_rs_links = mysql_num_rows($rs_links);
?>
<html>
<head>
<title>Echelon - B3 Repository Tool (by xlr8or)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
@import url("css/default.css");
-->
</style>
</head>

<body class="tabelinhoud" text="#FFFFFF" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<?php require_once('login/inc_loggedin.php'); ?>
<table width="100%" class="tabelinhoud" cellspacing="0" cellpadding="0">
  <tr> 
    <td colspan="2" class="navigatie">Welcome to Echelon, the B3 repository webfront.</td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="2">These are the links available for your level:</td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
  <?php do { ?>
  <tr> 
    <td width="17%">&#8226; 
      <a href="<?php echo $row_rs_links['link']; ?>"><?php echo $row_rs_links['name']; ?></a></td>
    <td width="83%"><?php echo $row_rs_links['description']; ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php } while ($row_rs_links = mysql_fetch_assoc($rs_links)); ?>
</table>
<?php include "footer.php"; ?>
</body>
</html>
<?php
mysql_free_result($rs_links);
?>

