<?php
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');

function ipcalc() 
{
  global $ipfrom;
  global $ipto;
  global $networkaddress;
  global $netmaskbits;
  $errmsg = "Error!<br />Use your back button to try again.";
  
  // Step 1. Collect IP range
  if (isset($_GET['ipfrom'])) $ipfrom = $_GET['ipfrom']; else $ipfrom = "0.0.0.0";
  if (isset($_GET['ipto'])) $ipto = $_GET['ipto']; else $ipto = "0.0.0.0";
  
  // Step 2. Calculate 32bit numbers
  $ip32f = ip2long($ipfrom);
  $ip32t = ip2long($ipto);
  if ($ip32f == -1 || $ip32t == -1)
  {
    die ("$errmsg</body></html>");
  }
  //echo "From $ip32f to $ip32t <br/>";
  // Step 3. Bitwise Compare them
  $result = $ip32f ^ $ip32t;
  //echo "xor result: $result<br/>";
  // Step 4. Calculate Netmask
  $netmaskbits = 0;
  while ( !($result & 0x80000000) )
  {
    $netmaskbits += 1;
    $result = $result << 1;
    
    if ($netmaskbits == 33)
    {
      echo "Netmaskbits overflow<br/>";
      break;
    }
  }
  //echo "Netmaskbits: $netmaskbits<br/>";
 
  // Step 5. Determine Network Address
  $netmask = 0;
  for ($i=0; $i<$netmaskbits; $i+=1)
  {
    $netmask = $netmask >> 1;
    $netmask = $netmask | 0x80000000;
  }
  $networkaddress = long2ip($ip32f & $netmask);
}
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
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabeluitleg">
        <tr>
          <td>
            <strong>IP calculator</strong>
            <br>
            With this calculator you can insert an IP-range and the result is a broad
            netblock that you can insert into your linux firewall or banned list.
            <br>
            You can find calculators online for reverse calculation:
            <br>
            [
            <a href="http://jodies.de/ipcalc" target="_blank">Jodies</a>
            ]
            [
            <a href="http://www.subnetmask.info/" target="_blank">SubNetMask info</a>
            ]
            [
            <a href="http://www.surfnet.nl/diensten/surfnetthuis/adsl/ipcalc.html" target="_blank">SurfnetNL</a>
            ]
          </td>
        </tr>
      </table>
      <?php if (($_GET['ipfrom'] == ("") || $_GET['ipfrom'] == ("0.0.0.0"))  || ($_GET['ipto'] == ("") || $_GET['ipto'] == ("0.0.0.0")) ) { ?>
      <form action="ipcalc.php" method="GET" name="ipcalc" id="ipcalc">
        <table width="100%" border="0" cellpadding="3" cellspacing="0">
          <tr class="tabelkop">
            <td align="right">
              From:
            </td>
            <td>
              <input name="ipfrom" type="text" value="0.0.0.0" size="20" maxlength="15">
            </td>
          </tr>
          <tr class="tabelkop">
            <td align="right">
              To:
            </td>
            <td>
              <input name="ipto" type="text" value="0.0.0.0" size="20" maxlength="15">
            </td>
          </tr>
          <tr class="tabelkop">
            <td align="right">
              &nbsp;
            </td>
            <td>
              <input type="submit" name="Submit" value="Calculate">
              <input type="reset" name="Submit2" value="Reset">
            </td>
          </tr>
        </table>
      </form>
      <?php } ?>
      <?php if ($_GET['ipfrom'] != ("") && $_GET['ipfrom'] != ("0.0.0.0") && $_GET['ipto'] != ("") && $_GET['ipto'] != ("0.0.0.0")) { ?>
      <?php ipcalc(); ?>
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr class="tabelkop">
          <td>
            &nbsp;
            
          </td>
          <td>
            &nbsp;
            
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td colspan="2" >
            <br>
            <?php echo "Range From: <strong>$ipfrom</strong> to <strong>$ipto</strong>"?>
            -
            <strong>
              <?php echo "Result: $networkaddress/$netmaskbits" ?></strong>
            <br>
            <br>
            <a href="ipcalc.php">Calculate again</a>
            <br>
          </td>
        </tr>
      </table>
      <?php } ?>
      <?php include "footer.php"; ?>
    </div>
  </body>
</html>
