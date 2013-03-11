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

	session_start();

	$id = 0;
	if (isset($_GET['id']))
	{
	    $id = $_GET['id'];
	}

	require_once('includes/user.php');
	$user = new User();
	$username = $user->getUsername();
	if($username == "")
		$username = "system:guest"; //if no user logged on

	include('conn.php');
	include('includes/protection.php');
	$Query = sprintf("select url from " . TABLE_PREFIX . "favourites where id=%s", quote_smart($id));
	//echo($Query . "<br>\n");
	$dbResult = $dblink->query($Query);
	if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	{
		$url = "{$row["url"]}";
	}
	//echo("$url<br>");

	if ($url=="")
	{
		include('header.php');
		echo("<h2>" . T_("Redirect") . "</h2><p class=\"error\">" . T_("The bookmark cannot be found") . ".</p>");
		include('footer.php');
	}

	else
	{
		// update the bookmarkhits table
		//Check if its an admin accessing another account, so that we don't log his access
		if(!($user->isAdmin() && isset($_SESSION["oldname"])))
		{
			$Query = "
			  INSERT INTO " . TABLE_PREFIX . "bookmarkhits
			  (BookmarkID, Name, Time, IP)
			  VALUES
			  ('$id', '$username', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "')
			";

			$dbResult = $dblink->query($Query);
		}

		// update the favourites table
		// TODO: Check here also for the dates, otherwise remove
		$Query = ("select ADD_DATE, LAST_MODIFIED from " . TABLE_PREFIX . "favourites where id = '" . $id . "'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$add_date = "{$row["add_date"]}";
			$last_mod = "{$row["last_modified"]}";
		}

		$Query = ("UPDATE " . TABLE_PREFIX . "favourites SET ADD_DATE = '" . $add_date . "', LAST_VISIT = NOW(), LAST_MODIFIED = '" . $last_mod . "' WHERE ID =" . $id);
		$dbResult = $dblink->query($Query);

		header ("Location: ". $url);
	}
	
?>