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
//$query_rs_clientinfo = sprintf("SELECT * FROM clients WHERE id = %s", $colname_rs_clientinfo);
$query_rs_clientinfo = sprintf("SELECT T1.*, T2.name as level FROM clients T1 LEFT JOIN groups T2 ON T1.group_bits = T2.id WHERE T1.id = %s", $colname_rs_clientinfo);
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
      <?php require_once('login/inc_loggedin.php'); ?>
      <?php include('Connections/inc_codnav.php'); ?>
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr class="tabelinhoud">
          <td width="9%" align="right" class="tabelkop">
            Name:
          </td>
          <td width="35%">
            <?php echo htmlspecialchars($row_rs_clientinfo['name']); ?>
          </td>
          <td width="12%" align="right" class="tabelkop">
            IP:
          </td>
          <td width="44%">
            <?php if ($row_rs_clientinfo['ip'] != "") { ?>
            <a href="<?php echo $path; ?>clients.php?game=<?php echo $game; ?>&search=<?php echo $row_rs_clientinfo['ip']; ?>"><?php echo $row_rs_clientinfo['ip']; ?></a>
            &nbsp;&nbsp;
            <a href="http://whois.domaintools.com/<?php echo $row_rs_clientinfo['ip']; ?>" target="_blank"><img src="img/querry.gif" alt="whois search" name="whois" title = "Whois search" width="16" height="15" border="0" align="absmiddle" id="whois"></a>
            &nbsp;&nbsp;
            <a href="http://geotool.servehttp.com/?ip=<?php echo $row_rs_clientinfo['ip']; ?>" target="_blank"><img src="img/world.gif" alt="Show Location on map" name="Map" title = "Show Location on map" width="16" height="15" border="0" align="absmiddle" id="map"></a>
            <?php } else { ?>
            (No IP address available)
            <?php } ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td align="right" class="tabelkop">
            Client id:
          </td>
          <td>
            @ 
            <?php echo $row_rs_clientinfo['id']; ?>
            <?php // start of the hidden admin level information 
            if ( ($row_rs_clientinfo['group_bits'] < $hide_admin_level ) && ($row_rs_clientinfo['pbid'] != "WORLD") ) { ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            
          </td>
          <td align="right" class="tabelkop">
            Connections:
          </td>
          <td>
            <?php echo $row_rs_clientinfo['connections']; ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td align="right" class="tabelkop">
            group_bits
          </td>
          <td>
            (
            <?php //echo $row_rs_clientinfo['group_bits']; ?>
            <?php if ($row_rs_clientinfo['level'] == "")
                    {
                     echo "Un-registered";
                    }
                     else
                    {
                     echo $row_rs_clientinfo['level'];
                    }
            ?>
            )
          </td>
          <td align="right" class="tabelkop">
            First seen:
          </td>
          <td>
            <?php echo date('l, d/m/Y (H:i)',$row_rs_clientinfo['time_add']); ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td align="right" class="tabelkop">
            GUID:
          </td>
          <td>
            <?php if ($row_rs_clientinfo['guid'] != $row_rs_clientinfo['pbid']) { ?>
            <a href="<?php echo $path; ?>clients.php?game=<?php echo $game; ?>&search=<?php echo $row_rs_clientinfo['guid']; ?>"><?php echo $row_rs_clientinfo['guid']; ?></a>
            <?php } else { ?>
            (No GUID available)
            <?php } ?>
          </td>
          <td align="right" class="tabelkop">
            Last seen:
          </td>
          <td>
            <?php echo date('l, d/m/Y (H:i)',$row_rs_clientinfo['time_edit']); ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
          <td align="right" class="tabelkop">
            PB-GUID
          </td>
          <td>
            <?php if (($row_rs_clientinfo['pbid'] != "") && ($row_rs_clientinfo['pbid'] != "WORLD")) { ?>
            <a href="<?php echo $path; ?>clients.php?game=<?php echo $game; ?>&search=<?php echo $row_rs_clientinfo['pbid']; ?>"><?php echo $row_rs_clientinfo['pbid']; ?></a>
            <?php } else { ?>
            (No Punkbuster GUID available)
            <?php } ?>
          </td>
          <td align="right" class="tabelkop">
            Greeting:
          </td>
          <td>
            <?php echo $row_rs_clientinfo['greeting']; ?>
          </td>
        </tr>
        <tr class="tabelinhoud">
         <td class="tabelkop">
         &nbsp;
         </td>
         <td colspan=3 class="tabelkop">
        		<b>Echelon Admin Functions below</b>
       	 </td>
       	</tr>
	<tr class="tabelinhoud">
	  <td align="right"class="tabelkop">
	    Comment
	  </td>
	 <td><br>
		<form name=textinput method="Post" Action="admin/textadd.php?id=<?php echo $row_rs_clientinfo['id']; ?>">
			<input type="text" value="Enter new comment" Name="comment">
			<input type="hidden" value="<?php echo $game; ?>" Name="game">
			<input type="submit" name="submit1" value="update">
		</form>
	  </td>
	  <td class="tabelkop" align="right">
	  	Banning
	  </td>
	  <td>
	  <form name="tempbaninput" method="Post" Action="admin/tempban.php?id=<?php echo $row_rs_clientinfo['id']; ?>&pbid=<?php echo $row_rs_clientinfo['pbid']; ?>&clientname=<?php echo urlencode($row_rs_clientinfo['name']); ?>&client_ip=<?php echo $row_rs_clientinfo['ip']; ?>&game=<?php echo $game;?>">
      <input type="text" value="Banned by an Echelon WebAdmin" size="50" Maxlength="50" Name="reason">
    	<input type="text" value="Number" size="9" Maxlength="9" Name="bantime">
    	<SELECT name="time">
      	<option>Minutes</option>
      	<option>Hours</option>
      	<option>Days</option>           	
    	</select>
      <input type="submit" name="submit2" value="tempban" class="button">
    </form>

	  <form name="baninput" method="Post" Action="admin/ban.php?id=<?php echo $row_rs_clientinfo['id']; ?>&pbid=<?php echo $row_rs_clientinfo['pbid']; ?>&clientname=<?php echo urlencode($row_rs_clientinfo['name']); ?>&client_ip=<?php echo $row_rs_clientinfo['ip']; ?>&game=<?php echo $game;?>">
      <input type="text" value="Banned by an Echelon WebAdmin" size="50" Maxlength="50" Name="reason">
      <input type="submit" value="permban" class="button" style="background: #B9A489 url(img/insta_ban.gif) no-repeat top left; padding-left:15px">
    </form>
	  </td>
	</tr>
	<tr class="tabelinhoud">
	  <td align="right"class="tabelkop">Level</td>
	  <td colspan=3>
	  <form name=adminaddition method="Post" Action="admin/adminadd.php?id=<?php echo $row_rs_clientinfo['id']; ?>">
		  ID Level
        <SELECT name="level">
            	<option value="0"<?=($row_rs_clientinfo['group_bits']=='0')?' selected':'';?>>unregistered</option>
            	<option value="1"<?=($row_rs_clientinfo['group_bits']=='1')?' selected':'';?>>user</option>
            	<option value="2"<?=($row_rs_clientinfo['group_bits']=='2')?' selected':'';?>>regular</option>
            	<option value="8"<?=($row_rs_clientinfo['group_bits']=='8')?' selected':'';?>>moderator</option>   
              <option value="16"<?=($row_rs_clientinfo['group_bits']=='16')?' selected':'';?>>admin</option>
              <option value="32"<?=($row_rs_clientinfo['group_bits']=='32')?' selected':'';?>>full admin</option>
              <option value="64"<?=($row_rs_clientinfo['group_bits']=='64')?' selected':'';?>>senior admin</option>
   	    </select>
			<input type="hidden" value="<?php echo $game; ?>" Name="game">
			<input type="submit" name="submit3" value="Change Level">
		</form>
            <?php } // end of the hidden admin level information ?>
	  </td>
  </tr>
   </table>
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td class="tabelkop">
            Used Aliasses:
          </td>
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
      <?php include('inc_clienthistory.php'); ?>
      <?php if ($chatlogger_plugin_activated) include('inc_clientchat.php'); ?>
      <?php include "footer.php"; ?>
    </div>
  </body>
</html>
<?php
mysql_free_result($rs_clientinfo);
mysql_free_result($rs_aliases);
?>
