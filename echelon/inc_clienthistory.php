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
<table width="100%" border="0" cellpadding="1" cellspacing="1">
  <tr>
    <td colspan="7" class="tabelkop">
      Client History. (All Bans, TempBans, Kicks and Warnings)    <a href="clientdetails_print.php?id=<?php echo $colname_rs_clientbans; ?>&game=<?php echo $game; ?>" target=_blank><img src="img/print_icon.gif" alt="printversion" width="11" height="9" border="0" /></a></td>
  </tr>
  <tr>
    <td class="tabelkop">
      penalty
    </td>
    <td class="tabelkop">
      penalty-type
    </td>
    <td class="tabelkop">
      inactive
    </td>
    <td class="tabelkop">
      added/duration
    </td>
    <td class="tabelkop">
      expires
    </td>
    <td width="200" class="tabelkop">
      reason
    </td>
    <td class="tabelkop">
      admin
    </td>
  </tr>
  <?php do { ?>
  <tr class="tabelinhoud">
    <td>
      <?php echo $row_rs_clientbans['id']; ?>
      &nbsp;&nbsp;
      <a href="admin/unban.php?banid=<?php echo $row_rs_clientbans['id']; ?>&pbid=<?php echo $row_rs_clientinfo['pbid']; ?>&type=<?php echo $row_rs_clientbans['type']; ?>&game=<?php echo $game; ?>">
        <img src="img/remove.gif" alt="de-activate / unban" title="de-activate / unban" width="16" height="15" border="0" align="absmiddle"></a>
    </td>
    <td>
      <?php echo $row_rs_clientbans['type']; ?>
    </td>
    <td>
      <?php echo $row_rs_clientbans['inactive']; ?>
    </td>
    <td>
      <?php if ($row_rs_clientbans['time_add'] != (0)) { ?>
      <?php echo date('l, d/m/Y (H:i)',$row_rs_clientbans['time_add']); ?>
      <?php } /* if ($row_rs_clientbans['time_add'] != (0)) */ ?>
      <?php if ($row_rs_clientbans['time_add'] == (0)) { ?>
      <?php echo $row_rs_clientbans['duration']; ?>
      mins.
<?php }
/* if ($row_rs_clientbans['time_add'] == (0)) */
      ?>
    </td>
    <td>
<?php 
if ($row_rs_clientbans['type'] == 'Notice'){
	 echo "<span class=\"inactive\">Notice added by Admin</span>"; }
else 

if ($row_rs_clientbans['inactive'] == (0)) {
	if ($row_rs_clientbans['time_expire'] != (0)) { 
		if (($row_rs_clientbans['time_expire'] <= time()) && ($row_rs_clientbans['time_expire'] != -1)) {
		  echo "<span class=\"expired\">".date('l, d/m/Y (H:i)',$row_rs_clientbans['time_expire'])."</span>"; }
		if ($row_rs_clientbans['time_expire'] == -1) {
		  echo "<span class=\"permanent\">permanent</span>"; }
		if ($row_rs_clientbans['time_expire'] > time()) {
		  echo "<span class=\"active\">".date('l, d/m/Y (H:i)',$row_rs_clientbans['time_expire'])."</span>"; }
		}
	 }
else { echo "<span class=\"inactive\">de-activated/unbanned</span>"; }
      if ($row_rs_clientbans['time_expire'] == (0)) { ?>
      (kick only)

<?php }
/* if ($row_rs_clientbans['time_expire'] == (NULL)) */
      ?>
    </td>
    <td width="200">
      <?php echo preg_replace('/\\^([0-9])/ie', '', $row_rs_clientbans['reason']); ?>
      <br>
      <i>( 
        <?php echo $row_rs_clientbans['data']; ?>
        )</i>
    </td>
    <td>
      <a href="<?php echo $path; ?>clientdetails.php?game=<?php echo $game; ?>&id=<?php echo $row_rs_clientbans['admi_id']; ?>">
        <?php echo htmlspecialchars($row_rs_clientbans['admi_name']); ?></a>
    </td>
  </tr>
  <?php } while ($row_rs_clientbans = mysql_fetch_assoc($rs_clientbans)); ?>
  <tr class="tabelinhoud" class="tabelonderschrift">
    <td>&nbsp;
      
    </td>
    <td>&nbsp;
      
    </td>
    <td>&nbsp;
      
    </td>
    <td>&nbsp;
      
    </td>
    <td>
      <span class="permanent">
        [permban]
      </span>
      <span class="active">
        [active ban]
      </span>
      <span class="expired">
        [expired ban/warn]
      </span>
    </td>
    <td width="200">&nbsp;
      
    </td>
    <td>
      click admin to see details
    </td>
  </tr>
</table>
<?php } // Show if recordset not empty ?>
<?php
mysql_free_result($rs_clientbans);
?>
