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
include('header.php');
//TODO: redo with sorting javascript
?>
<?php
	include('access.php');
	$access = checkAccess();
	if($access)
	{
		include('gheader.php');
		include('conn.php');
		if (isset($_POST['group_id']))
		{
			 $group_id = $_POST['group_id'];
		}
		$btnUsername = "orderButtonNeutral";
		$btnDateJoin = "orderButtonNeutral";
		$btnPriv = "orderButtonNeutral";

		if (isset($_POST['orderby']))
		{
			 $strOrderBy = $_POST['orderby'];

			 if($strOrderBy == "name")
			 	$btnUsername = "orderButtonSelected";
			 else if($strOrderBy == "formatted_time")
			 	$btnDateJoin = "orderButtonSelected";
			 else
			 	$btnPriv = "orderButtonSelected";
		}
		else
		{
			$btnUsername = "orderButtonSelected";
			$strOrderBy = "name";
		}

		$user = new User();
		$username = $user->getUsername();

		// Check if manager joined
		include('includes/groups_functions.php');
		$partOf = checkIfManager($group_id, $username);
		$isMember = checkIfMember($group_id, $username);

		/* Retrieve the name of the group */
		$rec_name = returnGroupName($group_id);
		echo("<b>" . T_("Members of group") . " ". $rec_name . "</b><br><br>\n");
		if(!$partOf && !$isMember)
			echo("<p class=\"error\">" . T_("You must be the manager of this group") . ".</p>");
		else
		{
			if (isset($_POST['aname']))
			{
				//Change the user's priv
				 $aname = $_POST['aname'];
				 $newPriv = $_POST['newPriv'];
				 changeMemberPriv($aname, $newPriv);
			}
			//Retrieve the list of members
			$Query = ("select name, date_join AS formatted_time, priv from " . TABLE_PREFIX . "gsubscriptions where (group_id = '$group_id') order by ". $strOrderBy);
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);
			echo("<table cellpadding=\"2\">\n");
			$isData = false;
			while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				if(!$isData)
				{
					require_once('includes/convert_date.php');
					echo("<tr><td valign=\"bottom\"><form action=\"gmembers.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"hidden\" name=\"orderby\" value=\"name\"><input type=\"submit\" class=\"$btnUsername\" value=\"" . T_("Username") . "\"></form></td><td valign=\"bottom\"><form action=\"gmembers.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"hidden\" name=\"orderby\" value=\"formatted_time\"><input type=\"submit\" class=\"$btnDateJoin\" value=\"" . T_("Date Joined") . "\"></form></td><td valign=\"bottom\"><form action=\"gmembers.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"hidden\" name=\"orderby\" value=\"priv\"><input type=\"submit\" class=\"$btnPriv\" value=\"" . T_("Privilege") . "\"></form></td>");
					if($partOf) //If the manager
						echo("<td>" . T_("Delete") . "</td><td align=\"center\"><b style=\"text-decoration:underline; cursor:pointer;\" onmouseover=\"return overlib('" . T_("Select the checkbox to remove all his bookmarks") . "');\" onmouseout=\"return nd();\">?</b></td>");

					echo("</tr>\n");
					$isData = true;
				}
				$rec_name = "{$row["name"]}";
				$rec_date_join = "{$row["formatted_time"]}";
				$newDate = convert_date($rec_date_join);
				$rec_priv = "{$row["priv"]}";
				$priv = returnPrivName($rec_priv);

				// TODO: Review privileges, not using a db cause of translations
				$privn0 = returnPrivName('0');
				$privn1 = returnPrivName('1');
				$privn2 = returnPrivName('2');
				$priv0 = "0";
				$priv1 = "1";
				$priv2 = "2";

				switch($rec_priv)
				{
					case 0: $priv0 .= ("\" selected=\"selected"); break;
					case 1: $priv1 .= ("\" selected=\"selected"); break;
					case 2: $priv2 .= ("\" selected=\"selected"); break;
				}


				echo("<tr><td valign=\"top\">" . $rec_name . "</td><td valign=\"top\">" . $newDate . "</td>");
				if($partOf) //If the manager
					echo("<td valign=\"bottom\"><form action=\"gmembers.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"hidden\" name=\"aname\" value=\"$rec_name\"><select name=\"newPriv\" onchange=\"submit()\"><option value=\"$priv0\">$privn0</option><option value=\"$priv1\">$privn1</option><option value=\"$priv2\">$privn2</option></select></form></td><td valign=\"middle\"><form action=\"gunsubs.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"hidden\" name=\"username\" value=\"$rec_name\"><input type=\"hidden\" name=\"manager\" value=\"true\"><input type=\"submit\" name=\"submitted\" class=\"genericButton\" value=\"" . T_("Delete") . "\"></td><td valign=\"middle\"><input type=\"checkbox\" name=\"removeBooks\"></form></td>\n");
				else // If a member
					echo("<td valign=\"top\">" . $priv . "</td>\n");
				echo("</tr>\n");
			}
			if(!$isData)
				echo("<tr><td>" . T_("You have no users subscribed to your group yet") . ".</td></tr>\n");
			echo("</table>\n");

			if($partOf)
				echo("<br><br><form action=\"gmanage.php\" method=\"post\"><input type=\"submit\" class=\"genericButton\" value=\"<< " . T_("Management section") . "\"></form>");
			else
				echo("<br><br><form action=\"gdetails.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"submit\" class=\"genericButton\" value=\"<< " . T_("Group Details") . "\"></form>");
		}
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>