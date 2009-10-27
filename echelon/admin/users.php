<?php include "../ctracker.php"; ?>
<?php // Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 1; ?>
<?php require_once('../Connections/wwwvalidate.php'); ?>
<?php require_once('../login/inc_authorize.php'); ?>
<?php require_once('../Connections/inc_config.php'); ?>
<?php function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "resetpass")) {
  $updateSQL = sprintf("UPDATE users SET password=%s WHERE id=%s",
                       GetSQLValueString($_POST['standardpass'], "text"),
                       GetSQLValueString($_POST['id2'], "int"));
  mysql_select_db($database_wwwvalidate, $wwwvalidate);
  $Result1 = mysql_query($updateSQL, $wwwvalidate) or die(mysql_error());
}
if ((isset($_POST['id'])) && ($_POST['id'] != "") && ($_POST['id'] != "1")) {
  $deleteSQL = sprintf("DELETE FROM users WHERE id=%s",
                       GetSQLValueString($_POST['id'], "int"));
  mysql_select_db($database_wwwvalidate, $wwwvalidate);
  $Result1 = mysql_query($deleteSQL, $wwwvalidate) or die(mysql_error());
}
mysql_select_db($database_wwwvalidate, $wwwvalidate);
$query_rs_users = "SELECT * FROM users";
$rs_users = mysql_query($query_rs_users, $wwwvalidate) or die(mysql_error());
$row_rs_users = mysql_fetch_assoc($rs_users);
$totalRows_rs_users = mysql_num_rows($rs_users);
?>
<html>
  <head>
    <title>
      Echelon - B3 Repository Tool (by xlr8or)
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <style type="text/css">
      <!--
      @import url(../css/default.css);
      -->
    </style>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
window.open(theURL,winName,features);
}
//-->
</script>
  </head>
  <body>
    <div id="wrapper">
      <?php require_once('../login/inc_loggedin.php'); ?>
      <?php include('../Connections/inc_codnav.php'); ?>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="3" class="tabelinhoud">
            Modify or remove echelon users. 
            <a href="#" onClick="MM_openBrWindow('adduser.php','edituser','resizable=yes,width=640,height=480')">To add a new user click here.</a>
          </td>
        </tr>
        <tr>
          <td colspan="3" class="tabelinhoud">
            &nbsp;
          </td>
        </tr>
        <tr>
          <td colspan="3">
            <table width="100%"  border="0" cellpadding="1" cellspacing="1">
              <tr class="tabelkop">
                <td width="25%">
                  loginname
                </td>
                <td width="25%">
                  echelon level 
                </td>
                <td width="25%">
                  reset  password to 'echelon'? 
                </td>
                <td width="25%">
                  be carefull :) 
                </td>
              </tr>
              <?php do { ?>
              <tr class="tabelinhoud">
                <td>
                  <a href="#" onClick="MM_openBrWindow('useredit.php?id=<?php echo $row_rs_users['id']; ?>','edit','resizable=yes,width=640,height=480')">
                    <?php echo $row_rs_users['username']; ?></a>
                </td>
                <td>
                  <?php echo $row_rs_users['ech_level']; ?>
                </td>
                <td>
                  <form action="<?php echo $editFormAction; ?>" method="POST" name="resetpass" id="resetpass">
                    <input name="id2" type="hidden" id="id4" value="<?php echo $row_rs_users['id']; ?>">
                    <input type="submit" name="Submit2" value="reset password">
                    <input name="standardpass" type="hidden" id="standardpass" value="<?php echo md5('echelon')?>">
                    <input type="hidden" name="MM_update" value="resetpass">
                  </form>
                </td>
                <td>
                  <form action="users.php" method="post" name="userdeletion" id="userdeletion">
                    <input name="id" type="hidden" id="id3" value="<?php echo $row_rs_users['id']; ?>">
                    <input type="submit" name="Submit" value="remove user">
                  </form>
                </td>
              </tr>
              <?php } while ($row_rs_users = mysql_fetch_assoc($rs_users)); ?>
              <tr class="tabelonderschrift">
                <td>
                  &nbsp;
                </td>
                <td>
                  &nbsp;
                </td>
                <td>
                  &nbsp;
                </td>
                <td>
                  &nbsp;
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>
<?php
mysql_free_result($rs_users);
?>
