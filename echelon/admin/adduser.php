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
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "useraddition")) {
  $insertSQL = sprintf("INSERT INTO users (username, password, ech_level, b3cod, b3uo) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString(md5($_POST['password']), "text"),
                       GetSQLValueString($_POST['ech_level'], "int"),
                       GetSQLValueString($_POST['b3cod'], "int"),
                       GetSQLValueString($_POST['b3uo'], "int"));
  mysql_select_db($database_wwwvalidate, $wwwvalidate);
  $Result1 = mysql_query($insertSQL, $wwwvalidate) or die(mysql_error());
  $insertGoTo = "../admin/done-close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
mysql_select_db($database_wwwvalidate, $wwwvalidate);
$query_rs_users = "SELECT * FROM users";
$rs_users = mysql_query($query_rs_users, $wwwvalidate) or die(mysql_error());
$row_rs_users = mysql_fetch_assoc($rs_users);
$totalRows_rs_users = mysql_num_rows($rs_users);
?>
<?php require_once('../login/inc_authorize.php'); ?>
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
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="3" class="tabeluitleg">
            Add a new Echelon user 
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
              <tr>
                <td colspan="3">
                  <form action="<?php echo $editFormAction; ?>" method="POST" name="useraddition" id="useraddition">
                    <table width="100%" align="left" class="tabelkop">
                      <tr valign="baseline">
                        <td nowrap align="right">
                          Username:
                        </td>
                        <td>
                          <input type="text" name="username" size="32">
                        </td>
                      </tr>
                      <tr valign="baseline">
                        <td height="31" align="right" nowrap>
                          Password:
                        </td>
                        <td>
                          <input type="text" name="password" value="echelon" size="32">
                        </td>
                      </tr>
                      <tr valign="baseline">
                        <td nowrap align="right">
                          Echelon level:
                        </td>
                        <td>
                          <select name="ech_level" id="ech_level">
                            <option value="1">
                            1: superadmin
                            </option>
                            <option value="2">
                            2: admin
                            </option>
                            <option value="3" selected>
                            3: moderator
                            </option>
                          </select>
                          [1: add/remove echelon users] [2: edit gameclients] [3: investigate only] 
                        </td>
                      </tr>
                      <!--            <tr valign="baseline">
                      <td nowrap align="right">B3cod:</td>
                      <td><input type="text" name="b3cod" value="0" size="32"></td>
                      </tr>
                      <tr valign="baseline">
                      <td nowrap align="right">B3uo:</td>
                      <td><input type="text" name="b3uo" value="0" size="32"></td>
                      </tr>
                      -->
                      <tr valign="baseline">
                        <td nowrap align="right">
                          &nbsp;
                        </td>
                        <td>
                          <input name="submit" type="submit" onClick="MM_validateForm('username','','R','password','','R');return document.MM_returnValue" value="add user">
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
                    <input type="hidden" name="MM_insert" value="useraddition">
                  </form>
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
