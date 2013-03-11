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
 * Installation functions
 * Started on July 1, 2007
 * TODO:
 */

function writeConfig(&$table_prefix, $dbhost, $dbport, $dbuname, $dbpass, $dbname, $dbtype)
{
	//Fix for table_prefix
	if(substr($table_prefix,-1,1) != "_" && $table_prefix != "")
		$table_prefix .= "_";

	// Use the example config to create the real one from the form information
	$configExample = file_get_contents("config.example.php", true);
		
	// Replace each var
	$configContent = str_replace("%%dbhost%%", $dbhost, $configExample);
	$configContent = str_replace("%%dbport%%", $dbport, $configContent);
	$configContent = str_replace("%%dbuname%%", $dbuname, $configContent);
	$configContent = str_replace("%%dbpass%%", $dbpass, $configContent);
	$configContent = str_replace("%%dbname%%", $dbname, $configContent);
	$configContent = str_replace("%%dbtype%%", $dbtype, $configContent);
	$configContent = str_replace("%%TABLE_PREFIX%%", $table_prefix, $configContent);

	// Write the file
	$handle = fopen("../config.inc.php", 'w');
	fwrite($handle, $configContent);
	fclose($handle);
}

function whatsNextDiv()
{
?>
	<div style="margin: 2em; font-size: small;">
	<h3>What's next?</h3>
	<ul>
		<li><b>Delete</b> the install/ folder.</li>
		<li>Find all the information in the <a href="http://wiki.getboo.com">projet's wiki</a></li>
		<li>Get the latest development news in the <a href="http://blog.getboo.com">projet's blog</a></li>
		<li>Track down the <a href="https://sourceforge.net/tracker/?group_id=194055&atid=947894">bugs</a> you find or get help in the <a href="https://sourceforge.net/forum/forum.php?forum_id=686367">forums</a>!</li>
		<li>Check for the translations available and learn how you can <a href="http://wiki.getboo.com/translations">translate GetBoo</a>!</a></li>
		<li>Once your site is popular, list your installation in this <a href="http://wiki.getboo.com/examples">wiki page</a>!</a></li>
	</ul>
	<a href="../">Enjoy using GetBoo! &rarr;</a>
	</div>
<?php	
}
//Start of phpBB3 functions
/**
* Determine if we are able to load a specified PHP module and do so if possible
*/
function can_load_dll($dll)
{
	return ((@ini_get('enable_dl') || strtolower(@ini_get('enable_dl')) == 'on') && (!@ini_get('safe_mode') || strtolower(@ini_get('safe_mode')) == 'off')) ? true : false;
}

