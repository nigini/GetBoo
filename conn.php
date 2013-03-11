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

	$from_conn = true;
	if(!$from_upgrade) include("config.inc.php");
	
	// Set include path
	ini_set('include_path',ini_get('include_path'). PATH_SEPARATOR . ABSPATH . 'includes' . DIRECTORY_SEPARATOR . 'pear' . DIRECTORY_SEPARATOR . PATH_SEPARATOR);
	
	include_once('MDB2.php');
	
	$dsn = array(
	    'phptype'  => $dbtype,
	    'username' => $dbuname,
	    'password' => $dbpass,
	    'hostspec' => $dbhost,
	    'database' => $dbname
	);
	if(!empty($dbport))
		$dsn['port'] = $dbport;
	
	// create MDB2 instance
	$dblink =& MDB2::singleton($dsn);
	if (PEAR::isError($dblink)) {
	    header("Location: error.php");
	}
	
	// set the default fetchmode
	$dblink->setFetchMode(MDB2_FETCHMODE_ASSOC);
	
	if($dbtype == "pgsql")
		$dateDiffQuery = "EXTRACT(epoch FROM now() -";
	else
		$dateDiffQuery = "(now() -";
		
	define("DATE_DIFF_SQL", $dateDiffQuery);
	define("DAY_SECONDS", 86400);
    
	require_once('includes/config.php');
?>