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
set_time_limit(0); // Unlimited execution time
ini_set('include_path',ini_get('include_path'). PATH_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'pear' . DIRECTORY_SEPARATOR . PATH_SEPARATOR);
define("NUMBER_OF_FIELDS", 18);
include("installFunctions.php");
include_once('MDB2.php');

	// Check if form submitted (install)
	if ($_POST['upgradeBtn'] || !empty($_FILES["config_file"]["name"])) //hack for ajax form..
	{
		$upgradeFrom = "";
		if(!empty($_FILES["config_file"]["name"])) // from config file
		{
			if($_FILES["config_file"]["name"] == "config.inc.php")
			{
				move_uploaded_file($_FILES["config_file"]["tmp_name"], dirname(__FILE__) . DIRECTORY_SEPARATOR . $_FILES["config_file"]["name"]);
				$from_conn = true; // from version 1.2
				$upgradeFrom = "config";
			}
			else
			{
				echo("<p class=\"error\">The configuration file must be named config.inc.php (otherwise rename it to match this format).</p>");
			}
		}
		else // from db information
		{
			$countVars = -1; //to account for the form button
			foreach($_POST as $key=>$currentPOST)
			{
				if(trim($currentPOST) != "")
				{
					$$key = $currentPOST;
					$countVars++;
				}
			}
			
			//minimum required fields for DB upgrade: type, host, name, uname 
			if(empty($dbtype) || empty($dbhost) || empty($dbname) || empty($dbuname))
				echo("<p class=\"error\">The form is incomplete</p>");
			else
			{
				// test database connection information
				if(!empty($dbport))
					$dbportStr = ":$dbport";
				$conn_dsn = "$dbtype://$dbuname:$dbpass@$dbhost$dbportStr/$dbname";
				$dblink = MDB2::connect($conn_dsn);
				if (MDB2::isError($dblink)) {
			        echo("<p class=\"error\">A connection cannot be established with the database.</p>");
				}
				else
				{
					$upgradeFrom = "database";
				}
			}
		}
		if(!empty($upgradeFrom))
			include("upgrade.php");
	}
	// Check if form submitted (install)
	if ($_POST['installBtn'])
	{
		define("VERSION_NUMBER", "1.04");
		// Retrieve form vars

		//var_dump($_POST);

		$countVars = -1; //to account for the form button
		foreach($_POST as $key=>$currentPOST)
		{
			if(trim($currentPOST) != "")
			{
				$$key = $currentPOST;
				$countVars++;
			}
		}

		$number_of_fields = NUMBER_OF_FIELDS;
		if(empty($dbport)) $number_of_fields--;
		if(empty($table_prefix)) $number_of_fields--;
		if(empty($dbpass)) $number_of_fields--;
		if(!isset($db_create)) $number_of_fields--;
		if($countVars != $number_of_fields)
			echo("<p class=\"error\">The form is incomplete</p>");
		else
		{
			// First create the database if we have to
			if($db_create == "on")
			{
				if(!empty($dbport))
					$dbportStr = ":$dbport";
				$conn_dsn = "$dbtype://$dbuname:$dbpass@$dbhost$dbportStr";
				$dblink = MDB2::connect($conn_dsn);
				if (MDB2::isError($dblink)) {
			        echo("<p class=\"error\">A connection cannot be established with the database.</p>");
			        $errorCreatingDB = true;
				}
				else
				{
					$dblink_temp = MDB2::connect($conn_dsn);
					$dblink_temp->loadModule('Manager');
					$dblink_temp->createDatabase($dbname);
					if (MDB2::isError($dblink_temp)) {
	                	echo("<p class=\"error\">Error when creating the database. Make sure you have the privileges to create a database and that a database with the same name doesn't exist.</p>");
						$errorCreatingDB = true;
			        }
				}
			}

			if(!$errorCreatingDB)
			{
				// test database connection information
				$conn_dsn = "$dbtype://$dbuname:$dbpass@$dbhost$dbportStr/$dbname";
				$dblink = MDB2::connect($conn_dsn);
				if (MDB2::isError($dblink)) {
			        echo("<p class=\"error\">A connection cannot be established with the database.</p>");
				}
				else
				{
					writeConfig($table_prefix, $dbhost, $dbport, $dbuname, $dbpass, $dbname, $dbtype);

					$from_install = true;
					include('../conn.php');
					include('../includes/user.php');
					$user = new User();
					
					// Load the right shema structure
					switch($dbtype)
					{
						case "mysqli":
						case "mysql": $structure = "mysql"; break;
						case "pgsql": $structure = "pgsql"; break;
					}

					// Create the tables from the sql file (with the script from phpBB)
					include('../includes/sql_parse.php');
					$sqlFile = "../includes/sql/" . $structure . "_structure.sql";
					$sql_query = @fread(@fopen($sqlFile, 'r'), @filesize($sqlFile));

					$sql_query = preg_replace('/gb_/', $table_prefix, $sql_query);

					$sql_query = remove_remarks($sql_query);
					$sql_query = split_sql_file($sql_query, ";");


					for ($i = 0; $i < sizeof($sql_query); $i++)
					{
						if (trim($sql_query[$i]) != '')
						{
							if (!($result = $dbResult = $dblink->query($sql_query[$i])))
							{
								echo("<p class=\"error\">Error when creating the database structure. Make sure the tables don't already exist.</p>");
								$errorCreatingStructure = true;
								// destroy config
								unlink("../config.inc.php");
								break;
							}
						}
					}

					if(!$errorCreatingStructure)
					{
						// Add config vars
						$SETTINGS['path_mod'] = "../";
						require_once('../includes/configuration.php');
						$cachedir = dirname(__FILE__) .'/cache/';
						$cachedir = str_replace('\\','/',$cachedir);
						$cachedir = str_replace('/install','',$cachedir);
						$website_dir = dirname(__FILE__);
						$website_dir = str_replace('\\','/',$website_dir);
						$website_dir = str_replace('/install','',$website_dir);
						$configVars = array("WEBSITE_NAME" => $website_name, "WEBSITE_LOCALE" => $website_locale, "WEBSITE_ROOT" => $website_root, "WEBSITE_DIR" => $website_dir, "USECACHE" => $usecache, "USE_DEMO" => $use_demo, "CURL_AVAILABLE" => $curl_available, "ANTI_SPAM" => $anti_spam, "CACHE_DIR" => $cachedir, "VERSION" => VERSION_NUMBER);
						foreach($configVars as $key => $configVar)
						{
							$result = Configuration::SetConfig($key, $configVar, "../");
							if(!$result)
							{
								$errors[] = ("Error when assigning the config variables");
								break;
							}
						}

						// Create admin account
						$passencrypt = $user->encryptPassword($admin_password);
						$Query = "insert into " . TABLE_PREFIX . "session (Name, Pass, Email, LastLog, DateJoin, Status, Style) " .
									"values('$admin_username','$passencrypt','$admin_email', now(), now(), 'admin', 'Auto')";
						//echo($Query . "<br>\n");
						$AffectedRows = $dblink->exec($Query);
						$Query = "INSERT INTO " . TABLE_PREFIX . "activation values ('$admin_username', '0', 'Y', NULL, '$admin_email')";
						$dbResult = $dblink->exec($Query);
						$AffectedRows += $dbResult;
						if($AffectedRows != 2)
						{
							$errors[] = ("Error when creating the admin user");
						}

						// Create demo account if true
						if($use_demo)
						{
							$passencryptDemo = $user->encryptPassword("demo");
							$Query = "insert into " . TABLE_PREFIX . "session (Name, Pass, Email, LastLog, DateJoin, Status, Style) " .
										"values('demo','$passencryptDemo','demo@getboo.com', now(), now(), 'normal', 'Auto')";
							//echo($Query . "<br>\n");
							$AffectedRows = $dblink->exec($Query);
							$Query = "INSERT INTO " . TABLE_PREFIX . "activation values ('demo', '0', 'Y', NULL, 'demo@getboo.com')";
							$dbResult = $dblink->exec($Query);
							$AffectedRows += $dbResult;
							if($AffectedRows != 2)
							{
								$errors[] = ("Error when creating the demo user</p>");
							}
						}

						// CHMOD 777 /cache
						//@chmod("../cache", 0777);
						//TODO: do we really need/want to chmod 777 the cache?

						// If we are here, its because it has been a success, or almost!
						if(count($errors) > 0)
						{
							echo("<p class=\"notice\">The installation has completed with these errors:</p>");
							foreach($errors as $error)
								echo("<p class=\"error\">$error</p>");
							echo("<p class=\"notice\">");
						}
						else
							echo("<p class=\"success\">The installation was successful!</p>");
						whatsNextDiv();
					}
				}
			}
		}
	}
?>