/**
* Returns an array of available DBMS with some data, if a DBMS is specified it will only
* return data for that DBMS and will load its extension if necessary.
*/
function get_available_dbms($dbms = false, $return_unavailable = false)
{
	global $lang;
	$available_dbms = array(
		'firebird'	=> array(
			'LABEL'			=> 'FireBird',
			'SCHEMA'		=> 'firebird',
			'MODULE'		=> 'interbase',
			'DELIM'			=> ';;',
			'COMMENTS'		=> 'remove_remarks',
			'DRIVER'		=> 'firebird',
			'AVAILABLE'		=> true,
		),
		'mysqli'	=> array(
			'LABEL'			=> 'MySQL with MySQLi Extension',
			'SCHEMA'		=> 'mysql',
			'MODULE'		=> 'mysqli',
			'DELIM'			=> ';',
			'COMMENTS'		=> 'remove_remarks',
			'DRIVER'		=> 'mysqli',
			'AVAILABLE'		=> true,
		),
		'mysql'		=> array(
			'LABEL'			=> 'MySQL',
			'SCHEMA'		=> 'mysql',
			'MODULE'		=> 'mysql',
			'DELIM'			=> ';',
			'COMMENTS'		=> 'remove_remarks',
			'DRIVER'		=> 'mysql',
			'AVAILABLE'		=> true,
		),
		'mssql'		=> array(
			'LABEL'			=> 'MS SQL Server 2000+',
			'SCHEMA'		=> 'mssql',
			'MODULE'		=> 'mssql',
			'DELIM'			=> 'GO',
			'COMMENTS'		=> 'remove_comments',
			'DRIVER'		=> 'mssql',
			'AVAILABLE'		=> true,
		),
		'mssql_odbc'=>	array(
			'LABEL'			=> 'MS SQL Server [ ODBC ]',
			'SCHEMA'		=> 'mssql',
			'MODULE'		=> 'odbc',
			'DELIM'			=> 'GO',
			'COMMENTS'		=> 'remove_comments',
			'DRIVER'		=> 'mssql_odbc',
			'AVAILABLE'		=> true,
		),
		'oracle'	=>	array(
			'LABEL'			=> 'Oracle',
			'SCHEMA'		=> 'oracle',
			'MODULE'		=> 'oci8',
			'DELIM'			=> '/',
			'COMMENTS'		=> 'remove_comments',
			'DRIVER'		=> 'oracle',
			'AVAILABLE'		=> true,
		),
		'pgsql' => array(
			'LABEL'			=> 'PostgreSQL 7.x/8.x',
			'SCHEMA'		=> 'postgres',
			'MODULE'		=> 'pgsql',
			'DELIM'			=> ';',
			'COMMENTS'		=> 'remove_comments',
			'DRIVER'		=> 'postgres',
			'AVAILABLE'		=> true,
		),
		'sqlite'		=> array(
			'LABEL'			=> 'SQLite',
			'SCHEMA'		=> 'sqlite',
			'MODULE'		=> 'sqlite',
			'DELIM'			=> ';',
			'COMMENTS'		=> 'remove_remarks',
			'DRIVER'		=> 'sqlite',
			'AVAILABLE'		=> true,
		),
	);

	if ($dbms)
	{
		if (isset($available_dbms[$dbms]))
		{
			$available_dbms = array($dbms => $available_dbms[$dbms]);
		}
		else
		{
			return array();
		}
	}

	// now perform some checks whether they are really available
	foreach ($available_dbms as $db_name => $db_ary)
	{
		$dll = $db_ary['MODULE'];

		if (!@extension_loaded($dll) || !file_exists_incpath("MDB2" . DIRECTORY_SEPARATOR . "Driver" . DIRECTORY_SEPARATOR . $db_name.".php"))
		{
			#if (!can_load_dll($dll))
			#{
				if ($return_unavailable)
				{
					$available_dbms[$db_name]['AVAILABLE'] = false;
				}
				else
				{
					unset($available_dbms[$db_name]);
				}
				continue;
			#}
		}
		$any_db_support = true;
	}

	if ($return_unavailable)
	{
		$available_dbms['ANY_DB_SUPPORT'] = $any_db_support;
	}
	return $available_dbms;
}

/**
* Generate the drop down of available database options
*/
function dbms_select($default = '')
{
	global $lang;
	
	$available_dbms = get_available_dbms(false, false);
	$dbms_options = '';
	foreach ($available_dbms as $dbms_name => $details)
	{
		$selected = ($dbms_name == $default) ? ' selected="selected"' : '';
		$dbms_options .= '<option value="' . $dbms_name . '"' . $selected .'>' .$details['LABEL'] . '</option>';
	}
	return $dbms_options;
}

//END of phpBB3 functions

// From http://aidanlister.com/repos/v/function.file_exists_incpath.php
function file_exists_incpath ($file)
{
    $paths = explode(PATH_SEPARATOR, get_include_path());
 
    foreach ($paths as $path) {
        // Formulate the absolute path
        $fullpath = $path . DIRECTORY_SEPARATOR . $file;
 
        // Check it
        if (file_exists($fullpath)) {
            return $fullpath;
        }
    }
 
    return false;
}
?>
