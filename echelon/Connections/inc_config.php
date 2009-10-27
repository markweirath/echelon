<?php
$path = "/echelon/";
$hide_admin_level = "128"; // 1, 2, 8, 16, 32, 64, 128 -> Hides clientdetails 

## settings for the echelon database
$hostname_wwwvalidate = "localhost";
$database_wwwvalidate = "echelon";
$username_wwwvalidate = "echelon";
$password_wwwvalidate = "echelonb3";

## settings for your b3 databases
$config = array(
  "clanname" => "Echelon",
  //How many servers do we have down here
  "numservers" => 3, 
  "servers" => array(
    1 => array(
      "name" => "UO",
      // Database connection for the B3 
      "hostname" => "localhost",
      "database" => "databasename",
      "username" => "db-username",
      "password" => "db-password",
      //Do we have PunkBuster in this game? If yes, we'll use RCON to ban/unban PB-guids - Set it to "0" if not.
      "PBactive" => "0",
      //Set up rcon for talkback (need chatlogger plugin installed) and PB banning facilities
      "rcon_ip" => "127.0.0.1",
      "rcon_port" => "28960",
      "rcon_pass" => "rconpassword",
      // set to 1 if you want permban from that server included into the banlist page.
      "include_in_banlist" => 0,
      // set this to 1 if you are using the chatlogger plugin setup for that server. (see http://www.bigbrotherbot.com/forums/index.php?topic=423.0)
      "chatlogger_activated" => 0
    ),
    2 => array(
      "name" => "COD",
      // Database connection for the B3
      "hostname" => "localhost",
      "database" => "databasename",
      "username" => "db-username",
      "password" => "db-password",
      //Do we have PunkBuster in this game? If yes, we'll use RCON to ban/unban PB-guids - Set it to "0" if not.
      "PBactive" => "0",
      //Set up rcon for talkback (need chatlogger plugin installed) and PB banning facilities
      "rcon_ip" => "127.0.0.1",
      "rcon_port" => "28960",
      "rcon_pass" => "rconpassword",
      // set to 1 if you want permban from that server included into the banlist page.
      "include_in_banlist" => 0,
      // set this to 1 if you are using the chatlogger plugin setup for that server. (see http://www.bigbrotherbot.com/forums/index.php?topic=423.0)
      "chatlogger_activated" => 0
    ),
    3 => array(
      "name" => "COD2",
      // Database connection for the B3
      "hostname" => "localhost",
      "database" => "databasename",
      "username" => "db-username",
      "password" => "db-password",
      //Do we have PunkBuster in this game? If yes, we'll use RCON to ban/unban PB-guids - Set it to "0" if not.
      "PBactive" => "0",
      //Set up rcon for talkback (need chatlogger plugin installed) and PB banning facilities
      "rcon_ip" => "127.0.0.1",
      "rcon_port" => "28960",
      "rcon_pass" => "rconpassword",
      // set to 1 if you want permban from that server included into the banlist page.
      "include_in_banlist" => 0,
      // set this to 1 if you are using the chatlogger plugin setup for that server. (see http://www.bigbrotherbot.com/forums/index.php?topic=423.0)
      "chatlogger_activated" => 0
    )
  )
);
?>
