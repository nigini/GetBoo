Product Name			GetBoo
Product URL			http://www.getboo.com
Product Description		Web-based bookmarking system

Release Version			1.04
Release Date 			April 7th 2008

Author's Name			Maxime Chartrand-Dumas
Author's Email			maxime@getboo.com
License				GNU General Public License (GPL)

==== INSTALLATION ====

=== QUICK INSTALLATION ===

  - Upload all the files on your webserver or localhost
  - Point to the root folder of the script to start the installation script
  - Fill out the form information and then submit
  - Remove the /install folder

=== LONG INSTALLATION ===

  - Upload all the files on your webserver or localhost
  - Create a database or use an existing one and import the sql file includes/sql/structure1.0.sql
  - Move the file install/config.example.php to its parent folder, and rename it to config.inc.php
  - Open this file and edit the database information and configuration values
    - Set TABLE_PREFIX to gb_ (if you want to use another prefix, first modify the sql file accordingly)
  - Open your MySQL database and add an admin account (status="admin") in table gb_session
    - If you want to offer a demo account, add a another user demo/demo and status="normal" (this can be added later when you are logged in)
  - On *nix platforms, set the user (e.g. Apache) for the files (e.g. <code>chown www-data.www-data -R getboo/</code>)
  - Remove the install/ folder

==== UPGRADING ====

=== From 1.0 to latest ===

  - Move your config.inc.php file to another folder (makes the upgrade process faster)
  - Replace your files with the new release (if you use caching, you could backup your cache folder)
  - Point to the root folder of the script to start the upgrade script
  - Either load your config file at the end of the script or fill out the form information (except the admin configuration section).
  - Click the "Upgrade" button
  - Remove the /install folder

=== From 1.0 to 1.01 ===

  - Move your config.inc.php file to another folder
  - Replace your files with the new release (if you use caching, you could backup your cache folder)
  - Move your config.inc.php file to the root folder of the application
    - Insert after line 91 (''define("ANTI_SPAM", true);'') the following line: ''define("VERSION", "1.01");''
  - Remove the install/ folder

--> See http://wiki.getboo.com/install for more information

==== TRANSLATION ====

--> See http://wiki.getboo.com/translations for more information
I would be glad if you could help!

==== LINKS ====

GetBoo Project:
http://sourceforge.net/projects/getboo/

GetBoo Website
http://www.getboo.com/

GetBoo Wiki:
http://wiki.getboo.com/

GetBoo Blog:
http://blog.getboo.com/

GetBoo SVN
Command: svn co https://getboo.svn.sourceforge.net/svnroot/getboo getboo
Web: http://getboo.svn.sourceforge.net/viewvc/getboo/

Help forum:
https://sourceforge.net/forum/forum.php?forum_id=686367

Bug reports:
https://sourceforge.net/tracker/?group_id=194055&atid=947894

Feature requests:
https://sourceforge.net/tracker/?group_id=194055&atid=947897

User-submitted patches:
https://sourceforge.net/tracker/?group_id=194055&atid=947896