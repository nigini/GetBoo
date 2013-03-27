* The data from the experiment was downloaded from: http://arvindn.livejournal.com/116137.html

* You should use "create_csv_files.php" to convert the needed data from the original file to 
a format that will be imported efficiently to MySQL database.

* Now that you have the 4 files you have to execute "mysql_import.sql" as root in the "getboo"
database. Whatchout:

  * This will delete and recreate the 4 tables related to BOOKMARKS and TAGS in the memory.

  * This will require 3 GB of RAM.

