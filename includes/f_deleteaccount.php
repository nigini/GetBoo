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

function deleteUserAccount($uname, $massDelete = false)
{
	global $user;
	if($uname != null)
	{
		include('conn.php');
		$Query = "delete from " . TABLE_PREFIX . "session where name='$uname'";
		//echo($Query . "<br>\n");
		$AffectedRows = $dblink->exec($Query);
		if($AffectedRows == 1)
		{
			//Delete all books/folders from user's account
			$Query = "delete from " . TABLE_PREFIX . "folders where name='" . $uname . "' AND pid != '-1'"; //Avoid deleting group folders
			//echo($Query . "<br>\n");
			$AffectedRows = $dblink->exec($Query);
			if($AffectedRows >= 0)
			{
				if(TAGS && !$massDelete)
				{
					include('tags_functions.php');

					//Get all the bookmark ids of this user before deleting, in order to remove the entries in table tags_books
					$Query = "select id from " . TABLE_PREFIX . "favourites where name='" . $uname . "'";
					$dbResult = $dblink->query($Query);
					$strTags = "";
					while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
					{
						$current_id = "{$row["id"]}";
						$public = checkIfPublic($current_id);
						if($public)
						{
							//Remove (unstore) all the tags attached to this bookmark in table tags_books
							unstoreTags($current_id);
						}
					}
				}
				$Query = "delete from " . TABLE_PREFIX . "favourites where name='" . $uname . "'";
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows >= 0)
				{
					$Query = "delete from " . TABLE_PREFIX . "activation where name='$uname'";
					//echo($Query . "<br>\n");
					$AffectedRows = $dblink->exec($Query);
					if($AffectedRows == 1)
					{
						$success = true;
						if($massDelete)
						{
							echo("<br>\n" . $uname . " ... deleted!");
						}
						else if($user->isAdmin())
						{
							echo("<p class=\"success\">" . sprintf(T_("%s was successfully deleted") . "</p>",$uname));
							echo("<p><a href=\"manageusers.php\"><< " . T_("Go Back") . "</a></p>");
						}
						else
						{
							echo("<p class=\"success\">" . T_("You have been successfully deleted") . ".</p><p>" . sprintf(T_("Thank you for using %s"),WEBSITE_NAME) . "!</p>");
							$_SESSION = array();
							session_destroy();
						}
					}
					else
						echo("<p class=\"error\">" . T_("There has been a problem when deleting your activation code") . ".</p>");
				}
				else
					echo("<p class=\"error\">" . T_("There has been a problem when deleting bookmarks") . ".</p>");
			}
			else
				echo("<p class=\"error\">" . T_("There has been a problem when deleting folders") . ".</p>");
		}
		else
			echo("<p class=\"error\">" . T_("There has been a problem") . ".</p>");
	}
	else
	{
		echo("<p class=\"error\">" . T_("The form is incomplete") . ".</p>");
	}
}
?>