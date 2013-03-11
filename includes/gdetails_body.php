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

	$Query = ("select group_name, group_id, description, manager, password from " . TABLE_PREFIX . "groups where (group_id = '$group_id')");
	//echo($Query . "<br>\n");
	$dbResult = $dblink->query($Query);

	if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	{
		$rec_id = "{$row["group_id"]}";
		$rec_name = "{$row["group_name"]}";
		$rec_manager = "{$row["manager"]}";
		$rec_description = "{$row["description"]}";
		$rec_password = "{$row["password"]}";
		echo(T_("Group name") . ": <b>$rec_name</b><br>" . T_("Description") . ": $rec_description<br>" . T_("Manager") . ": $rec_manager<br><b>");
		if($rec_password != "")
			echo("" . T_("Private group (requires password)") . "</b>");
		else
			echo("" . T_("Public group") . "</b>");
		$partOf = (checkIfManager($group_id, $username) || checkIfMember($group_id, $username));
		if($partOf)
		{
			$nbMembers = returnNumberMembers($group_id);
			echo("<br><br>\n" . T_("Number of members") . ": " . $nbMembers);
			echo("<br><br><form action=\"gmembers.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"submit\" class=\"genericButton\" value=\">> " . T_("See the list of members") . "\"></form>");
		}
	}
?>