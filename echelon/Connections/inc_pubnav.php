<?php 
$navThisPage = $_SERVER["PHP_SELF"];
$game = "";
$search = "";
if (!empty($_GET['game'])) {
  $game = $_GET['game']; }
else {
  $game = "1"; }
if (!empty($_GET['search'])) {
  $search = $_GET['search']; }
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="navigatie">
  <tr>
    <td><?php if ($xlrdatabase == "multi")
    {
    $navcounter = 1;
    while ($navcounter <= $numservers) {
      if ($navcounter == 1) { echo "[ "; }
        else { echo " | "; }
      echo "<a href=\"" . $navThisPage . "?game=" . $navcounter;
      if (!empty($_GET['search'])) { echo "&search=" . $_GET['search']; }
      echo "\"";
      if ($game == $navcounter) { echo " class=\"activegame\">"; }
      else { echo " class=\"navigatie\">"; }
      echo "${gamename[$navcounter]}";
      echo "</a>";
      $navcounter++;
      }
	  echo " ]&nbsp;-&nbsp;<a class=\"navigatie\" href=\"banlist.php\">banlist.txt</a>";
    }
      ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabelinhoud">
  <tr>
    <td>
      &nbsp;
    </td>
  </tr>
</table>
