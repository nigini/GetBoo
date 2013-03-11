<?php
/***************************************************************************
Copyright (C) 2005-2008 GetBoo project
http://sourceforge.net/projects/getboo/
http://www.getboo.com/

This file is part of GetBoo.

GetBoo is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

GetBoo is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with GetBoo; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
***************************************************************************/

/**
 * Upgrade script
 * Started on July 1, 2007
 * TODO:
 */
 	ini_set('include_path',ini_get('include_path'). PATH_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'pear' . DIRECTORY_SEPARATOR . PATH_SEPARATOR);
 	$errors = array();
	define("UPGRADE_TO_VERSION", "1.04");
	$SETTINGS['path_mod'] = "../";
	if($upgradeFrom == "config")
	{
		define("LINES_NB_OLD", 100);
		$from_install = true;
		$from_upgrade = true;
		$configFileName = "config.inc.php";
		//For versions prior to 1.02, we need to escape the gettext support to avoid errors
		$lines = file ($configFileName);
	 	if(count($lines) > LINES_NB_OLD)
	 	{
	 		$configFileName = "config.inc.trucated.php";
	 		$handle = fopen($configFileName, 'w');
	 		for($i=0; $i < 100; $i++)
	 		{
				fwrite($handle, $lines[$i]);
	 		}
			fwrite($handle, "?>");
			fclose($handle);
			unlink("config.inc.php");
	 	}

		include($configFileName);
		if(!defined("VERSION_NUMBER") && count($lines) > LINES_NB_OLD)
			define("VERSION_NUMBER", "1.0");
			
		unlink($configFileName);

		// For upgrade purposes, we assume that if no dbtype and dbport was provided (versions 1.03 and before), we default to mysql
		if(empty($dbtype))
			$dbtype = "mysql";
		if(empty($dbport))
			$dbport = "";
			
		// Create new config file
		$table_prefix = TABLE_PREFIX;
		writeConfig($table_prefix, $dbhost, $dbport, $dbuname, $dbpass, $dbname, $dbtype);
		
		$from_upgrade = false;
			
		if(!defined("VERSION_NUMBER"))
		{
			require_once('../includes/configuration.php');
			$configs = Configuration::LoadConfig("VERSION", "../");
			define("VERSION_NUMBER", $configs['VERSION']);
		}
		
		$from_install = false;
	}
	else if($upgradeFrom == "database")
	{
		// Create new config file
		writeConfig($table_prefix, $dbhost, $dbport, $dbuname, $dbpass, $dbname, $dbtype);

		// Get version number from db. If no config, then version is prior to 1.02
		require_once('../includes/configuration.php');
		$configs = Configuration::LoadConfig("VERSION", "../");
		if(empty($configs['VERSION']))
			$configs['VERSION'] = "1.01";
		define("VERSION_NUMBER", $configs['VERSION']);
		$from_upgrade = false;
	}

	include("../conn.php");
	
	// Do changes from current version to the latest
	switch(VERSION_NUMBER)
	{
		case "1.0":
		case "1.01":
			//Create configs table to database
			$Query = "CREATE TABLE " . TABLE_PREFIX . "configs (
						config_name VARCHAR( 100 ) NOT NULL ,
						config_value VARCHAR( 255 ) NOT NULL ,
						config_description TEXT NOT NULL ,
						PRIMARY KEY ( config_name )
						) ENGINE = MYISAM COMMENT = 'Configuration variables';";
			$AffectedRows = $dblink->exec($Query);
			if($AffectedRows != 0)
				$errors[] = ("Error when creating the configs table in the database");

			//Move configs to database
			$Query = "INSERT INTO " . TABLE_PREFIX . "configs VALUES ('IS_GETBOO', '0', 'Boolean set to true only for GetBoo.com where certain functions/pages appear')
						, ('WEBSITE_NAME', '', 'Name of the GetBoo installation')
						, ('WEBSITE_LOCALE', '', 'Locale for the translation in use')
						, ('WEBSITE_ROOT', '', 'Root of the installation. Only used at certain places when the absolute url is required. Add slash / at the end of the url')
						, ('WEBSITE_DIR', '', 'Real directory path where the script resides on the server (no localhost or www url)')
						, ('WAITTIME', '600', 'Time to wait after a user has 3 unsuccessful login attemps, in seconds')
						, ('ONLINE_TIMEOUT', '600', 'Delay of inactivity for users to be considered online, in seconds')
						, ('GROUPS_FID', '-1', 'Folder ID (virtual) of the groups folder')
						, ('MAIN_FID', '0', 'Folder ID (virtual) of the main folder containing the user\'s bookmarks')
						, ('TAGS', '1', 'Boolean determining if the users can add and modify their bookmarks to make them public')
						, ('TAGS_PER_PAGE', '10', 'Number of bookmarks displayed per page for the social bookmarking part (10, 20, 30, 40, 50)')
						, ('USER_TIMEOUT', '1800', 'Delay of inactivity for users before their session expires, in seconds')
						, ('NEWS_MSG_LENGTH', '325', 'Number of chars to display in the news section for the truncated version of the news')
						, ('NEWS_PER_PAGE', '5', 'Number of news to display in the news section')
						, ('SAME_IP_NEW_ACCONT_DELAY', '48', 'Delay (hours) for a member to register a new account with the same IP address (anti-spam protection measure)')
						, ('MAXIMUM_PAGES_RECENT_TAGS', '5', 'Maximum number of pages for the recent tags (bookmarks)')
						, ('PUBLIC_TIMEOUT', '60', 'Minimum number of days the member has to be registered before being able to display its public bookmarks in the recent tags page (anti-spam protection measure)')
						, ('USER_MAX_TIMEOUT', '3600', 'Maximum number of seconds the member can be inactive before his session expires')
						, ('DEBUG', '0', 'Set to true to use functions to debug if you need to test your scripts/add-ons. Check with the includes/debug.php file for a list of functions available to use. (true/false)')
						, ('USECACHE', '', 'Use caching of public bookmarks pages for faster execution (true/false)')
						, ('CACHE_DIR', '', 'Directory to store the cached pages. Must be chmod 777 (done by the installation script by default)')
						, ('USE_DEMO', '', 'Display the demo account to visitors. Is created during installation, otherwise create a demo/demo account yourself if enabled after. (true/false)')
						, ('CURL_AVAILABLE', '', 'Boolean to enable curl (library) functions')
						, ('ANTI_SPAM', '', 'Boolean to enable anti-spam measures if the site experiences spamming')
						, ('VERSION', '', 'Version number of the application')";
			$AffectedRows = $dblink->exec($Query);
			if($AffectedRows != 25)
				$errors[] = ("Error when updating the config variables in the database (v1.02)");
			require_once('../includes/configuration.php');
			$website_dir = dirname(__FILE__);
			$website_dir = str_replace('\\','/',$website_dir);
			$website_dir = str_replace('/install','',$website_dir);

			if($upgradeFrom == "config")
			{
				$cachedir = str_replace('\\','/',CACHE_DIR);
				$cachedir = str_replace('/install','',$cachedir);
				$configVars = array("IS_GETBOO" => IS_GETBOO, "WEBSITE_NAME" => WEBSITE_NAME, "WEBSITE_LOCALE" => WEBSITE_LOCALE, "WEBSITE_ROOT" => WEBSITE_ROOT, "WEBSITE_DIR" => $website_dir, "WAITTIME" => WAITTIME, "ONLINE_TIMEOUT" => ONLINE_TIMEOUT, "GROUPS_FID" => GROUPS_FID, "MAIN_FID" => MAIN_FID, "TAGS" => TAGS, "TAGS_PER_PAGE" => TAGS_PER_PAGE, "USER_TIMEOUT" => USER_TIMEOUT, "NEWS_MSG_LENGTH" => NEWS_MSG_LENGTH, "NEWS_PER_PAGE" => NEWS_PER_PAGE, "SAME_IP_NEW_ACCONT_DELAY" => SAME_IP_NEW_ACCONT_DELAY, "MAXIMUM_PAGES_RECENT_TAGS" => MAXIMUM_PAGES_RECENT_TAGS, "PUBLIC_TIMEOUT" => PUBLIC_TIMEOUT, "USER_MAX_TIMEOUT" => USER_MAX_TIMEOUT, "DEBUG" => DEBUG, "USECACHE" => USECACHE, "USE_DEMO" => USE_DEMO, "SNAP_URL" => SNAP_URL, "CURL_AVAILABLE" => CURL_AVAILABLE, "ANTI_SPAM" => ANTI_SPAM, "CACHE_DIR" => $cachedir, "VERSION" => UPGRADE_TO_VERSION);
			}
			else if($upgradeFrom == "database")
			{
				$cachedir = dirname(__FILE__) .'/cache/';
				$cachedir = str_replace('\\','/',$cachedir);
				$cachedir = str_replace('/install','',$cachedir);
				$configVars = array("IS_GETBOO" => 0, "WEBSITE_NAME" => $website_name, "WEBSITE_LOCALE" => $website_locale, "WEBSITE_ROOT" => $website_root, "WEBSITE_DIR" => $website_dir, "USECACHE" => $usecache, "USE_DEMO" => $use_demo, "SNAP_URL" => $snap_url, "CURL_AVAILABLE" => $curl_available, "ANTI_SPAM" => $anti_spam, "CACHE_DIR" => $cachedir, "VERSION" => UPGRADE_TO_VERSION);
			}

			foreach($configVars as $key => $configVar)
			{
				$result = Configuration::SetConfig($key, $configVar, "../");
				if(!$result)
				{
					$errors[] = ("Error when updating the config variable named $key");
				}
			}
		case "1.02":
			//Change configs vars
			$totalAffectedRows = 0;

			$Query = "INSERT INTO " . TABLE_PREFIX . "configs VALUES ('NEWS', '0', 'Enable news module (true/false)')
						, ('USE_SCREENSHOT', '1', 'Enable screen shot capture of public bookmarks (true/false)')
						, ('SCREENSHOT_URL', 'http://images.websnapr.com/?size=S&url=%s', 'Screen shot application, with %s as the placeholder for the url variable')
						, ('CAPTCHA', '1', 'Enable captcha security during new user registration (true/false)')";
			$AffectedRows = $dblink->exec($Query);
			$totalAffectedRows += $AffectedRows;
			$Query = "UPDATE " . TABLE_PREFIX . "configs SET config_value = '1.03' WHERE config_name = 'VERSION'";
			$AffectedRows = $dblink->exec($Query);
			$totalAffectedRows += $AffectedRows;
			
			if(VERSION_NUMBER == "1.0" || VERSION_NUMBER == "1.01")
				$totalAffectedRows++;
			if($totalAffectedRows != 5)
				$errors[] = ("Error when updating the config variables in the database (v1.03)");
		case "1.03":
			$totalAffectedRows = 0;
			
			//Add extra configs information and configs_groups table
			$Query = "CREATE TABLE " . TABLE_PREFIX . "configs_groups (
				ID int(3) NOT NULL,
				title varchar(30) NOT NULL,
				description varchar(255) NOT NULL,
				PRIMARY KEY  (ID)
				) ENGINE=MyISAM COMMENT='Groups of configuration values';";
			$dbResult = $dblink->query($Query);
			
			$Query = "ALTER TABLE " . TABLE_PREFIX . "configs ADD config_type VARCHAR( 30 ) NOT NULL ,
				ADD config_group INT( 3 ) NOT NULL ,
				ADD config_choices text NOT NULL ;";
			$dbResult = $dblink->query($Query);
			
			$Query = "UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Enable curl (library) functions',config_type = 'boolean',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'CURL_AVAILABLE';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Directory to store the cached pages. Must be writable by the server.',config_type = 'string',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'CACHE_DIR';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Display the demo account to visitors. Is created during installation, otherwise create a demo/demo account yourself if enabled after',config_type = 'boolean',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'USE_DEMO';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Use caching of public bookmarks pages for faster execution',config_type = 'boolean',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'USECACHE';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Flag installation as development. Not recommended for production mode.',config_type = 'boolean',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'DEBUG';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Maximum number of seconds the member can be inactive before his session expires',config_type = 'integer',config_group = 3,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'USER_MAX_TIMEOUT';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Minimum number of days the member has to be registered before being able to display its public bookmarks in the recent tags page',config_type = 'integer',config_group = 2,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'PUBLIC_TIMEOUT';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Maximum number of pages for the recent tags (bookmarks)',config_type = 'integer',config_group = 3,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'MAXIMUM_PAGES_RECENT_TAGS';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Number of news to display in the news section',config_type = 'integer',config_group = 3,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'NEWS_PER_PAGE';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Delay (hours) for a member to register a new account with the same IP address',config_type = 'integer',config_group = 2,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'SAME_IP_NEW_ACCONT_DELAY';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Delay of inactivity for users before their session expires, in seconds',config_type = 'integer',config_group = 3,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'USER_TIMEOUT';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Number of chars to display in the news section for the truncated version of the news',config_type = 'integer',config_group = 3,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'NEWS_MSG_LENGTH';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Number of bookmarks displayed per page for the social bookmarking part',config_type = 'choices',config_group = 3,config_choices = '10,20,30,40,50' WHERE " . TABLE_PREFIX . "configs.config_name = 'TAGS_PER_PAGE';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'The users can add and modify their bookmarks to make them public',config_type = 'boolean',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'TAGS';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Folder ID (virtual) of the main folder containing the user''s bookmarks',config_type = 'integer',config_group = 0,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'MAIN_FID';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Delay of inactivity for users to be considered online, in seconds',config_type = 'integer',config_group = 3,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'ONLINE_TIMEOUT';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Folder ID (virtual) of the groups folder',config_type = 'integer',config_group = 0,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'GROUPS_FID';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Time to wait after a user has 3 unsuccessful login attemps, in seconds',config_type = 'integer',config_group = 2,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'WAITTIME';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Real directory path where the script resides on the server (no localhost or www url)',config_type = 'string',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'WEBSITE_DIR';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Root of the installation. Add slash / at the end of the url',config_type = 'string',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'WEBSITE_ROOT';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Name of the GetBoo installation',config_type = 'string',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'WEBSITE_NAME';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Locale for the translation in use',config_type = 'choices',config_group = 1,config_choices = 'en_US,fr_FR,es_ES,cs_CZ,de_DE' WHERE " . TABLE_PREFIX . "configs.config_name = 'WEBSITE_LOCALE';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'True only for GetBoo.com',config_type = 'boolean',config_group = 0,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'IS_GETBOO';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Enable anti-spam measures if the site experiences spamming',config_type = 'boolean',config_group = 2,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'ANTI_SPAM';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Version number of the application',config_type = 'string',config_group = 0,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'VERSION';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Enable news module',config_type = 'boolean',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'NEWS';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Enable screen shot capture of public bookmarks',config_type = 'boolean',config_group = 1,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'USE_SCREENSHOT';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Screen shot application, with %s as the placeholder for the url variable',config_type = 'choices',config_group = 1,config_choices = 'http://images.websnapr.com/?size=S&url=%s,http://spa.snap.com/preview/?url=%s,http://www.artviper.net/screenshots/screener.php?q=100&w=120&h=90&sdx=1024&sdy=768&url=%s&.jpg' WHERE " . TABLE_PREFIX . "configs.config_name = 'SCREENSHOT_URL';
			UPDATE " . TABLE_PREFIX . "configs SET config_description = 'Enable captcha security during new user registration',config_type = 'boolean',config_group = 2,config_choices = '' WHERE " . TABLE_PREFIX . "configs.config_name = 'CAPTCHA'";
			//split queries since PHP doesn't allow multiple queries
			$Query = trim($Query);
			$queries = explode(";", $Query);
			foreach($queries as $singleQuery)
			{
				$dbResult = $dblink->exec($singleQuery);
				$AffectedRows = $dbResult;
				$totalAffectedRows += $AffectedRows;
			} // 29 rows
			
			// Insert DATE_FORMAT config var
			$Query = "INSERT INTO " . TABLE_PREFIX . "configs VALUES ('DATE_FORMAT', 'F d, Y h:i:s A', 'The date format is the same as the PHP date function. Do not specify the timezone paramater (e).', 'string', 3, '')";
			$AffectedRows = $dblink->exec($Query);
			$totalAffectedRows += $AffectedRows; //1 row
			
			$Query = "INSERT INTO " . TABLE_PREFIX . "configs_groups (ID, title, description) VALUES
				(0, 'Hidden', 'Hidden Configuration values'),
				(1, 'Basic', 'Minimal Configuration settings'),
				(2, 'Security', 'Security features'),
				(3, 'Constants', 'GetBoo contants');
				";
			$AffectedRows = $dblink->exec($Query);
			$totalAffectedRows += $AffectedRows; //4 rows
			
			$Query = "UPDATE " . TABLE_PREFIX . "configs SET config_value = '1.04' WHERE config_name = 'VERSION'";
			$AffectedRows = $dblink->exec($Query);
			$totalAffectedRows += $AffectedRows; //1 row
			
			// Checks for previous versions bugs
			// Check if SNAP_URL config is still present (version 1.02 and before)
			$Query = "select config_value from " . TABLE_PREFIX . "configs WHERE config_name = 'SNAP_URL'";
			$dbResult = $dblink->query($Query);
			if(count($dbResult->fetchRow()) == 1)
			{
				$Query = "DELETE FROM " . TABLE_PREFIX . "configs WHERE config_name = 'SNAP_URL'";
				$AffectedRows = $dblink->exec($Query);
			}
			
			// Check if USECACHE is set to "false", change to 0 (version 1.02 and before)
			$Query = "select config_value from " . TABLE_PREFIX . "configs WHERE config_name = 'USECACHE' AND config_value='false'";
			$dbResult = $dblink->query($Query);
			if(count($dbResult->fetchRow()) == 1)
			{
				$Query = "UPDATE " . TABLE_PREFIX . "configs SET config_value = '0' WHERE config_name = 'USECACHE'";
				$AffectedRows = $dblink->exec($Query);
			}

			if($totalAffectedRows != 35)
				$errors[] = ("Error when updating the config variables in the database (v1.04)");
		break;
	}
	if(count($errors) > 0)
	{
		echo("<p class=\"notice\">The upgrade process encountered the following errors:</p>");
		foreach($errors as $error)
			echo("<p class=\"error\">$error</p>");
	}
	else
	{
		echo("<p class=\"success\">The upgrade from version " . VERSION_NUMBER . " to " . UPGRADE_TO_VERSION . " was successful!</p>");
	}
	whatsNextDiv();
?>
