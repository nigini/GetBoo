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
	require_once('config.inc.php');
	$customTitle = T_("My Groups");
	require_once('header.php'); ?>
<?php
	include('access.php');
	$access = checkAccess();
	$keywords = false;
	if($access)
	{
		include('gheader.php');
?>
		<form action="groups.php" method="post">
		<input name="keywords" value="<?php echo htmlentities(stripslashes($keywords)) ?>" class="formtext" onfocus="this.select()" />
		<input type="submit" name="Submit" value="<?php echo T_("Search");?>" class="genericButton" />
		</form>
		<br>
<?php
		if (isset($_POST['keywords']))
		{
			 $keywords = $_POST['keywords'];
		}
		$keywords = trim($keywords);
		$keywords = preg_replace("/ +/", " ", $keywords);
		include('includes/protection.php');
		remhtml($keywords);

		include('conn.php');

		include('includes/groups_functions.php');

		$words = split(" ", $keywords, 8);
		$results = array ();
		$resultsCount = 0;

		foreach ($words as $search_string)
		{
			// TODO: Do some validation
			if (preg_match("/^-/", $search_string))
			{
			  echo "<b>" . T_("Not") . "</b> $search_string<br>\n";
			}

			$Query = ("select group_id, group_name, description, password from " . TABLE_PREFIX . "groups where (group_name LIKE '%$search_string%') or (description LIKE '%$search_string%')");
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);

			while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$rec_name = "{$row["group_name"]}";
				$rec_id = "{$row["group_id"]}";
				$rec_description = "{$row["description"]}";
				$rec_password = "{$row["password"]}";
				//Check if the group is already displayed to avoid repeating it more than once
				if(!in_array($rec_id, $results))
				{
					$results[$resultsCount++] = $rec_id;
					echo("- <b>$rec_name</b> : $rec_description<br><i>");
					if($rec_password != "")
						echo(T_("Private group"));
					else
						echo(T_("Public group"));

					echo("</i><table><tr><td valign=\"top\"><form action=\"gdetails.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Details") . "\"></form></td>");

					// Show the join button if the user is not subscribed to this group yet,
					// else display the button to view or unsubscribe from this group
					// Check if the user is already part of the group

					$partOf = (checkIfManager($rec_id, $username) || checkIfMember($rec_id, $username));
					$onlyMember = checkIfMember($rec_id, $username);

					if(!$partOf)
					{
						echo("<td valign=\"top\"><form action=\"gjoin.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Join") . "\"></form></td></tr></table>");
					}
					elseif($onlyMember)
					{
						echo("<td valign=\"top\"><form action=\"gunsubs.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"submitLinkGroups\" value=\"" . T_("Unsubscribe") . "\"></form></td></tr></table>");
					}
					else
						echo("</tr></table>");
				}
			}
		}
		
		echo("</div>");
	}
?>
<?php require_once('footer.php'); ?>