<?php
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');

$colname_rs_clientinfo = "1";
if (isset($_GET['id'])) {
  $colname_rs_clientinfo = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_clientinfo = sprintf("SELECT * FROM clients WHERE id = %s", $colname_rs_clientinfo);
$rs_clientinfo = mysql_query($query_rs_clientinfo, $b3connect) or die(mysql_error());
$row_rs_clientinfo = mysql_fetch_assoc($rs_clientinfo);
$totalRows_rs_clientinfo = mysql_num_rows($rs_clientinfo);
$colname_rs_aliases = "0";
if (isset($_GET['id'])) {
  $colname_rs_aliases = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_aliases = sprintf("SELECT * FROM aliases WHERE client_id = %s ORDER BY num_used DESC", $colname_rs_aliases);
$rs_aliases = mysql_query($query_rs_aliases, $b3connect) or die(mysql_error());
$row_rs_aliases = mysql_fetch_assoc($rs_aliases);
$totalRows_rs_aliases = mysql_num_rows($rs_aliases);
?>
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
  </head>
  <body>
    <div id="wrapper">
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr class="tabelinhoud">
          <td width="9%" align="right">
            Name:          </td>
          <td width="35%">
            <?php echo htmlspecialchars($row_rs_clientinfo['name']); ?>
          </td>
          <td width="12%" align="right">
            IP:          </td>
          <td width="44%">
            <?php if ($row_rs_clientinfo['ip'] != "") { ?>
              <?php echo $row_rs_clientinfo['ip']; ?>
            <?php } else { ?>
            (No IP address available)
            <?php } ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td align="right">
            Client id:          </td>
          <td>
            @ 
            <?php echo $row_rs_clientinfo['id']; ?>
          </td>
          <td align="right">
            Connections:          </td>
          <td>
            <?php echo $row_rs_clientinfo['connections']; ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td align="right">
            group_bits          </td>
          <td>
            (
            <?php echo $row_rs_clientinfo['group_bits']; ?>
            )          </td>
          <td align="right">
            First seen:          </td>
          <td>
            <?php echo date('l, d/m/Y (H:i)',$row_rs_clientinfo['time_add']); ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td align="right">
            GUID:          </td>
          <td>
            <?php if ($row_rs_clientinfo['guid'] != $row_rs_clientinfo['pbid']) { ?>
              <?php echo $row_rs_clientinfo['guid']; ?>
            <?php } else { ?>
            (No GUID available)
            <?php } ?>
          </td>
          <td align="right">
            Last seen:          </td>
          <td>
            <?php echo date('l, d/m/Y (H:i)',$row_rs_clientinfo['time_edit']); ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td align="right">
            PB-GUID          </td>
          <td>
            <?php if (($row_rs_clientinfo['pbid'] != "") && ($row_rs_clientinfo['pbid'] != "WORLD")) { ?>
              <?php echo $row_rs_clientinfo['pbid']; ?>
            <?php } else { ?>
            (No Punkbuster GUID available)
            <?php } ?>
          </td>
          <td align="right">
            Greeting:          </td>
          <td>
            <?php echo $row_rs_clientinfo['greeting']; ?>
          </td>
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="1" cellspacing="1" class="tabelinhoud">
        <tr>
          <td>
            <br /><strong>Used Aliasses:</strong></td>
        </tr>
        <tr>
          <td class="tabelinhoud">
            <?php do { ?>
            <?php echo htmlspecialchars($row_rs_aliases['alias']); ?>
            &nbsp;
            <i>(
              <?php echo $row_rs_aliases['num_used']; ?>
              x)</i>
            &nbsp;&nbsp;-&nbsp;&nbsp;
            <?php } while ($row_rs_aliases = mysql_fetch_assoc($rs_aliases)); ?>
          </td>
        </tr>
      </table>
      <?php include('inc_clienthistory_print.php'); ?>
  </div>
  </body>
</html>
<?php
mysql_free_result($rs_clientinfo);
mysql_free_result($rs_aliases);
?>
