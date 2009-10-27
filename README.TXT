##################################################################
#
# Echelon
# Web investigation tool for B3 (www.bigbrotherbot.com)
# (c) 2004, 2005 www.xlr8or.com (mailto:xlr8or@xlr8or.com)
#
# This program is free software and licensed under the terms of
# the GNU General Public License (GPL), version 2.
#
# http://www.gnu.org/copyleft/gpl.html
##################################################################

Echelon for B3
##################################################################
This tool is to investigate your playercrowd and see their B3 
history. See an overview of their warnings, kicks and (temp)bans. 
Also see which admin is kicking and banning ppl and their reasons 
for doing so.


Requirements:
##################################################################

- Website running php.
- website must have access to your B3 database.
- website must have rcon access to your game.


Installation:
##################################################################

1. Extract this package into the root of your website! It will 
create a directory called echelon and a dir called sql.

2. Create a MySQL database called echelon and a user (echelon) 
with a password (echelonb3) that has access from localhost (if 
your mysql is running on the same machine as your webserver) to 
this database. (Don't forget to flush the privileges)

3. Execute the echelon.sql script inside the directory 'sql' on 
the echelon database. This will create two tables and a few 
records.

4. Open 'Connections/inc_config.php' with your teksteditor and 
make sure the username and password for the b3 database(s) are set 
correctly.
MultiDB support requires some handywork... editing inc_config.php
correctly is very important. This config supplies you with a sample 
for 3 db's.

5. If your game has PunkBuster support, Echelon has the ability to
write to the pbbans.dat file for instant banning and unbanning of 
clients. This info can be set per server in b3connect.php.

6. ONLY IF YOU HAVE CHOSEN A DIFFERENT USERNAME AND/OR PASS UNDER 
POINT 2.: Open wwwvalidate.php with notepad and modify this 
connection so the username and pass match.

7. Point your browser to http://<yourwebsite>/echelon/index.php 
(or whatever the installation path is) and log in with 
'admin' and 'admin', change the password for this user, and have 
a bunch of fun!

8. You can modify the look and feel of Echelon by modifying the
css file called default.css inside the css-folder. A few examples
are included in that directory.


Upgrade Note:
##################################################################
This version of Echelon works with B3 v1.1.0+ If you're still using
B3 v1.0.0 you should use the database update as described below:

- Columns were added to the tables clients and penalties. 
To add these to your existing database, execute the following 
MySQL script on your database:
  ALTER TABLE clients ADD password VARCHAR(32),
    ADD login VARCHAR(16);
  ALTER TABLE penalties ADD data VARCHAR(255);


##################################################################
xlr8or - www.bigbrotherbot.com // www.xlr8or.com
