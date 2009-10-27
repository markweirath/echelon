<?php
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');
require_once('Connections/functions.php');

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rs_toppenalties = 25;
$pageNum_rs_toppenalties = 0;
if (isset($_GET['pageNum_rs_toppenalties'])) {
  $pageNum_rs_toppenalties = $_GET['pageNum_rs_toppenalties'];
}
$startRow_rs_toppenalties = $pageNum_rs_toppenalties * $maxRows_rs_toppenalties;
$xlorderby_rs_toppenalties = "Penalty";
if (isset($_GET['orderby'])) {
  $xlorderby_rs_toppenalties = (get_magic_quotes_gpc()) ? $_GET['orderby'] : addslashes($_GET['orderby']);
}
$xlorder_rs_toppenalties = "DESC";
if (isset($_GET['order'])) {
  $xlorder_rs_toppenalties = (get_magic_quotes_gpc()) ? $_GET['order'] : addslashes($_GET['order']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_toppenalties = sprintf("SELECT count(T1.id) as Penalty, count(T1.id)/T2.connections as ratio, sum(T1.duration) as duration, T1.client_id, T2.name FROM `penalties` T1 INNER JOIN clients T2 on T1.client_id = T2.id AND T1.inactive = 0 GROUP BY T1.client_id, T2.name ORDER BY %s %s", $xlorderby_rs_toppenalties,$xlorder_rs_toppenalties);
//$query_rs_toppenalties = sprintf("SELECT penalties.id, penalties.type, penalties.time_add, penalties.time_expire, penalties.reason, penalties.inactive, penalties.duration, target.id as target_id, target.name as target_name, admin.id as admi_id, admin.name as admi_name FROM penalties, clients as admin, clients as target WHERE admi_id != '0' AND penalties.type = 'Kick' AND inactive = 0 AND penalties.time_expire = 0 AND penalties.client_id = target.id AND penalties.admi_id = admin.id ORDER BY %s %s", $xlorderby_rs_toppenalties,$xlorder_rs_toppenalties);
$query_limit_rs_toppenalties = sprintf("%s LIMIT %d, %d", $query_rs_toppenalties, $startRow_rs_toppenalties, $maxRows_rs_toppenalties);
$rs_toppenalties = mysql_query($query_limit_rs_toppenalties, $b3connect) or die(mysql_error());
$row_rs_toppenalties = mysql_fetch_assoc($rs_toppenalties);
if (isset($_GET['totalRows_rs_toppenalties'])) {
  $totalRows_rs_toppenalties = $_GET['totalRows_rs_toppenalties'];
} else {
  $all_rs_toppenalties = mysql_query($query_rs_toppenalties);
  $totalRows_rs_toppenalties = mysql_num_rows($all_rs_toppenalties);
}
$totalPages_rs_toppenalties = ceil($totalRows_rs_toppenalties/$maxRows_rs_toppenalties)-1;
$queryString_rs_toppenalties = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rs_toppenalties") == false && 
        stristr($param, "totalRows_rs_toppenalties") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rs_toppenalties = "&" . implode("&", $newParams);
  }
}
$queryString_rs_toppenalties = sprintf("&totalRows_rs_toppenalties=%d%s", $totalRows_rs_toppenalties, $queryString_rs_toppenalties);
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
      <?php require_once('login/inc_loggedin.php'); ?>
      <?php include('Connections/inc_codnav.php'); ?>
      <table width="100%" class="tabeluitleg" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center">
            <strong>Toplist Penalties .</strong>
            <br>You are viewing the toplist Penalties.
          </td>
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td class="tabelkop">
            Amount&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=Penalty&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=Penalty&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td class="tabelkop">
            Avg per connection&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=ratio&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=ratio&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td class="tabelkop">
            total warn/tempban duration&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=duration&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=duration&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td class="tabelkop">
            client&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=name&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=name&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
        </tr>
        <?php do { ?>
        <tr class="tabelinhoud">
          <td>
            <?php echo $row_rs_toppenalties['Penalty']; ?>
          </td>
          <td>
            <?php echo $row_rs_toppenalties['ratio']; ?>
          </td>
          <td>
            <?php echo humanReadableDuration($row_rs_toppenalties['duration']*60); ?>
          </td>
          <td>
            <a href="clientdetails.php?game=<?php echo $game; ?>&id=<?php echo $row_rs_toppenalties['client_id']; ?>">
              <?php echo htmlspecialchars($row_rs_toppenalties['name']); ?></a>
          </td>
        </tr>
        <?php } while ($row_rs_toppenalties = mysql_fetch_assoc($rs_toppenalties)); ?>
        <tr class="tabelonderschrift">
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>
            click client to see details
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" align="center" class="recordnavigatie">
        <tr class="tabelkop">
          <td width="100%" colspan="4" align="center">
            Records:&nbsp;
            <?php echo ($startRow_rs_toppenalties + 1) ?>
            &nbsp;to&nbsp;
            <?php echo min($startRow_rs_toppenalties + $maxRows_rs_toppenalties, $totalRows_rs_toppenalties) ?>
            &nbsp;from&nbsp;
            <?php echo $totalRows_rs_toppenalties ?>
          </td>
        </tr>
        <tr>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_toppenalties > 0) { // Show if not first page ?>
            <a href="<?php printf("%25s?pageNum_rs_toppenalties=%25d%25s", $currentPage, 0, $queryString_rs_toppenalties); ?>">First</a>
            <?php } // Show if not first page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_toppenalties > 0) { // Show if not first page ?>
            <a href="<?php printf("%25s?pageNum_rs_toppenalties=%25d%25s", $currentPage, max(0, $pageNum_rs_toppenalties - 1), $queryString_rs_toppenalties); ?>">Previous</a>
            <?php } // Show if not first page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_toppenalties < $totalPages_rs_toppenalties) { // Show if not last page ?>
            <a href="<?php printf("%25s?pageNum_rs_toppenalties=%25d%25s", $currentPage, min($totalPages_rs_toppenalties, $pageNum_rs_toppenalties + 1), $queryString_rs_toppenalties); ?>">Next</a>
            <?php } // Show if not last page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_toppenalties < $totalPages_rs_toppenalties) { // Show if not last page ?>
            <a href="<?php printf("%25s?pageNum_rs_toppenalties=%25d%25s", $currentPage, $totalPages_rs_toppenalties, $queryString_rs_toppenalties); ?>">Last</a>
            <?php } // Show if not last page ?>
          </td>
        </tr>
      </table>
      <?php include "footer.php"; ?>
    </div>
  </body>
</html>
<?php
mysql_free_result($rs_toppenalties);
?>