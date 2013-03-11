-- --------------------------------------------------------

--
-- Table structure for table activation
--

CREATE TABLE gb_activation (
  Name varchar(20) NOT NULL default '',
  Id varchar(50) NOT NULL default '',
  Activated char(1) NOT NULL default 'N',
  IP varchar(15) default NULL,
  Email varchar(100) NOT NULL default '',
  PRIMARY KEY  (Name)
);

-- --------------------------------------------------------

--
-- Table structure for table bookexportimport
--

CREATE TABLE gb_bookexportimport (
  ID serial NOT NULL,
  Name varchar(20) NOT NULL default '',
  Method char(2) NOT NULL default '',
  Time timestamp NOT NULL,
  IP varchar(15) NOT NULL default '',
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table bookmarkhits
--

CREATE TABLE gb_bookmarkhits (
  BookmarkID integer NOT NULL default '0',
  Name varchar(20) NOT NULL default '',
  Time timestamp NOT NULL,
  IP varchar(15) NOT NULL default '',
  PRIMARY KEY  (BookmarkID,Name,Time)
);

-- --------------------------------------------------------

--
-- Table structure for table captchahits
--

CREATE TABLE gb_captchahits (
  ID serial NOT NULL,
  Code varchar(50) NOT NULL,
  Entered varchar(50) NOT NULL,
  Username varchar(20) NOT NULL,
  Email varchar(100) NOT NULL,
  IP varchar(15) NOT NULL,
  Source varchar(20) NOT NULL,
  Time timestamp NOT NULL,
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table comments
--

CREATE TABLE gb_comments (
  ID serial NOT NULL,
  BID integer NOT NULL,
  Title varchar(100) NOT NULL,
  Comment text NOT NULL,
  Author varchar(20) NOT NULL,
  Date timestamp NOT NULL,
  ParentID integer NOT NULL,
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table config
--

CREATE TABLE gb_configs (
  config_name varchar(100) NOT NULL,
  config_value varchar(255) NOT NULL,
  config_description text NOT NULL,
  config_type varchar(30) NOT NULL,
  config_group integer NOT NULL,
  config_choices text NOT NULL,
  PRIMARY KEY  (config_name)
) ;

INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('WEBSITE_NAME', '', 'Name of the GetBoo installation', 'string', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('WEBSITE_LOCALE', '', 'Locale for the translation in use', 'choices', 1, 'en_US,fr_FR,es_ES,cs_CZ,de_DE');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('WEBSITE_ROOT', '', 'Root of the installation. Add slash / at the end of the url', 'string', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('WEBSITE_DIR', '', 'Real directory path where the script resides on the server (no localhost or www url)', 'string', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('TAGS', '1', 'The users can add and modify their bookmarks to make them public', 'boolean', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('DEBUG', '0', 'Functions to debug if you need to test your scripts/add-ons. Not recommended for production mode.', 'boolean', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('USE_DEMO', '', 'Display the demo account to visitors. Is created during installation, otherwise create a demo/demo account yourself if enabled after', 'boolean', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('USECACHE', '', 'Use caching of public bookmarks pages for faster execution', 'boolean', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('CACHE_DIR', '', 'Directory to store the cached pages. Must be writable by the server.', 'string', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('NEWS', '0', 'Enable news module', 'boolean', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('CURL_AVAILABLE', '', 'Enable curl (library) functions', 'boolean', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('USE_SCREENSHOT', '1', 'Enable screen shot capture of public bookmarks', 'boolean', 1, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('SCREENSHOT_URL', 'http://images.websnapr.com/?size=S&url=%s', 'Screen shot application, with %s as the placeholder for the url variable', 'choices', 1, 'http://images.websnapr.com/?size=S&url=%s,http://spa.snap.com/preview/?url=%s,http://www.artviper.net/screenshots/screener.php?q=100&w=120&h=90&sdx=1024&sdy=768&url=%s&.jpg');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('ANTI_SPAM', '', 'Enable anti-spam measures if the site experiences spamming', 'boolean', 2, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('CAPTCHA', '1', 'Enable captcha security during new user registration', 'boolean', 2, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('DATE_FORMAT', 'F d, Y h:i:s A', 'The date format is the same as the PHP date function. Do not specify the timezone paramater (e).', 'string', 3, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('USER_MAX_TIMEOUT', '3600', 'Maximum number of seconds the member can be inactive before his session expires', 'integer', 3, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('PUBLIC_TIMEOUT', '60', 'Minimum number of days the member has to be registered before being able to display its public bookmarks in the recent tags page', 'integer', 2, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('MAXIMUM_PAGES_RECENT_TAGS', '5', 'Maximum number of pages for the recent tags (bookmarks)', 'integer', 3, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('NEWS_PER_PAGE', '5', 'Number of news to display in the news section', 'integer', 3, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('SAME_IP_NEW_ACCONT_DELAY', '48', 'Delay (hours) for a member to register a new account with the same IP address', 'integer', 2, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('USER_TIMEOUT', '1800', 'Delay of inactivity for users before their session expires, in seconds', 'integer', 3, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('NEWS_MSG_LENGTH', '325', 'Number of chars to display in the news section for the truncated version of the news', 'integer', 3, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('TAGS_PER_PAGE', '10', 'Number of bookmarks displayed per page for the social bookmarking part', 'choices', 3, '10,20,30,40,50');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('MAIN_FID', '0', 'Folder ID (virtual) of the main folder containing the user''s bookmarks', 'integer', 0, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('ONLINE_TIMEOUT', '600', 'Delay of inactivity for users to be considered online, in seconds', 'integer', 3, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('GROUPS_FID', '-1', 'Folder ID (virtual) of the groups folder', 'integer', 0, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('WAITTIME', '600', 'Time to wait after a user has 3 unsuccessful login attemps, in seconds', 'integer', 2, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('IS_GETBOO', '0', 'True only for GetBoo.com', 'boolean', 0, '');
INSERT INTO gb_configs (config_name, config_value, config_description, config_type, config_group, config_choices) VALUES ('VERSION', '', 'Version number of the application', 'string', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table configs_groups
--

CREATE TABLE gb_configs_groups (
  ID integer NOT NULL,
  title varchar(30) NOT NULL,
  description varchar(255) NOT NULL,
  PRIMARY KEY  (ID)
) ;

INSERT INTO gb_configs_groups (ID, title, description) VALUES (0, 'Hidden', 'Hidden Configuration values');
INSERT INTO gb_configs_groups (ID, title, description) VALUES (1, 'Basic', 'Minimal Configuration settings');
INSERT INTO gb_configs_groups (ID, title, description) VALUES (2, 'Security', 'Security features');
INSERT INTO gb_configs_groups (ID, title, description) VALUES (3, 'Constants', 'GetBoo contants');

-- --------------------------------------------------------

--
-- Table structure for table ebhints
--

CREATE TABLE gb_ebhints (
  ID serial NOT NULL,
  Popup char(1) NOT NULL default '0',
  Name varchar(20) NOT NULL default '',
  Time timestamp NOT NULL,
  IP varchar(15) NOT NULL default '',
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table favourites
--

CREATE TABLE gb_favourites (
  ID serial NOT NULL,
  Name varchar(20) NOT NULL default '',
  Title varchar(100) NOT NULL default '',
  FolderID integer NOT NULL default '0',
  Url text NOT NULL,
  Description varchar(150) default '',
  ADD_DATE timestamp NOT NULL default now(),
  LAST_VISIT timestamp,
  LAST_MODIFIED timestamp,
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table folders
--

CREATE TABLE gb_folders (
  ID serial NOT NULL,
  Name varchar(20) NOT NULL default '',
  Title varchar(30) NOT NULL default '',
  Description varchar(150) default '',
  PID integer NOT NULL default '0',
  ADD_DATE timestamp NOT NULL default now(),
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table gfolders
--

CREATE TABLE gb_gfolders (
  ID serial NOT NULL,
  Group_ID integer NOT NULL default '0',
  FolderID integer NOT NULL default '0',
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table groups
--

CREATE TABLE gb_groups (
  Group_ID serial NOT NULL,
  Group_Name varchar(20) NOT NULL default '',
  Manager varchar(20) NOT NULL default '',
  Description varchar(100) NOT NULL default '',
  Password varchar(50) default NULL,
  Date_Created timestamp NOT NULL,
  PRIMARY KEY  (Group_ID)
);

-- --------------------------------------------------------

--
-- Table structure for table gsubscriptions
--

CREATE TABLE gb_gsubscriptions (
  ID serial NOT NULL,
  Group_ID integer NOT NULL default '0',
  Name varchar(20) NOT NULL default '',
  Date_Join timestamp NOT NULL,
  Priv char(1) NOT NULL default '',
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table loginhits
--

CREATE TABLE gb_loginhits (
  ID serial NOT NULL,
  Name varchar(20) NOT NULL default '',
  Time timestamp NOT NULL,
  IP varchar(15) NOT NULL default '',
  Success char(1) NOT NULL default 'N',
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table news
--

CREATE TABLE gb_news (
  NewsID serial NOT NULL,
  Author varchar(20) NOT NULL default '',
  Title varchar(75) NOT NULL default '',
  Msg text NOT NULL,
  Date timestamp NOT NULL,
  PRIMARY KEY  (NewsID)
);

-- --------------------------------------------------------

--
-- Table structure for table newshits
--

CREATE TABLE gb_newshits (
  ID serial NOT NULL,
  NewsID integer NOT NULL default '0',
  Source char(1) NOT NULL default '',
  Time timestamp NOT NULL,
  IP varchar(15) NOT NULL default '',
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table searches
--

CREATE TABLE gb_searches (
  ID serial NOT NULL,
  Name varchar(20) NOT NULL default '',
  Keyword varchar(50) NOT NULL default '',
  Time timestamp NOT NULL,
  IP varchar(15) NOT NULL default '',
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table session
--

CREATE TABLE gb_session (
  Name varchar(20) NOT NULL default '',
  Pass varchar(50) NOT NULL default '',
  Email varchar(100) NOT NULL default '',
  PassHint varchar(150) NOT NULL default '',
  LastLog timestamp NOT NULL default now(),
  DateJoin timestamp NOT NULL default now(),
  Status varchar(20) NOT NULL default '',
  Style varchar(20) NOT NULL default 'Auto',
  LastActivity timestamp,
  PRIMARY KEY  (Name)
);

-- --------------------------------------------------------

--
-- Table structure for table tags
--

CREATE TABLE gb_tags (
  ID serial NOT NULL,
  Title varchar(50) NOT NULL,
  Date_Added timestamp NOT NULL,
  PRIMARY KEY  (ID)
);

-- --------------------------------------------------------

--
-- Table structure for table tags_added
--

CREATE TABLE gb_tags_added (
  B_ID integer NOT NULL default '0',
  Date_Added timestamp NOT NULL,
  PRIMARY KEY  (B_ID)
);

-- --------------------------------------------------------

--
-- Table structure for table tags_books
--

CREATE TABLE gb_tags_books (
  B_ID integer NOT NULL default '0',
  T_ID integer NOT NULL default '0',
  Date_Added timestamp NOT NULL,
  PRIMARY KEY  (B_ID,T_ID)
);