<?php
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('Connections/wwwvalidate.php');
require_once('login/inc_authorize.php');
require_once('Connections/inc_config.php');

$colname_rs_links = "1";
if (isset($_SESSION['xlradminlevel'])) {
  $colname_rs_links = (get_magic_quotes_gpc()) ? $_SESSION['xlradminlevel'] : addslashes($_SESSION['xlradminlevel']);
}
mysql_select_db($database_wwwvalidate, $wwwvalidate);
$query_rs_links = sprintf("SELECT * FROM links WHERE `level` >= %s ORDER BY name ASC", $colname_rs_links);
$rs_links = mysql_query($query_rs_links, $wwwvalidate) or die(mysql_error());
$row_rs_links = mysql_fetch_assoc($rs_links);
$totalRows_rs_links = mysql_num_rows($rs_links);
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
      <table width="100%" class="tabelinhoud" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2">
            <?php if ($colname_rs_links = 1) echo"&#8226; <a href=\"admin/users.php\">User Administration</a><br>"; ?>
            &#8226; <a href="login/changepass.php">Change your password</a><br>
            &#8226; <a href="ipcalc.php">NetBlock Calculator</a><br>
            &nbsp;<br>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="tabelkop">
            These are the links available for your level:
          </td>
        </tr>
        <tr>
          <td colspan="2">
            &nbsp;
          </td>
        </tr>
        <?php do { ?>
        <tr>
          <td width="17%">
            &#8226;
            <a href="<?php echo $row_rs_links['link']; ?>" target="_blank">
              <?php echo $row_rs_links['name']; ?></a>
          </td>
          <td width="83%">
            <?php echo $row_rs_links['description']; ?>
          </td>
        </tr>
        <tr>
          <td>
            &nbsp;
          </td>
          <td>
            &nbsp;
          </td>
        </tr>
        <?php } while ($row_rs_links = mysql_fetch_assoc($rs_links)); ?>
      </table>
      <?php include "footer.php"; ?>
    </div>
  </body>
</html>
<?php
mysql_free_result($rs_links);
?>
