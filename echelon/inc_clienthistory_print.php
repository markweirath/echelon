<?php include "ctracker.php"; ?>
<?php require_once('Connections/b3connect.php'); ?>
<?php require_once('Connections/inc_config.php'); ?>
<?php
$colname_rs_clientbans = "6490";
if (isset($_GET['id'])) {
  $colname_rs_clientbans = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_b3connect, $b3connect);
/*$query_rs_clientbans = sprintf("(SELECT penalties.id, penalties.type, penalties.client_id, penalties.time_add, penalties.time_expire, penalties.reason, penalties.data, penalties.inactive, penalties.duration, admin.id as admi_id, admin.name as admi_name FROM penalties, clients as admin WHERE penalties.client_id = %s AND penalties.admi_id = admin.id) UNION (SELECT penalties.id, penalties.type, penalties.client_id, penalties.time_add, penalties.time_expire, penalties.reason, penalties.data, penalties.inactive, penalties.duration, '1' as admi_id, 'B3' as admi_name FROM penalties, clients as admin WHERE penalties.client_id = %s AND penalties.admi_id = '0') ORDER BY id DESC", $colname_rs_clientbans,$colname_rs_clientbans);*/
// Next contrib done by Senator to optimize above query
$query_rs_clientbans = sprintf("SELECT 
                        T1.id, 
                        T1.type, 
                        T1.client_id, 
                        T1.time_add, 
                        T1.time_expire, 
                        T1.reason, 
                        T1.data, 
                        T1.inactive, 
                        T1.duration, 
                        COALESCE(T2.id,'1') as admi_id, 
                        COALESCE(T2.name, 'B3') as admi_name 
                        
                        FROM penalties T1
                        
                        LEFT JOIN clients T2
                        ON T2.id = T1.admin_id
                        
                        WHERE T1.client_id = %s
                        ORDER BY id DESC", $colname_rs_clientbans);
// End fix Senator
$rs_clientbans = mysql_query($query_rs_clientbans, $b3connect) or die(mysql_error());
$row_rs_clientbans = mysql_fetch_assoc($rs_clientbans);
$totalRows_rs_clientbans = mysql_num_rows($rs_clientbans);
?>
<?php if ($totalRows_rs_clientbans > 0) { // Show if recordset not empty ?>
<br>
<br>
<table width="100%" border="0" cellpadding="1" cellspacing="1" class="tabelinhoud">
  <tr class="tabelinhoud">
    <td><strong>Penalties</strong></td>
    <td>&nbsp;</td>
  </tr>
  <?php do { ?>
  <tr class="tabelinhoud">
    <td width="250" valign="top">
      <?php if ($row_rs_clientbans['time_add'] != (0)) { ?>
      <?php echo date('l, d/m/Y (H:i)',$row_rs_clientbans['time_add']); ?>
      <?php } /* if ($row_rs_clientbans['time_add'] != (0)) */ ?>
      <?php if ($row_rs_clientbans['time_add'] == (0)) { ?>
      <?php echo $row_rs_clientbans['duration']; ?>
      mins.
<?php }
/* if ($row_rs_clientbans['time_add'] == (0)) */
      ?>    </td>
    <td  valign="top">
      <?php echo preg_replace('/\\^([0-9])/ie', '', $row_rs_clientbans['reason']); ?>
      <br>
      <i>( 
        <?php echo $row_rs_clientbans['data']; ?>
        )</i>    </td>
  </tr>
  <?php } while ($row_rs_clientbans = mysql_fetch_assoc($rs_clientbans)); ?>
</table>
<?php } // Show if recordset not empty ?>
<?php
mysql_free_result($rs_clientbans);
?>
