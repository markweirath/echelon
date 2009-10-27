<?php
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rs_activebans = 25;
$pageNum_rs_activebans = 0;
if (isset($_GET['pageNum_rs_activebans'])) {
  $pageNum_rs_activebans = $_GET['pageNum_rs_activebans'];
}
$startRow_rs_activebans = $pageNum_rs_activebans * $maxRows_rs_activebans;

$xlorderby_rs_activebans = "id";
if (isset($_GET['orderby'])) {
  $xlorderby_rs_activebans = (get_magic_quotes_gpc()) ? $_GET['orderby'] : addslashes($_GET['orderby']);
}
$xlorder_rs_activebans = "DESC";
if (isset($_GET['order'])) {
  $xlorder_rs_activebans = (get_magic_quotes_gpc()) ? $_GET['order'] : addslashes($_GET['order']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_activebans = sprintf("SELECT penalties.id, penalties.type, penalties.time_add, penalties.time_expire, penalties.reason, penalties.inactive, penalties.duration, target.id as target_id, target.name as target_name, admin.id as admi_id, admin.name as admi_name FROM penalties, clients as admin, clients as target WHERE admin_id != '0' AND (penalties.type = 'Ban' OR penalties.type = 'TempBan') AND inactive = 0 AND penalties.time_expire <> 0 AND penalties.client_id = target.id AND penalties.admin_id = admin.id ORDER BY %s %s", $xlorderby_rs_activebans,$xlorder_rs_activebans);
$query_limit_rs_activebans = sprintf("%s LIMIT %d, %d", $query_rs_activebans, $startRow_rs_activebans, $maxRows_rs_activebans);
$rs_activebans = mysql_query($query_limit_rs_activebans, $b3connect) or die(mysql_error());
$row_rs_activebans = mysql_fetch_assoc($rs_activebans);

if (isset($_GET['totalRows_rs_activebans'])) {
  $totalRows_rs_activebans = $_GET['totalRows_rs_activebans'];
} else {
  $all_rs_activebans = mysql_query($query_rs_activebans);
  $totalRows_rs_activebans = mysql_num_rows($all_rs_activebans);
}
$totalPages_rs_activebans = ceil($totalRows_rs_activebans/$maxRows_rs_activebans)-1;

$queryString_rs_activebans = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rs_activebans") == false && 
        stristr($param, "totalRows_rs_activebans") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rs_activebans = "&" . implode("&", $newParams);
  }
}
$queryString_rs_activebans = sprintf("&totalRows_rs_activebans=%d%s", $totalRows_rs_activebans, $queryString_rs_activebans);
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

