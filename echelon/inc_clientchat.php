<?php include "ctracker.php"; ?>
<?php require_once('Connections/b3connect.php'); ?>
<?php require_once('Connections/inc_config.php'); ?>
<?php
$colname_rs_clientchat = "6490";
if (isset($_GET['id'])) {
  $colname_rs_clientchat = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_clientchat = sprintf('SELECT  * FROM `chatlog` WHERE `client_id` = %s ORDER BY `msg_time` DESC LIMIT 0, 50', $colname_rs_clientchat);
$rs_clientchat = mysql_query($query_rs_clientchat, $b3connect) or die(mysql_error());
$row_rs_clientchat = mysql_fetch_assoc($rs_clientchat);
$totalRows_rs_clientchat = mysql_num_rows($rs_clientchat);
?>
<?php if ($totalRows_rs_clientchat > 0) { // Show if recordset not empty ?>
<br>
<br>
<table width="100%" border="0" cellpadding="1" cellspacing="1">
  <tr>
    <td colspan="7" class="tabelkop">
      Client chat History. <a href="clientchat_print.php?id=<?php echo $colname_rs_clientchat; ?>&game=<?php echo $game; ?>" target=_blank><img src="img/print_icon.gif" alt="printversion" width="11" height="9" border="0" /></a></td>
  </tr>
  <tr>
    <td class="tabelkop" width="215">&nbsp;</td>
    <td class="tabelkop" width="200">&nbsp;</td>
    <td class="tabelkop">&nbsp;</td>
  </tr>
  <?php do { ?>
  <tr class="tabelinhoud">
    <td>
      <?php echo date('l, d/m/Y (H:i)',$row_rs_clientchat['msg_time']); ?>
    </td>
    <td>
      <?php echo htmlentities($row_rs_clientchat['client_name']); ?> says to 
      <?php 
        switch ($row_rs_clientchat['msg_type']) {
          case 'ALL':
            echo 'all';
          break;
          case 'TEAM':
            echo 'team '.$row_rs_clientchat['client_team'];
          break;
          case 'PM':
            echo '<a href="' . $path . 'clientdetails.php?game=' . $game . '&id=' . $row_rs_clientchat['target_id'] . '&game='.$game.'">';
            echo htmlentities($row_rs_clientchat['target_name']) . '</a>';
          break;
        }; 
      ?>
    </td>
    <td>
      <?php echo htmlentities($row_rs_clientchat['msg']); ?>
    </td>

  </tr>
  <?php } while ($row_rs_clientchat = mysql_fetch_assoc($rs_clientchat)); ?>
  <tr class="tabelinhoud" class="tabelonderschrift">
    <td>&nbsp;
    </td>
    <td>&nbsp;
    </td>
    <td>&nbsp;
    </td>
  </tr>
</table>
<?php } // Show if recordset not empty ?>
<?php
mysql_free_result($rs_clientchat);
?>
