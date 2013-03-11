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
	set_time_limit(0);
	include('header.php');
	echo("<h2>" . T_("Settings") . " -- " . T_("Inactive Users") . "</h2>\n");
	$dateDiff = (isset($_POST["dateDiff"]) && $_POST["dateDiff"] != "")?$_POST["dateDiff"]:7;
	echo("<div class=\"content\"><p>" . sprintf(T_("Removing inactive users (users who signed up more than %s days ago without activating their account)"),$dateDiff) . "...<br>");

	include('conn.php');
	require_once('includes/tags_functions.php');

	$Query = ("select session.name, session.datejoin AS formatted_time from " . TABLE_PREFIX . "session, " . TABLE_PREFIX . "activation where (session.name = activation.name and session.status='disabled' and " . DATE_DIFF_SQL . " datejoin)/" . DAY_SECONDS . " >= $dateDiff and activation.activated='N')");
	//var_dump($Query)
	$dbResult = $dblink->query($Query);

	$count = 0;
	$users = array();

  	while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
  	{
	  	$users[$count++] = $row;
  	}

	include("includes/f_deleteaccount.php");
  	foreach($users as $current_user)
  	{
	  	$uname = "{$current_user["name"]}";
	  	deleteUserAccount($uname, true);
	}
	if($count > 0)
		echo("<br><br><b>" . $count . "</b> " . T_("users have been deleted") . "!<br><a href=\"controlpanel.php\"><< Go back to Control Panel</a></p>");
	else
		echo("<br>" . T_("No") . " " . T_("users have been deleted") . ".");
	echo("<p><a href=\"manageusers.php\"><< " . T_("Back") . "</a></p>");
?>