<body>
<div id="wrapper">
<?php require_once('login/inc_loggedin.php'); ?>
<?php include('Connections/inc_codnav.php'); ?>
<table width="100%" class="tabeluitleg" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><strong>Admin bans.</strong><br>
      You are viewing the bans/tempbans issued by admins. Clients can only reconnect
        when the ban has expired,  never when a ban is permanent.</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="1" cellspacing="1">
  <tr>
    <td class="tabelkop">client&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=target_name&order=ASC"><img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=target_name&order=DESC"><img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a></td>
    <td class="tabelkop">type&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=type&order=ASC"><img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=type&order=DESC"><img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a></td>
    <td class="tabelkop">added&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=time_add&order=ASC"><img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=time_add&order=DESC"><img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a></td>
    <td class="tabelkop">expires&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=time_expire&order=ASC"><img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=time_expire&order=DESC"><img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a></td>
    <td width="200" class="tabelkop">reason&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=reason&order=ASC"><img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=reason&order=DESC"><img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a></td>
    <td class="tabelkop">admin&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=admi_name&order=ASC"><img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>&nbsp;<a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=admi_name&order=DESC"><img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a></td>
  </tr>
  <?php do { ?>
  <tr class="tabelinhoud">
    <td title="ban id : <?=$row_rs_activebans['id']?>"><a href="clientdetails.php?game=<?php echo $game; ?>&id=<?php echo $row_rs_activebans['target_id']; ?>"><?php echo htmlspecialchars($row_rs_activebans['target_name']); ?></a></td>
    <td><?php echo $row_rs_activebans['type']; ?></td>
    <td><?php echo date('l, d/m/Y (H:i)',$row_rs_activebans['time_add']); ?></td>
    <td><?php 
			if (($row_rs_activebans['time_expire'] <= time()) && ($row_rs_activebans['time_expire'] != -1)) {
			  echo "<span class=\"expired\">".date('l, d/m/Y (H:i)',$row_rs_activebans['time_expire'])."</span>"; }
			if ($row_rs_activebans['time_expire'] == -1) {
			  echo "<span class=\"permanent\">permanent</span>"; }
			if ($row_rs_activebans['time_expire'] > time()) {
			  echo "<span class=\"active\">".date('l, d/m/Y (H:i)',$row_rs_activebans['time_expire'])."</span>"; }
			?></td>
    <td width="200"><?php echo preg_replace('/\\^([0-9])/ie', '', $row_rs_activebans['reason']); ?></td>
    <td><a href="clientdetails.php?game=<?php echo $game; ?>&id=<?php echo $row_rs_activebans['admi_id']; ?>"><?php echo htmlspecialchars($row_rs_activebans['admi_name']); ?></a></td>
  </tr>
  <?php } while ($row_rs_activebans = mysql_fetch_assoc($rs_activebans)); ?>
  <tr class="tabelonderschrift">
    <td>click client to see details</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><span class="expired">[expired ban]</span> <span class="active">[active
        ban]</span> <span class="permanent">[permban]</span></td>
    <td width="200">&nbsp;</td>
    <td>click admin to see details</td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center" class="recordnavigatie">
  <tr class="tabelkop">
    <td width="100%" colspan="4" align="center">Records:&nbsp;<?php echo ($startRow_rs_activebans + 1) ?>&nbsp;to&nbsp;<?php echo min($startRow_rs_activebans + $maxRows_rs_activebans, $totalRows_rs_activebans) ?>&nbsp;from&nbsp;<?php echo $totalRows_rs_activebans ?> </td>
  </tr>
  <tr>
    <td align="center" width="25%">
      <?php if ($pageNum_rs_activebans > 0) { // Show if not first page ?>
      <a href="<?php printf("%25s?pageNum_rs_activebans=%25d%25s", $currentPage, 0, $queryString_rs_activebans); ?>">First</a>
      <?php } // Show if not first page ?>
    </td>
    <td align="center" width="25%">
      <?php if ($pageNum_rs_activebans > 0) { // Show if not first page ?>
      <a href="<?php printf("%25s?pageNum_rs_activebans=%25d%25s", $currentPage, max(0, $pageNum_rs_activebans - 1), $queryString_rs_activebans); ?>">Previous</a>
      <?php } // Show if not first page ?>
    </td>
    <td align="center" width="25%">
      <?php if ($pageNum_rs_activebans < $totalPages_rs_activebans) { // Show if not last page ?>
      <a href="<?php printf("%25s?pageNum_rs_activebans=%25d%25s", $currentPage, min($totalPages_rs_activebans, $pageNum_rs_activebans + 1), $queryString_rs_activebans); ?>">Next</a>
      <?php } // Show if not last page ?>
    </td>
    <td align="center" width="25%">
      <?php if ($pageNum_rs_activebans < $totalPages_rs_activebans) { // Show if not last page ?>
      <a href="<?php printf("%25s?pageNum_rs_activebans=%25d%25s", $currentPage, $totalPages_rs_activebans, $queryString_rs_activebans); ?>">Last</a>
      <?php } // Show if not last page ?>
    </td>
  </tr>
</table>
<?php include "footer.php"; ?>
</div>
</body>
</html>
<?php
mysql_free_result($rs_activebans);
?>
