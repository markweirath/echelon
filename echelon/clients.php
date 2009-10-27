<?php
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');

$currentPage = $_SERVER["PHP_SELF"];
?>
<?php
$maxRows_rs_clients = 25;
$pageNum_rs_clients = 0;
if (isset($_GET['pageNum_rs_clients'])) {
  $pageNum_rs_clients = $_GET['pageNum_rs_clients'];
}
$startRow_rs_clients = $pageNum_rs_clients * $maxRows_rs_clients;
$xlorderby_rs_clients = "id";
if (isset($_GET['orderby'])) {
  $xlorderby_rs_clients = (get_magic_quotes_gpc()) ? $_GET['orderby'] : addslashes($_GET['orderby']);
}
$xlorder_rs_clients = "DESC";
if (isset($_GET['order'])) {
  $xlorder_rs_clients = (get_magic_quotes_gpc()) ? $_GET['order'] : addslashes($_GET['order']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_clients = sprintf("SELECT T1.*, T2.name as level 
FROM clients T1 LEFT JOIN groups T2 
ON T1.group_bits = T2.id 
ORDER BY %s %s", $xlorderby_rs_clients,$xlorder_rs_clients);
$query_limit_rs_clients = sprintf("%s LIMIT %d, %d", $query_rs_clients, $startRow_rs_clients, $maxRows_rs_clients);
$rs_clients = mysql_query($query_limit_rs_clients, $b3connect) or die(mysql_error());
$row_rs_clients = mysql_fetch_assoc($rs_clients);
if (isset($_GET['totalRows_rs_clients'])) {
  $totalRows_rs_clients = $_GET['totalRows_rs_clients'];
} else {
  $all_rs_clients = mysql_query($query_rs_clients);
  $totalRows_rs_clients = mysql_num_rows($all_rs_clients);
}
$totalPages_rs_clients = ceil($totalRows_rs_clients/$maxRows_rs_clients)-1;
$maxRows_rs_clientsearch = 25;
$pageNum_rs_clientsearch = 0;
if (isset($_GET['pageNum_rs_clientsearch'])) {
  $pageNum_rs_clientsearch = $_GET['pageNum_rs_clientsearch'];
}
$startRow_rs_clientsearch = $pageNum_rs_clientsearch * $maxRows_rs_clientsearch;
$xlresult_rs_clientsearch = "halsdflkjhasdfhjklasfkjndcknbnqfoiuhk";
if (isset($_GET['search'])) {
  $xlresult_rs_clientsearch = (get_magic_quotes_gpc()) ? $_GET['search'] : addslashes($_GET['search']);
}
$xlorderby_rs_clientsearch = "id";
if (isset($_GET['orderby'])) {
  $xlorderby_rs_clientsearch = (get_magic_quotes_gpc()) ? $_GET['orderby'] : addslashes($_GET['orderby']);
}
$xlorder_rs_clientsearch = "DESC";
if (isset($_GET['order'])) {
  $xlorder_rs_clientsearch = (get_magic_quotes_gpc()) ? $_GET['order'] : addslashes($_GET['order']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_clientsearch = sprintf("SELECT * FROM clients WHERE name like '%%%s%%' OR guid like '%%%s%%' OR pbid like '%%%s%%' OR ip like '%%%s%%'  ORDER BY %s %s", $xlresult_rs_clientsearch,$xlresult_rs_clientsearch,$xlresult_rs_clientsearch,$xlresult_rs_clientsearch,$xlorderby_rs_clientsearch,$xlorder_rs_clientsearch);
$query_limit_rs_clientsearch = sprintf("%s LIMIT %d, %d", $query_rs_clientsearch, $startRow_rs_clientsearch, $maxRows_rs_clientsearch);
$rs_clientsearch = mysql_query($query_limit_rs_clientsearch, $b3connect) or die(mysql_error());
$row_rs_clientsearch = mysql_fetch_assoc($rs_clientsearch);
if (isset($_GET['totalRows_rs_clientsearch'])) {
  $totalRows_rs_clientsearch = $_GET['totalRows_rs_clientsearch'];
} else {
  $all_rs_clientsearch = mysql_query($query_rs_clientsearch);
  $totalRows_rs_clientsearch = mysql_num_rows($all_rs_clientsearch);
}
$totalPages_rs_clientsearch = ceil($totalRows_rs_clientsearch/$maxRows_rs_clientsearch)-1;
$queryString_rs_clients = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rs_clients") == false && 
        stristr($param, "totalRows_rs_clients") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rs_clients = "&" . implode("&", $newParams);
  }
}
$queryString_rs_clients = sprintf("&totalRows_rs_clients=%d%s", $totalRows_rs_clients, $queryString_rs_clients);
$queryString_rs_clientsearch = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rs_clientsearch") == false && 
        stristr($param, "totalRows_rs_clientsearch") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rs_clientsearch = "&" . implode("&", $newParams);
  }
}
$queryString_rs_clientsearch = sprintf("&totalRows_rs_clientsearch=%d%s", $totalRows_rs_clientsearch, $queryString_rs_clientsearch);
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
      @import url("lib/jquery.autocomplete/jquery.autocomplete.css");
      -->
    </style>
<script language="JavaScript">
<!--
//tmtC_winOpen
var AliasResults;
//tmtC_winOpenEnd
function tmt_winOpen(u,id,f,df){
if(eval(id)==null||eval(id+".closed")){
eval(id+"=window.open('"+u+"','"+id+"','"+f+"')");eval(id+".focus()");}
else if(df){eval(id+".focus()");}
else{eval(id+"=window.open('"+u+"','"+id+"','"+f+"')");eval(id+".focus()");}
}
//-->
</script>
  </head>
  <body>
    <div id="wrapper">
      <?php require_once('login/inc_loggedin.php'); ?>
      <?php include('Connections/inc_codnav.php'); ?>
      
      <table width="100%" class="tabelinhoud">
        <tr>
          <td>
            <form action="clients.php" method="GET" name="search" id="search">
              Search :
              <input name="search" type="text" id="search" value="<?php echo $_GET['search'];?>">
              <input type="submit" name="Submit" value="Search">
              <input type="hidden" name="game" value="<?php echo $game; ?>">
              [
              <a href="clients.php?game=<?php echo $game; ?>">clear search</a>
              ]
            </form>
          </td>
        </tr>
      </table>
      <?php if ($_GET['search'] == ("")) { ?>
      <table width="100%" class="tabeluitleg">
        <tr>
          <td align="center">
            <strong>Client list.</strong>
            <br>You are viewing a list of all  clients.
          </td>
        </tr>
      </table>
      <table width="100%" cellspacing="1" cellpadding="1">
        <tr class="tabelkop">
          <td>
            client-id&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=id&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=id&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            name&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=name&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=name&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            level&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=group_bits&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=group_bits&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            connections&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=connections&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=connections&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            firstseen&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=time_add&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=time_add&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            lastseen&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=time_edit&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=time_edit&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
        </tr>
        <?php do { ?>
        <tr class="tabelinhoud">
          <td>
            <?php echo $row_rs_clients['id']; ?>
          </td>
            <td <?php if (strlen(trim($row_rs_clients['name']))==0) { // special case where the player's name is only composed of blank characters  
                echo 'onclick="javascript:window.location=\'clientdetails.php?game=' . $game . '&amp;id=' . $row_rs_clients['id'] .'\'" style="cursor:pointer"';
              } ?> >
              <a href="clientdetails.php?game=<?php echo $game; ?>&id=<?php echo $row_rs_clients['id']; ?>">
                <?php 
                echo htmlspecialchars($row_rs_clients['name']); ?></a>
            </td>
          <td>
            <?php echo $row_rs_clients['level']; ?>
          </td>
          <td>
            <?php echo $row_rs_clients['connections']; ?>
          </td>
          <td>
            <?php echo date('l, d/m/Y (H:i)',$row_rs_clients['time_add']); ?>
          </td>
          <td>
            <?php echo date('l, d/m/Y (H:i)',$row_rs_clients['time_edit']); ?>
          </td>
        </tr>
        <?php } while ($row_rs_clients = mysql_fetch_assoc($rs_clients)); ?>
        <tr class="tabelonderschrift">
          <td>
            &nbsp;
          </td>
          <td>
            click client to see details
          </td>
          <td>
            &nbsp;
          </td>
          <td>
            &nbsp;
          </td>
          <td>
            &nbsp;
          </td>
          <td>
            &nbsp;
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" align="center" class="recordnavigatie">
        <tr class="tabelkop">
          <td width="100%" colspan="4" align="center">
            Records:&nbsp;
            <?php echo ($startRow_rs_clients + 1) ?>
            &nbsp;to&nbsp;
            <?php echo min($startRow_rs_clients + $maxRows_rs_clients, $totalRows_rs_clients) ?>
            &nbsp;from&nbsp;
            <?php echo $totalRows_rs_clients ?>
          </td>
        </tr>
        <tr>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_clients > 0) { // Show if not first page ?>
            <a href="<?php printf("%25s?pageNum_rs_clients=%25d%25s", $currentPage, 0, $queryString_rs_clients); ?>">First</a>
            <?php } // Show if not first page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_clients > 0) { // Show if not first page ?>
            <a href="<?php printf("%25s?pageNum_rs_clients=%25d%25s", $currentPage, max(0, $pageNum_rs_clients - 1), $queryString_rs_clients); ?>">Previous</a>
            <?php } // Show if not first page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_clients < $totalPages_rs_clients) { // Show if not last page ?>
            <a href="<?php printf("%25s?pageNum_rs_clients=%25d%25s", $currentPage, min($totalPages_rs_clients, $pageNum_rs_clients + 1), $queryString_rs_clients); ?>">Next</a>
            <?php } // Show if not last page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_clients < $totalPages_rs_clients) { // Show if not last page ?>
            <a href="<?php printf("%25s?pageNum_rs_clients=%25d%25s", $currentPage, $totalPages_rs_clients, $queryString_rs_clients); ?>">Last</a>
            <?php } // Show if not last page ?>
          </td>
        </tr>
      </table>
