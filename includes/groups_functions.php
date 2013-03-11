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

	/* Functions used for the groups section
	 * Started on 12.18.05
	 */

	//Return true if the user is the manager of this group
	function checkIfManager ($group_id, $mname)
	{
		include('conn.php');
		$partOf = false;
		$Query = ("select manager from " . TABLE_PREFIX . "groups where group_id= " . $group_id . " and manager = '" . $mname . "'");
		$dbResult10 = $dblink->query($Query);
		if($row =& $dbResult10->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$partOf = true;
		}
		
		return $partOf;
	}

	//Return true if the user is part of this group
	function checkIfMember ($group_id, $mname)
	{
		include('conn.php');
		$partOf = false;
		$Query = ("select name from " . TABLE_PREFIX . "gsubscriptions where group_id= " . $group_id . " and name = '" . $mname . "'");
		$dbResult11 = $dblink->query($Query);
		if($row =& $dbResult11->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$partOf = true;
		}
		
		return $partOf;
	}

	//Return the group_id of the group, providing the name
	function returnGroupID ($gname)
	{
		include('conn.php');
		/* Retrieve the group_id of the group */

		$Query = ("select group_id from " . TABLE_PREFIX . "groups where (group_name = '$gname')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_id = "{$row["group_id"]}";
		}
		
		return $rec_id;
	}

	//Return the folderID of the folder, providing the name and the username
	function returnFolderID ($fname, $uname)
	{
		include('conn.php');

		$Query = ("select ID from " . TABLE_PREFIX . "folders where (title = '" . $fname . "' and name = '" . $uname . "' and pid='-1')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_id = "{$row["id"]}";
		}
		
		return $rec_id;
	}

	//Return the name of the folder, providing the id
	function returnFolderName ($fid)
	{
		include('conn.php');

		$Query = ("select title from " . TABLE_PREFIX . "folders where (id = '$fid')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_name = "{$row["title"]}";
		}
		
		return $rec_name;
	}

	//Return the name of the group
	function returnGroupName ($group_id)
	{
		include('conn.php');
		/* Retrieve the name of the group */

		$Query = ("select group_name from " . TABLE_PREFIX . "groups where (group_id = '$group_id')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_name = "{$row["group_name"]}";
		}
		
		return $rec_name;
	}

	//Return the group folder_id of a member, providing an ID
	function returnGroupFolderWithID ($group_id, $membername)
	{
		include('conn.php');
		$group_name = returnGroupName($group_id);
		$Query = ("select id from " . TABLE_PREFIX . "folders where (pid = '-1' and title = '" . $group_name . "' and name = '" . $membername . "')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_id = "{$row["id"]}";
		}
		
		return $rec_id;
	}

	//Return the description of the group
	function returnGroupDesc ($group_id)
	{
		include('conn.php');
		/* Retrieve the desc of the group */

		$Query = ("select description from " . TABLE_PREFIX . "groups where (group_id = '$group_id')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_desc = "{$row["description"]}";
		}
		
		return $rec_desc;
	}

	//Return true if the group is private
	function isGroupPrivate ($group_id)
	{
		include('conn.php');
		$private = false;
		$Query = ("select password from " . TABLE_PREFIX . "groups where (group_id = '$group_id')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_pass = "{$row["password"]}";
			$private = ($rec_pass != "");
		}
		
		return $private;
	}

	//Return the name of the priv
	function returnPrivName ($priv_id)
	{
		switch($priv_id)
		{
			case 0: $priv_desc = T_("Disabled"); break;
			case 1: $priv_desc = T_("Member"); break;
			case 2: $priv_desc = T_("Moderator"); break;
		}
		return $priv_desc;
	}

	//Change the priv of a member
	function changeMemberPriv ($memberName, $newPriv)
	{
		include('conn.php');
		$Query = ("select date_join from " . TABLE_PREFIX . "gsubscriptions where name = '" . $memberName . "'");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			$rec_date = "{$row["date_join"]}";
		$Query = ("UPDATE " . TABLE_PREFIX . "gsubscriptions SET Priv = '" . $newPriv . "', date_join = '" . $rec_date . "' where name = '" . $memberName . "'");
		$dbResult = $dblink->query($Query);
		
	}

	//Return the priv of a member
	function returnMemberPriv ($memberName)
	{
		include('conn.php');
		$Query = ("select priv from " . TABLE_PREFIX . "gsubscriptions where name = '" . $memberName . "'");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			$rec_priv = "{$row["priv"]}";
		
		return $rec_priv;
	}

	//Create the folder for the group, in the member's bookmarks
	function createMemberGroupFolder ($username, $gName, $gDesc)
	{
		include('conn.php');
		$Query = "INSERT INTO " . TABLE_PREFIX . "folders (Name , Title , Description , PID) " . "values('" . $username . "','" . $gName . "','" . $gDesc . "','-1') ";
		$AffectedRows = $dblink->exec($Query);
		
		$group_id = returnGroupID($gName);
		$folder_id = returnFolderID($gName, $username);
		$success2 = insertGFolderRecord($group_id, $folder_id);
		if($success2 != 1)
			$AffectedRows = -1;
		return $AffectedRows;
	}

	//Insert the record of the new folder for the group in the table gfolders
	function insertGFolderRecord ($group_id, $folder_id)
	{
		include('conn.php');
		$Query = "INSERT INTO " . TABLE_PREFIX . "gfolders ( Group_ID , FolderID ) " . "values('" . $group_id . "', '" . $folder_id . "')";
		$AffectedRows = $dblink->exec($Query);
		
		return $AffectedRows;
	}

	//Return the number of members in this group
	function returnNumberMembers ($group_id)
	{
		include('conn.php');

		$Query = ("select count(*) as Total from " . TABLE_PREFIX . "gsubscriptions where (group_id = '$group_id')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_total = "{$row["total"]}";
		}
		//$rec_total++; Don't include the manager
		
		return $rec_total;
	}
?>