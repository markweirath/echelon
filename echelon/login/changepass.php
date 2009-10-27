<?php include "../ctracker.php"; ?>
<?php // Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3; ?>
<?php require_once('../login/inc_authorize.php'); ?>
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
  $updateSQL = sprintf("UPDATE users SET password=%s WHERE id=%s",
                       GetSQLValueString(md5($_POST['pass1']), "text"),
                       GetSQLValueString($_POST['id'], "int"));
  mysql_select_db($database_wwwvalidate, $wwwvalidate);
  $Result1 = mysql_query($updateSQL, $wwwvalidate) or die(mysql_error());
  $updateGoTo = "../links.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
$colname_rs_users = "1";
if (isset($_SESSION['xlradmin'])) {
  $colname_rs_users = (get_magic_quotes_gpc()) ? $_SESSION['xlradmin'] : addslashes($_SESSION['xlradmin']);
}
mysql_select_db($database_wwwvalidate, $wwwvalidate);
$query_rs_users = sprintf("SELECT * FROM users WHERE username = '%s'", $colname_rs_users);
$rs_users = mysql_query($query_rs_users, $wwwvalidate) or die(mysql_error());
$row_rs_users = mysql_fetch_assoc($rs_users);
$totalRows_rs_users = mysql_num_rows($rs_users);
?>
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
function tmt_compareField(f1,f2,rule,errorMsg){
var myErr = "";
if(eval("MM_findObj('"+f1+"').value"+rule+"MM_findObj('"+f2+"').value")){
alert(unescape(errorMsg));myErr += 'errorMsg';}
document.MM_returnValue = (myErr == "");
}
function MM_findObj(n, d) { //v4.01
var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function MM_validateForm() { //v4.0
var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
if (val) { nm=val.name; if ((val=val.value)!="") {
if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
} else if (test!='R') { num = parseFloat(val);
if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
min=test.substring(8,p); max=test.substring(p+1);
if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
} } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
} if (errors) alert('The following error(s) occurred:\n'+errors);
document.MM_returnValue = (errors == '');
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
            Change your user password 
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
                    <?php echo $row_rs_users['username']; ?>
                  </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap align="right">
                    Echelon level:
                  </td>
                  <td>
                    <?php echo $row_rs_users['ech_level']; ?>
                  </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap align="right">
                    New password:
                  </td>
                  <td>
                    <input name="pass1" type="password" id="pass1">
                  </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap align="right">
                    Retype password 
                  </td>
                  <td>
                    <input name="pass2" type="password" id="pass2">
                  </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap align="right">
                    &nbsp;
                  </td>
                  <td>
                    <input name="submit" type="submit" onClick="tmt_compareField('pass1','pass2','!=','The%20passwords%20didn%27t%20match');MM_validateForm('pass1','','R','pass2','','R');return document.MM_returnValue" value="Change password">
                  </td>
                </tr>
                <tr valign="baseline">
                  <td nowrap align="right">
                    &nbsp;
                  </td>
                  <td>
                    <a href="../links.php" onClick="tmt_winControl('self','close()')" class="navigatie">Cancel</a>
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
