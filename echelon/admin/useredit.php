<?php include "../ctracker.php"; ?>
<?php // Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 1; ?>
<?php require_once('../Connections/wwwvalidate.php'); ?>
<?php require_once('../Connections/inc_config.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
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
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "updateuser")) {
  $updateSQL = sprintf("UPDATE users SET username=%s, ech_level=%s, b3cod=%s, b3uo=%s WHERE id=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['ech_level'], "int"),
                       GetSQLValueString(isset($_POST['b3cod']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['b3uo']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['id'], "int"));
  mysql_select_db($database_wwwvalidate, $wwwvalidate);
  $Result1 = mysql_query($updateSQL, $wwwvalidate) or die(mysql_error());
  $updateGoTo = "done-close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
$colname_rs_users = "0";
if (isset($_GET['id'])) {
  $colname_rs_users = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_wwwvalidate, $wwwvalidate);
$query_rs_users = sprintf("SELECT * FROM users WHERE id = %s", $colname_rs_users);
$rs_users = mysql_query($query_rs_users, $wwwvalidate) or die(mysql_error());
$row_rs_users = mysql_fetch_assoc($rs_users);
$totalRows_rs_users = mysql_num_rows($rs_users);
?>
<?php require_once('../login/inc_authorize.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
function tmt_winControl(id,c){
var d=eval(id)==null||eval(id+".closed");
if(!d){eval(id+"."+c);}
}
//-->
</script>
  </head>
  <body>
    <div id="wrapper">
      <?php require_once('../login/inc_loggedin.php'); ?>
      <?php include('../Connections/inc_codnav.php'); ?>
      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td class="tabeluitleg">
            Update Echelon user
          </td>
        </tr>
        <tr>
          <td class="tabelinhoud">
            &nbsp;
          </td>
        </tr>
        <tr>
          <td>
            <form action="<?php echo $editFormAction; ?>" method="POST" name="updateuser" id="updateuser">
              <table align="center" class="tabelkop">
                <tr valign="baseline">
                  <td nowrap align="right">
                    Username:
                  </td>
                  <td>
                    <input type="text" name="username" value="<?php echo $row_rs_users['username']; ?>" size="32">
                  </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap align="right">
                    Echelon level:
                  </td>
                  <td>
                    <select name="ech_level">
                      <option value="1" <?php if (!(strcmp($row_rs_users['ech_level'], 1))) {echo "SELECTED";} ?>>
                      1: superadmin
                      </option>
                      <option value="2" <?php if (!(strcmp($row_rs_users['ech_level'], 2))) {echo "SELECTED";} ?>>
                      2: admin
                      </option>
                      <option value="3" <?php if (!(strcmp($row_rs_users['ech_level'], 3))) {echo "SELECTED";} ?>>
                      3: moderator
                      </option>
                    </select>
                  </td>
                </tr>
                <!--        <tr valign="baseline">
                <td nowrap align="right">B3cod:</td>
                <td><input type="checkbox" name="b3cod" value=""  <?php if (!(strcmp($row_rs_users['b3cod'],1))) {echo "checked";} ?>></td>
                </tr>
                <tr valign="baseline">
                <td nowrap align="right">B3uo:</td>
                <td><input type="checkbox" name="b3uo" value=""  <?php if (!(strcmp($row_rs_users['b3uo'],1))) {echo "checked";} ?>></td>
                </tr>
                -->
                <tr valign="baseline">
                  <td nowrap align="right">
                    &nbsp;
                  </td>
                  <td>
                    <input name="submit" type="submit" value="Update record">
                  </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap align="right">
                    &nbsp;
                  </td>
                  <td>
                    <a href="#" onClick="tmt_winControl('self','close()')" class="navigatie">Cancel</a>
                  </td>
                </tr>
              </table>
              <input type="hidden" name="id" value="<?php echo $row_rs_users['id']; ?>">
              <input type="hidden" name="MM_update" value="updateuser">
            </form>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>
<?php
mysql_free_result($rs_users);
?>
