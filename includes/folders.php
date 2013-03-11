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

	function get_group_path ($folderid, $uname, $showdesc = true)
	{
		include('conn.php');
		require_once('user.php');
		$user = new User();
		$style = $user->getStyle();

		$Query = ("select pid, title from " . TABLE_PREFIX . "folders where id='$folderid' and name='$uname'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$t = "{$row["title"]}";
			$pid = "{$row["pid"]}";
		}

		$group_id = $pid;

		if($showdesc)
			$desc = get_fdescription($folderid, $uname, $pid);

		if($t != null)
			$string = "<td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\" />/<input type=\"submit\" class=\"submitBookmarksPathFolder\" value=\"$t\" /></form></td></tr></table>";
		else
			$string = "</tr></table>";

		if($showdesc)
			$string .= "<table><tr><td class=\"folderDesc\">$desc</td></tr></table>";

		while ($group_id > 0)
		{

			$Query = "
				 SELECT id, pid, title
					FROM " . TABLE_PREFIX . "folders
				  WHERE id = '$group_id'
					 AND name = '" . $uname . "'";

			#print "<p><pre>$Query</pre><p>\n\n";

			$dbResult = $dblink->query($Query);
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$title = "{$row["title"]}";
				$pid2 = "{$row["pid"]}";
			}

			//$g = apb_group($row['group_id']);

			$string = ("<td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$group_id\" />/<input type=\"submit\" class=\"submitBookmarksPath\" value=\"$title\" /></form></td>$string");

			$group_id = $pid2;
		}

		$groupsStr = "";
		if($folderid=="-1")
	  		$groupsStr = ("<td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"-1\" />/<input type=\"submit\" class=\"submitBookmarksPathFolder\" value=\"" . T_("Groups") . "\" /></form></td>");
		elseif($pid=="-1")
			$groupsStr = ("<td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"-1\" />/<input type=\"submit\" class=\"submitBookmarksPath\" value=\"" . T_("Groups") . "\" /></form></td>");

  		$string = "<table class=\"bookmarksPath\"><tr><td valign=\"bottom\"><img src=\"images/style/$style/folder.GIF\" alt=\"Folder\" class=\"books_fimg\"></td><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"submit\" class=\"submitBookmarksPath\" value=\"" . T_("Main") . "\" /></form></td>$groupsStr$string";

		//echo("string: $string<br>");
		
		return $string;
	}

	function get_fdescription ($folderid, $uname, $pid=0)
	{
		if($folderid == "-1")
			$description = T_("Group folders");
		else
		{
			include('conn.php');
			if($pid == -1) // Group folder
			{
				include('groups_functions.php');
				$description = returnGroupDesc(returnGroupID(returnFolderName($folderid)));
			}
			else
			{
				$Query = ("select description from " . TABLE_PREFIX . "folders where id='$folderid' and name='$uname'");
				//echo($Query . "<br>\n");
				$dbResult = $dblink->query($Query);
				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$description = "{$row["description"]}";
				}
			}
			
		}
		return $description;
	}

	function get_ftitle ($folderid, $uname)
	{
		include('conn.php');
		$Query = ("select title from " . TABLE_PREFIX . "folders where id='$folderid' and name='$uname'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$title = "{$row["title"]}";
		}
		
		return $title;
	}

	function folders_dropdown($uname, $selectname)
	{
		echo("<select name=\"" . $selectname . "\" class=\"formtext\">\n");
		echo("<option value=\"0\">" . T_("Main") . "</option>\n");
		include('conn.php');
		$Query = ("select id, pid, title from " . TABLE_PREFIX . "folders where name='$uname' and pid = 0 order by title");
		echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$id = "{$row["id"]}";
			$pid = "{$row["pid"]}";
			$title = "{$row["title"]}";
			echo("<option value=\"" . $id . "\">" . $title . "</option>\n");
			folder_dropdown_children($uname, $id, 3);
		}

		//Include groups folders
		folder_dropdown_children($uname, -1, 0, true);

		echo("</select>\n");
		
	}

	function folder_dropdown_children($uname, $id, $count, $groups = false)
	{
		include('conn.php');
		$Query = ("select id, pid, title from " . TABLE_PREFIX . "folders where name='$uname' and pid = " . $id . " order by title");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$id = "{$row["id"]}";
			$pid = "{$row["pid"]}";
			$title = "{$row["title"]}";

			$strOut = str_repeat("&nbsp;", $count);
			if($groups)
				$strOut .= ("Groups: ");
			else
				$strOut .= ("- ");
			$strOut .= $title;
			echo("<option value=\"" . $id . "\">" . $strOut . "</option>\n");

			$count += 3;
			folder_dropdown_children($uname, $id, $count);
			$count -= 3;
		}
		
	}

	function copy_folder($id, $pid)
	{
		include('conn.php');
		$Query = ("select name, title, description from " . TABLE_PREFIX . "folders where id='" . $id . "'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$username = "{$row["name"]}";
			$title = "{$row["title"]}";
			$description = "{$row["description"]}";
		}
		$Query = "INSERT INTO " . TABLE_PREFIX . "folders (Name , Title , Description , PID) " . "values('" . $username . "','" . $title . "','" . $description . "','" . $pid . "') ";
		//echo($Query . "<br>\n");
		$AffectedRows = $dblink->exec($Query);
		return ($AffectedRows == 1);
		
	}

	function isGroupFolder($id)
	{
		include('conn.php');
		$Query = ("select pid from " . TABLE_PREFIX . "folders where id='" . $id . "'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$pid = "{$row["pid"]}";
		}
		return ($pid == "-1");
		
	}
?>