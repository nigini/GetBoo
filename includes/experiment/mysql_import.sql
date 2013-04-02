-- --------------------------------------------------------
-- Remove old tables that will be created in memory
-- --------------------------------------------------------
SET GLOBAL tmp_table_size = 1024 * 1024 * 1024 * 5;
SET GLOBAL max_heap_table_size = 1024 * 1024 * 1024 * 5;
DROP TABLE IF EXISTS `gb_favourites`, `gb_tags`, `gb_tags_added`, `gb_tags_books`;

-- --------------------------------------------------------
-- Table structure for table `gb_favourites`
-- --------------------------------------------------------

CREATE TABLE `gb_favourites` (
  `ID` int(3) NOT NULL AUTO_INCREMENT,
  `Name` varchar(20) NOT NULL DEFAULT '',
  `Title` varchar(100) NOT NULL DEFAULT '',
  `FolderID` int(3) NOT NULL,
  `Url` varchar(1000) NOT NULL,
  `Description` varchar(150) DEFAULT '',
  `ADD_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `LAST_VISIT` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LAST_MODIFIED` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) MAX_ROWS=1500000 ENGINE=MEMORY  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- Table structure for table `gb_tags`
-- --------------------------------------------------------

CREATE TABLE `gb_tags` (
  `ID` int(4) NOT NULL AUTO_INCREMENT,
  `Title` varchar(50) NOT NULL,
  `Date_Added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) MAX_ROWS=250000 ENGINE=MEMORY  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------
-- Table structure for table `gb_tags_added`
-- --------------------------------------------------------

CREATE TABLE `gb_tags_added` (
  `B_ID` int(4) NOT NULL DEFAULT '0',
  `Date_Added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`B_ID`)
) MAX_ROWS=1500000 ENGINE=MEMORY DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Table structure for table `gb_tags_books`
-- --------------------------------------------------------

CREATE TABLE `gb_tags_books` (
  `B_ID` int(4) NOT NULL DEFAULT '0',
  `T_ID` int(4) NOT NULL DEFAULT '0',
  `Date_Added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`B_ID`,`T_ID`)
) MAX_ROWS=4000000 ENGINE=MEMORY DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Turn off table keys to make import faster!
-- --------------------------------------------------------
ALTER TABLE gb_favourites DISABLE KEYS;
ALTER TABLE gb_tags DISABLE KEYS;
ALTER TABLE gb_tags_books DISABLE KEYS;
ALTER TABLE gb_tags_added DISABLE KEYS;


LOAD DATA INFILE '1_books_output.txt' INTO TABLE gb_favourites CHARACTER SET 'utf8' FIELDS 
TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n';
LOAD DATA INFILE '2_tags_output.txt' INTO TABLE gb_tags CHARACTER SET 'utf8' FIELDS 
TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n';
LOAD DATA INFILE '3_relations_output.txt' INTO TABLE gb_tags_books CHARACTER SET 'utf8' FIELDS 
TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n';
LOAD DATA INFILE '4_pubbooks_output.txt' INTO TABLE gb_tags_added CHARACTER SET 'utf8' FIELDS 
TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n';

-- --------------------------------------------------------
-- Turn on table keys to reorganize them!
-- --------------------------------------------------------
ALTER TABLE gb_favourites ENABLE KEYS;
ALTER TABLE gb_tags ENABLE KEYS;
ALTER TABLE gb_tags_books ENABLE KEYS;
ALTER TABLE gb_tags_added ENABLE KEYS;
ALTER TABLE gb_tags ADD INDEX ( Title );

