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
include('header.php'); ?>
<?php
	include('access.php');
	$access = checkAccess();
	if($access)
	{
		include('gheader.php');
?>
		<b><?php echo T_("Groups I manage");?></b><br><br>
<?php
		include('conn.php');

		$Query = ("select group_id, group_name, description, password from " . TABLE_PREFIX . "groups where (manager = '". $username . "')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		$countResults = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$countResults++;
			$rec_name = "{$row["group_name"]}";
			$rec_id = "{$row["group_id"]}";
			$rec_description = "{$row["description"]}";
			$rec_password = "{$row["password"]}";
			echo("- <b>$rec_name</b> : $rec_description<br><i>");
			if($rec_password != "")
				echo(T_("Private group"));
			else
				echo(T_("Public group"));
			echo("</i><table><tr><td valign=\"top\"><form action=\"gdetails.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Details") . "\"></form></td>");
			echo("<td valign=\"top\"><form action=\"gedit.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Edit") . "\"></form></td>");
			echo("<td valign=\"top\"><form action=\"gmembers.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Members") . "\"></form></td>");
			echo("<td valign=\"top\"><form action=\"gdelete.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Delete") . "\"></form></td></tr></table>");
		}
		if($countResults == 0)
		{
			echo("" . T_("You do not manage any groups") . ".<br><br>\n");
?>
<a href="gcreate.php"><?php echo T_("Create</a> a <b>new group");?></b><br><br>
<?php
		}
?>
		<br><b><?php echo T_("Groups I am a member");?></b><br><br>
<?php
		$Query = ("select g.group_id, group_name, description, password from " . TABLE_PREFIX . "groups g, " . TABLE_PREFIX . "gsubscriptions s where (g.group_id = s.group_id and s.name = '". $username . "')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		$countResults = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$countResults++;
			$rec_name = "{$row["group_name"]}";
			$rec_id = "{$row["group_id"]}";
			$rec_description = "{$row["description"]}";
			$rec_password = "{$row["password"]}";
			echo("- <b>$rec_name</b> : $rec_description<br><i>");
			if($rec_password != "")
				echo(T_("Private group"));
			else
				echo(T_("Public group"));
			echo("</i><table><tr><td valign=\"top\"><form action=\"gdetails.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Details") . "\"></form></td>");
			echo("<td valign=\"top\"><form action=\"gunsubs.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Unsubscribe") . "\"></form></td></tr></table>");
		}
		if($countResults == 0)
		{
			echo("" . T_("You are not part of any groups") . ".<br><br>\n");
?>
<a href="groups.php"><?php echo T_("Search</a> a <b>group");?></b><br><br>
<?php
		}
		
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>