<?php }
/* if ($_GET['search'] == ("")) */
      ?>
      <?php if ($_GET['search'] != ("")) { ?>
      <table width="100%" class="tabeluitleg">
        <tr>
          <td align="center">
            <strong>Client search.</strong>
            <br>Results for clientsearch on &quot;
            <strong>
              <?php echo $search; ?></strong>
            &quot;.
            We search on NAME, GUID, PB-GUID and IP.
          </td>
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr class="tabelkop">
          <td>
            client-id&nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=id&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=id&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            name&nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=name&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=name&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            connections&nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=connections&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=connections&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            firstseen&nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=time_add&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=time_add&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td>
            lastseen&nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=time_edit&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?search=<?php echo $search; ?>&game=<?php echo $game; ?>&orderby=time_edit&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
        </tr>
        <?php do { ?>
        <tr class="tabelinhoud">
          <td>
            <?php echo $row_rs_clientsearch['id']; ?>
          </td>
          <td>
            <a href="clientdetails.php?game=<?php echo $game; ?>&id=<?php echo $row_rs_clientsearch['id']; ?>">
              <?php echo htmlspecialchars($row_rs_clientsearch['name']); ?></a>
          </td>
          <td>
            <?php echo $row_rs_clientsearch['connections']; ?>
          </td>
          <td>
            <?php echo date('l, d/m/Y (H:i)',$row_rs_clientsearch['time_add']); ?>
          </td>
          <td>
            <?php echo date('l, d/m/Y (H:i)',$row_rs_clientsearch['time_edit']); ?>
          </td>
        </tr>
        <?php } while ($row_rs_clientsearch = mysql_fetch_assoc($rs_clientsearch)); ?>
        <tr class="tabelonderschrift">
          <td>
            &nbsp;
          </td>
          <td>
            click client to see details
          </td>
          <td>
            &nbsp;
          </td>
          <td>
            &nbsp;
          </td>
          <td>
            &nbsp;
          </td>
        </tr>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" align="center" class="recordnavigatie">
        <tr class="tabelkop">
          <td width="100%" colspan="4" align="center">
            Records:&nbsp;
            <?php echo ($startRow_rs_clientsearch + 1) ?>
            &nbsp;to&nbsp;
            <?php echo min($startRow_rs_clientsearch + $maxRows_rs_clientsearch, $totalRows_rs_clientsearch) ?>
            &nbsp;from&nbsp;
            <?php echo $totalRows_rs_clientsearch ?>
          </td>
        </tr>
        <tr>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_clientsearch > 0) { // Show if not first page ?>
            <a href="<?php printf("%25s?pageNum_rs_clientsearch=%25d%25s", $currentPage, 0, $queryString_rs_clientsearch); ?>">First</a>
            <?php } // Show if not first page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_clientsearch > 0) { // Show if not first page ?>
            <a href="<?php printf("%25s?pageNum_rs_clientsearch=%25d%25s", $currentPage, max(0, $pageNum_rs_clientsearch - 1), $queryString_rs_clientsearch); ?>">Previous</a>
            <?php } // Show if not first page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_clientsearch < $totalPages_rs_clientsearch) { // Show if not last page ?>
            <a href="<?php printf("%25s?pageNum_rs_clientsearch=%25d%25s", $currentPage, min($totalPages_rs_clientsearch, $pageNum_rs_clientsearch + 1), $queryString_rs_clientsearch); ?>">Next</a>
            <?php } // Show if not last page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_clientsearch < $totalPages_rs_clientsearch) { // Show if not last page ?>
            <a href="<?php printf("%25s?pageNum_rs_clientsearch=%25d%25s", $currentPage, $totalPages_rs_clientsearch, $queryString_rs_clientsearch); ?>">Last</a>
            <?php } // Show if not last page ?>
          </td>
        </tr>
      </table>
      
      <table width="100%" border="0" cellspacing="5" cellpadding="0" class="recordnavigatie">
        <tr>
          <td align="center">
            <a href="#" onClick="tmt_winOpen('searchalias.php?search=<?php echo $search; ?>&amp;game=<?php echo $game; ?>
              ','AliasResults','width=500,height=400,left=0,top=0,resizable=yes',0)">Not found what you were looking for? Click here to search on &quot;
              <strong>
                <?php echo $search; ?></strong>
              &quot; in
              the alias table!</a>
          </td>
        </tr>
      </table>
<?php }
/* if ($_GET['search'] != ("")) */
      ?>
      <?php include "footer.php"; ?>
    </div>
  </body>
</html>
<?php
mysql_free_result($rs_clients);
mysql_free_result($rs_clientsearch);
?>

<!-- javascript is better placed after html to allow browsers start HTML rendering earlier -->
<script language="JavaScript" type="text/javascript" src="lib/jquery-1.2.6.min.js"></script>
<script language="JavaScript" type="text/javascript" src="lib/jquery.autocomplete/jquery.autocomplete.min.js"></script>
<script language="JavaScript" type="text/javascript" src="lib/jquery.bgiframe.min.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
$(document).ready(function() {
 $('input:text#search').autocomplete("ajax.php", {
    minChars: 3,
    cacheLength: 10,
    matchContains:1,
    width: 200,
    extraParams: { 'action':'searchPlayerByName'<?php if (isset($_GET['game'])) echo ", 'game': ".$_GET['game'];?> },
    formatItem: function(row) {
      return row[1];
    }
  });
});
//-->
</script>
