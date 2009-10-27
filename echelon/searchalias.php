<?php
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');

$colname_rs_aliassearch = "asdfhjkalsdflkjasdhf";
if (isset($_GET['search'])) {
  $colname_rs_aliassearch = (get_magic_quotes_gpc()) ? $_GET['search'] : addslashes($_GET['search']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_aliassearch = sprintf("SELECT * FROM aliases WHERE `alias` like '%%%s%%'", $colname_rs_aliassearch);
$rs_aliassearch = mysql_query($query_rs_aliassearch, $b3connect) or die(mysql_error());
$row_rs_aliassearch = mysql_fetch_assoc($rs_aliassearch);
$totalRows_rs_aliassearch = mysql_num_rows($rs_aliassearch);
?>
<?php require_once('login/inc_authorize.php'); ?>
<html>
  <head>
    <title>
      Echelon - B3 Repository Tool (by xlr8or)
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <style type="text/css">
      <!--
      @import url("css/default.css");
      -->
    </style>
<script language="JavaScript" type="text/JavaScript">
<!--
function tmt_winLoad(id,u){
var d=eval(id)==null||eval(id+".closed");
if(!d){eval(id+".location.href='"+u+"'")}
}
function tmt_winControl(id,c){
var d=eval(id)==null||eval(id+".closed");
if(!d){eval(id+"."+c);}
}
//-->
</script>
  </head>
  <body>
    <table width="100%" border="0" cellpadding="1" cellspacing="1">
      <tr class="tabelkop">
        <td>
          Alias
        </td>
        <td>
          belongs to
        </td>
        <td>
          # used
        </td>
        <td>
          first used
        </td>
        <td>
          last used
        </td>
      </tr>
      <?php do { ?>
      <tr class="tabelinhoud">
        <td>
          <a href="#" onClick="tmt_winLoad('opener','clientdetails.php?game=<?php echo $game; ?>&amp;id=<?php echo $row_rs_aliassearch['client_id']; ?>');tmt_winControl('self','close()')">
            <?php echo $row_rs_aliassearch['alias']; ?></a>
        </td>
        <td>
          @ 
          <?php echo $row_rs_aliassearch['client_id']; ?>
        </td>
        <td>
          <?php echo $row_rs_aliassearch['num_used']; ?>
        </td>
        <td>
          <?php echo date('l, d/m/Y (H:i)',$row_rs_aliassearch['time_add']); ?>
        </td>
        <td>
          <?php echo date('l, d/m/Y (H:i)',$row_rs_aliassearch['time_edit']); ?>
        </td>
      </tr>
      <?php } while ($row_rs_aliassearch = mysql_fetch_assoc($rs_aliassearch)); ?>
      <tr>
        <td>
          &nbsp;
        </td>
        <td>
          &nbsp;
        </td>
        <td>
        </td>
        <td>
          &nbsp;
        </td>
        <td>
          <a href="#" onClick="tmt_winControl('self','close()')">Close window</a>
        </td>
      </tr>
    </table>
    <?php include "footer.php"; ?>
  </body>
</html>
<?php
mysql_free_result($rs_aliassearch);
?>
