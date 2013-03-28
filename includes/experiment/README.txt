* The data used in the experiment was downloaded from: http://arvindn.livejournal.com/116137.html

* You should use "create_csv_files.php" to convert the needed data from the original file to 
a format that will be imported efficiently to a MySQL database. More precisely this script will
generate 4 CSV files (each one containing data for a different table) called: 1_books_output.txt, 
2_tags_output.txt, 3_relations_output.txt, 4_pubbooks_output.txt. An example:

  * php create_csv_files.php delicious-rss-1250k getboo_username 2> /dev/null

* Now that you have the 4 CSV files you have to execute "mysql_import.sql" as database root in the 
"getboo" database. For example: 

  * mysql --database=getboo -u root -p < includes/experiment/mysql_import.sql 

* WARNING:

  * This scrip may fail every first execution because of some unkown reason (sorry!).

  * This will delete and recreate the 4 tables related to BOOKMARKS and TAGS in the memory.

  * This will set permission to MySQL to use at most 3 GB of RAM to store these tables (what will
		probably be used).

