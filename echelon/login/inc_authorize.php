<?php
// Include this file in your protected pages. If the user is not logged in correctly it will redirect the user to the login page.
session_start();

if ((($_SESSION['xlrsesid']) != (session_id())) || (($_SESSION['xlradminlevel']) > ( $requiredlevel )))
  {
  header ("Location: $path/index.php");
  exit;
  }
?>
