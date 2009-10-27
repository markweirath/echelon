<?php 
include "ctracker.php";
error_reporting( E_ERROR ^ E_WARNING );

// Next line sets the echelon userlevel for this page. 1=superadmins - 2=admins - 3=moderators
$requiredlevel = 3;
require_once('Connections/b3connect.php');
require_once('login/inc_authorize.php');
require_once('admin/rcon.php');

if ($chatlogger_plugin_activated != 1)
  {
  header ("Location: $path/clients.php?game=$game");
  exit;
  }

$currentPage = $_SERVER["PHP_SELF"];

$talkback = "";
if (isset($_POST['talkback'])) {
  $talkback = $_POST['talkback'];
  if ($talkback != "") {
    $command = "say ^4[B3](^3" . $_SESSION['xlradmin'] . "^4): ^3" . $talkback;
    //echo "command: " . $command;
    rcon ($command);
  }
}

if (isset($_GET['maxRows_rs_chats'])) {
  $maxRows_rs_chats = $_GET['maxRows_rs_chats'];
} else {
  $maxRows_rs_chats = 50;
}
$pageNum_rs_chats = 0;
if (isset($_GET['pageNum_rs_chats'])) {
  $pageNum_rs_chats = $_GET['pageNum_rs_chats'];
}
$startRow_rs_chats = $pageNum_rs_chats * $maxRows_rs_chats;
$xlorderby_rs_chats = "id";
if (isset($_GET['orderby'])) {
  $xlorderby_rs_chats = (get_magic_quotes_gpc()) ? $_GET['orderby'] : addslashes($_GET['orderby']);
}
$xlorder_rs_chats = "DESC";
if (isset($_GET['order'])) {
  $xlorder_rs_chats = (get_magic_quotes_gpc()) ? $_GET['order'] : addslashes($_GET['order']);
}
mysql_select_db($database_b3connect, $b3connect);
$query_rs_chats = sprintf("SELECT * FROM chatlog ORDER BY %s %s", $xlorderby_rs_chats,$xlorder_rs_chats);
$query_limit_rs_chats = sprintf("%s LIMIT %d, %d", $query_rs_chats, $startRow_rs_chats, $maxRows_rs_chats);
$rs_chats = mysql_query($query_limit_rs_chats, $b3connect) or die(mysql_error());
$row_rs_chats = mysql_fetch_assoc($rs_chats);
if (isset($_GET['totalRows_rs_chats'])) {
  $totalRows_rs_chats = $_GET['totalRows_rs_chats'];
} else {
  $count_rs_chats = mysql_query('SELECT COUNT(*) numrows FROM chatlog');
  $count_row_rs_chats = mysql_fetch_assoc($count_rs_chats);
  $totalRows_rs_chats = $count_row_rs_chats['numrows'];
}
$totalPages_rs_chats = ceil($totalRows_rs_chats/$maxRows_rs_chats)-1;
$queryString_rs_chats = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rs_chats") == false && 
        stristr($param, "totalRows_rs_chats") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rs_chats = "&" . implode("&", $newParams);
  }
}
$queryString_rs_chats = sprintf("&totalRows_rs_chats=%d%s", $totalRows_rs_chats, $queryString_rs_chats);
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
            <strong>The Chatlog</strong>
            <br>You are viewing the chatlog.
          </td>
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <thead>
        <tr>
          <td class="tabelkop" width = "215">
            Chatlog&nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=id&order=ASC">
              <img src="img/asc.gif" alt="ascending" width="11" height="9" border="0" align="absmiddle"></a>
            &nbsp;
            <a href="<?php echo $navThisPage; ?>?game=<?php echo $game; ?>&orderby=id&order=DESC">
              <img src="img/desc.gif" alt="descending" width="11" height="9" border="0" align="absmiddle"></a>
          </td>
          <td class="tabelkop" width = "200">
            <span id="refreshcommand"><a href="<?php echo $path; ?>chats.php?game=<?php echo $game; ?>" style="color: #FFFFFF; font-weight: normal">Refresh now</a></span>
            
            <!--<a href="javascript:this.location.reload();" style="color: #FFFFFF; font-weight: normal">refresh</a>-->
          </td>
          <td class="tabelkop">
            <table width="100%">
              <tr>
                <td>
                  <?php if ($rcon_pass != "rconpassword") { // this is the default rcon pass in the config, indicating rcon is not set up properly ?>
                  <form name="talkback" method="post" action="chats.php?game=<?php echo $game; ?>">
                    <input name="talkback" type="text" id="talkback" size="45" value="<?php echo $talkback; ?>">
                    <input type="submit" name="Submit" value="TalkBack">
                  </form>
                  <?php } ?>
                </td>
                <td>
                  <form name="rowcount" method="get" action="chats.php">
                    <input type="hidden" name="game" value="<?php echo $game; ?>"/>
                    <select name="maxRows_rs_chats" onchange='this.form.submit()'>
                      <option value="10"<?php if ($maxRows_rs_chats == 10) echo "selected" ?>>10</option>
                      <option value="25"<?php if ($maxRows_rs_chats == 25) echo "selected" ?>>25</option>
                      <option value="50"<?php if ($maxRows_rs_chats == 50) echo "selected" ?>>50</option>
                      <option value="75"<?php if ($maxRows_rs_chats == 75) echo "selected" ?>>75</option>
                      <option value="100"<?php if ($maxRows_rs_chats == 100) echo "selected" ?>>100</option>
                      <option value="250"<?php if ($maxRows_rs_chats == 250) echo "selected" ?>>250</option>
                      <option value="500"<?php if ($maxRows_rs_chats == 500) echo "selected" ?>>500</option>
                    </select>
                    Lines per page
                  </form>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        </thead>
        <tbody id="chatlog">
        <?php do { ?>
        <tr class="tabelinhoud" id="<?php echo $row_rs_chats['id'] ?>">
          <td>
            <i><?php echo date('l, d/m/Y (H:i)',$row_rs_chats['msg_time']); ?></i>
          </td>
          <td>
            <a href="clientdetails.php?game=<?php echo $game; ?>&id=<?php echo $row_rs_chats['client_id']; ?>"><?php echo htmlentities($row_rs_chats['client_name']); ?></a>&nbsp;(<?php echo $row_rs_chats['msg_type']; ?>)
          </td>
          <td>
            <?php 
              if (substr($row_rs_chats['msg'],0,1) == '!' or substr($row_rs_chats['msg'],0,1) == '@') echo "<b style=\"color:blue\">"; 
              echo htmlentities($row_rs_chats['msg']); 
              if (substr($row_rs_chats['msg'],0,1) == '!' or substr($row_rs_chats['msg'],0,1) == '@') echo "</b>"; 
            ?>
          </td>
        </tr>
        <?php } while ($row_rs_chats = mysql_fetch_assoc($rs_chats)); ?>
        </tbody>
        <tfoot>
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
        </tr>
        </tfoot>
      </table>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" align="center" class="recordnavigatie">
        <tr class="tabelkop">
          <td width="100%" colspan="4" align="center">
            Records:&nbsp;
            <?php echo ($startRow_rs_chats + 1) ?>
            &nbsp;to&nbsp;
            <?php echo min($startRow_rs_chats + $maxRows_rs_chats, $totalRows_rs_chats) ?>
            &nbsp;from&nbsp;
            <?php echo $totalRows_rs_chats ?>
          </td>
        </tr>
        <tr>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_chats > 0) { // Show if not first page ?>
            <a href="<?php printf("%25s?pageNum_rs_chats=%25d%25s", $currentPage, 0, $queryString_rs_chats); ?>">First</a>
            <?php } // Show if not first page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_chats > 0) { // Show if not first page ?>
            <a href="<?php printf("%25s?pageNum_rs_chats=%25d%25s", $currentPage, max(0, $pageNum_rs_chats - 1), $queryString_rs_chats); ?>">Previous</a>
            <?php } // Show if not first page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_chats < $totalPages_rs_chats) { // Show if not last page ?>
            <a href="<?php printf("%25s?pageNum_rs_chats=%25d%25s", $currentPage, min($totalPages_rs_chats, $pageNum_rs_chats + 1), $queryString_rs_chats); ?>">Next</a>
            <?php } // Show if not last page ?>
          </td>
          <td align="center" width="25%">
            <?php if ($pageNum_rs_chats < $totalPages_rs_chats) { // Show if not last page ?>
            <a href="<?php printf("%25s?pageNum_rs_chats=%25d%25s", $currentPage, $totalPages_rs_chats, $queryString_rs_chats); ?>">Last</a>
            <?php } // Show if not last page ?>
          </td>
        </tr>
      </table>
      <?php include "footer.php"; ?>
    </div>
  </body>
  

  <!-- javascript is better placed after html to allow browsers start HTML rendering earlier -->
  <script language="JavaScript" type="text/javascript" src="lib/jquery-1.2.6.min.js"></script>
  <script language="JavaScript" type="text/javascript" src="lib/jquery-ui-effectscore-1.5.2.min.js"></script>
  <script language="JavaScript" type="text/javascript" src="lib/dateFormat.js"></script>
  <STYLE type="text/css">
    #chatlog tr.new { background-color: lightblue; }
  </STYLE>
  <script language="JavaScript" type="text/javascript">
  <!--
    function buildChatLine(data) {
      //console.log(data);
      var $node = $('<tr class="tabelinhoud" id="'+data.id+'">' +
        '<td><i>'+(new Date()).dateFormat('l, d/m/Y (H:i)', 'en', data.msg_time)+'</i></td>' +
        '<td><a href="clientdetails.php?game=<?php echo $game; ?>'+
        '&id='+data.client_id+'"></a>&nbsp;(' + data.msg_type + ')</td>' +
        '<td class="msg"></td>'+
        '</tr>');
        $node.find('a').text(data.client_name);
        $node.find('td.msg').text(data.msg);
        if ( data.msg.charAt(0)=='!' || data.msg.charAt(0)=='@' ) {
          $('td.msg', $node).css({'color':'blue', 'font-weight':'bold'});
        }
        return $node;
    }
    
  
    function updateChat() { 
      // display the loading image
      $('body').append($('<div id="loadingimage" style="background-color:yellow;position:absolute;top:25px;right:35px">&nbsp;<i>updating</i>&nbsp;<img src="<?php echo $path; ?>/img/indicator.gif"></div>'));
      
      // fetch the new chat lines
      $.getJSON("ajax.php", { 
        'action': 'getLastChat', 
        'lastId':$("tr:first", "tbody#chatlog").attr("id"),
        'game': <?php echo $game; ?>
      }, function(json){
        //console.log(json);
        if (json.length > 0) {
          for (i=json.length-1; i>=0; i--) {
            $("tr:first", "tbody#chatlog").before(
              buildChatLine(json[i])
                .css('background-color','lightblue')
                .animate( { backgroundColor: 'white' }, 30000)
             );
          }
        }
      });
      
      $('#loadingimage').fadeOut('slow',function(){$(this).remove();});
    }; 
    
    
    ////////////////////////// START when page is rendered ////////////////////////////
    var autorefreshtimerId;
    $(document).ready(function() {
      
      $('<img src="<?php echo $path; ?>/img/indicator.gif"/>'); // preload image
    
      $('input:text[@name=talkback]', 'form[@name=talkback]').focus();

      $clone = $('span#refreshcommand').clone();
      $('span#refreshcommand').html('[&nbsp;'+$clone.html()+'&nbsp;|&nbsp;<label for="autorefresh" style="color: #FFFFFF; font-weight: normal">Auto refresh</label><input type="checkbox" name="autorefresh" />&nbsp;]');
      
      // if autorefresh box is checked, stats updating right away
      if ($('input:checkbox[@name=autorefresh]').get(0).checked) {
        autorefreshtimerId = window.setInterval(updateChat,4000);
      }
      
      // on autorefresh checkbox change :
      $('input:checkbox[@name=autorefresh]').click(function() {
        clearInterval(autorefreshtimerId);
        if (this.checked) {
          updateChat();
          autorefreshtimerId = window.setInterval(updateChat, 4000);
        }
      });
      
      // make the talkback form ajax if the talkback form exists
      if ( $('form[@name=talkback]').length > 0 ) {
        $('form[@name=talkback]').submit(function(){
        
          // if no text : cancel
          if ($('input:text[@name=talkback]', 'form[@name=talkback]').val().length <= 0) {
            $('input:text[@name=talkback]', 'form[@name=talkback]').focus();
            return false;
          }
          
          // displays the loading image
          $('input:text[@name=talkback]', 'form[@name=talkback]').css({
            'background-image':'url(<?php echo $path; ?>/img/indicator.gif)',
            'background-position': 'right',
            'background-repeat': 'no-repeat'
          });
          
          // the actual form post
          $.post('ajax.php', {
            action: 'talkback',
            game: <?php echo $game; ?>,
            talkback: $('input:text[@name=talkback]', 'form[@name=talkback]').val()
          }, function(data, status) {
            // we got a response
            $('input:text[@name=talkback]', 'form[@name=talkback]')
              .css({'background-image':''}) // remove the loading image
              .focus().select(); // focus and select the text input
            
            if (status=='success') {
              //console.info(data);
              $("tr:first", "tbody#chatlog").before(
                buildChatLine({
                  id: $("tr:first", "tbody#chatlog").attr("id"),
                  client_id: 0,
                  client_name: '<?php echo $_SESSION['xlradmin']; ?>',
                  msg_time: (new Date()).dateFormat('U'),
                  msg_type: 'TALKBACK',
                  msg: $('input:text[@name=talkback]', 'form[@name=talkback]').val()
                }).show().css('background-color','lightgreen')
               );
            } 
            else if (typeof console != 'undefined') console.warn('something went wrong: ', data);
        
          });
          
          // finally return false to cancel the default submit action
          return false; 
        });
      }
      
    });
  //-->
  </script>
</html>
<?php
mysql_free_result($rs_chats);
?>
