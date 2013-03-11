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

include('includes/folders.php');
$folderid = $_POST['folderid'];
if($folderid == '')
	$folderid = MAIN_FID;

// Form actions
$copyBookId = $_POST['copyBookId'];
if($copyBookId != null) //Copy a bookmark
{
	$successCopy = copy_bookmark($copyBookId, $folderid);
	if($successCopy)
	{
		echo("<p class=\"success\">" . T_("You have successfully copied this bookmark") . ".</p>");
		$_SESSION["movetype"] = "";
		$_SESSION["moveid"] = "";
	}
	else
		echo("<p class=\"error\">" . T_("There has been a problem when pasting the bookmark") . ".</p>");
}

$moveBookId = $_POST['moveBookId'];
if($moveBookId != null) //Move a bookmark
{
	$Query = "update " . TABLE_PREFIX . "favourites set folderid = '$folderid' where id='" . $moveBookId . "'";
	//echo($Query . "<br>\n");
	$AffectedRows = $dblink->exec($Query);
	if($AffectedRows == 0 || $AffectedRows == 1)
	{
		echo("<p class=\"success\">" . T_("You have successfully moved this bookmark") . ".</p>");
		$_SESSION["movetype"] = "";
		$_SESSION["moveid"] = "";
	}
	else
		echo("<p class=\"error\">" . T_("There has been a problem when moving the bookmark") . ".</p>");
}

$copyFolderId = $_POST['copyFolderId'];
if($copyFolderId != null) //Copy a folder
{
	$successCopy = copy_folder($copyFolderId, $folderid);
	if($successCopy)
	{
		echo("<p class=\"success\">" . T_("You have successfully copied this folder") . "</p>");
		$_SESSION["movetype"] = "";
		$_SESSION["moveid"] = "";
	}
	else
		echo("<p class=\"error\">" . T_("There has been a problem when pasting the folder") . ".</p>");
}

$moveFolderId = $_POST['moveFolderId'];
if($moveFolderId != null) //Move a folder
{
	$Query = "update " . TABLE_PREFIX . "folders set pid = '$folderid' where id='" . $moveFolderId . "'";
	//echo($Query . "<br>\n");
	$AffectedRows = $dblink->exec($Query);
	if($AffectedRows == 0 || $AffectedRows == 1)
	{
		echo("<p class=\"success\">" . T_("You have successfully moved this folder") . "</p>");
		$_SESSION["movetype"] = "";
		$_SESSION["moveid"] = "";
	}
	else
		echo("<p class=\"error\">" . T_("There has been a problem when moving the folder") . ".</p>");
}

$delFolderId = $_POST['delFolderId'];
if($delFolderId != null)
{
	$resultArr = delete_folder($delFolderId, $username);
	if($resultArr['success'])
	{
		echo("<p class=\"success\">" . $resultArr['message'] . ".</p>");
	}
	else
		echo("<p class=\"error\">" . $resultArr['message'] . ".</p>");
}
// End of form actions

$Query = ("select title from " . TABLE_PREFIX . "folders where id='" . $folderid . "'");
$dbResult = $dblink->query($Query);
$title = "";
if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
{
	$title = "{$row["title"]}";
}

if($folderid!=MAIN_FID)
{
	$Query = ("select pid from " . TABLE_PREFIX . "folders where (name='$username' and id='$folderid')");
	//echo($Query . "<br>\n");
	$dbResult = $dblink->query($Query);
	$parentid = 0;
	if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	{
		$parentid = "{$row["pid"]}";
	}
}

$path = get_group_path($folderid, $username);

//Check if in a group folder
if($parentid==GROUPS_FID)
{
	//Get the group_ip
	$group_id = returnGroupID($title);

	//Check the privilege of the member
	//Check if the manager
	$partOf = checkIfManager($group_id, $username);
	if(!$partOf) //Not the manager
	{
		$privOfMember = returnMemberPriv($username);
		$privName = returnPrivName($privOfMember);
	}
	else
		$privOfMember = 3;
}
else
	$privOfMember = 1;

if($privOfMember != 0 && $folderid!=GROUPS_FID)
{
	$moveids = $_POST['moveids'];
	$movetypes = $_POST['movetypes'];
	if($movetypes != null && $moveids !=null)
	{
		$_SESSION["movetype"] = $movetypes;
		$_SESSION["moveid"] = $moveids;
	}

	$movetype = $_SESSION["movetype"];
	$moveid = $_SESSION["moveid"];
	$movestring = "";

	if($movetype != null && $moveid !=null)
	{
		$found = false;
		if($movetype == 0)
		{
			$_SESSION["movetype"] = "";
			$_SESSION["moveid"] = "";
			$movestring = "";
		}
		else if($movetype == 1)
		{
			$Query = ("select title from " . TABLE_PREFIX . "favourites where id='$moveid'");
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);
			$ftitle = 0;
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$ftitle = "{$row["title"]}";
				$found = true;
			}

			$movestring = ("<td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\"><input type=\"hidden\" name=\"copyBookId\" value=\"$moveid\"><input type=\"image\" src=\"images/books/mvpaste.GIF\" class=\"books_img\" value=\"" . T_("Copy Here") . "\" alt=\"" . T_("Copy Here") . "\" title=\"" . T_("Copy Here") . "\"></form></td><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\"><input type=\"hidden\" name=\"moveBookId\" value=\"$moveid\"><input type=\"image\" src=\"images/books/mvhere.GIF\" class=\"books_img\" value=\"" . T_("Move Here") . "\" alt=\"" . T_("Move Here") . "\" title=\"" . T_("Move Here") . "\"></form></td><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\"><input type=\"hidden\" name=\"movetypes\" value=\"0\"><input type=\"hidden\" name=\"moveids\" value=\"0\"><input type=\"image\" src=\"images/books/mvcancel.GIF\" class=\"books_img\" value=\"" . T_("Cancel") . "\" alt=\"" . T_("Cancel") . "\" title=\"" . T_("Cancel") . "\"></form></td><td valign=\"middle\"><img src=\"images/style/$style/bookmark.GIF\" alt=\"Bookmark\" class=\"books_img\"></td><td colspan=\"2\">$ftitle</td>");
		}
		else
		{
			//Check if in a group folder, since they cannot move folders there or if they are moving the folder into itself
			if(!$parentid==GROUPS_FID && $folderid!=$moveid)
			{
				$Query = ("select title from " . TABLE_PREFIX . "folders where id='$moveid'");
				//echo($Query . "<br>\n");
				$dbResult = $dblink->query($Query);
				$ftitle = 0;
				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$ftitle = "{$row["title"]}";
					$found = true;
				}
				$movestring = ("<td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\"><input type=\"hidden\" name=\"copyFolderId\" value=\"$moveid\"><input type=\"image\" src=\"images/books/mvpaste.GIF\" class=\"books_img\" value=\"" . T_("Copy Here") . "\" alt=\"" . T_("Copy Here") . "\" title=\"" . T_("Copy Here") . "\"></form></td><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\"><input type=\"hidden\" name=\"moveFolderId\" value=\"$moveid\"><input type=\"image\" src=\"images/books/mvhere.GIF\" class=\"books_img\" value=\"" . T_("Move Here") . "\" alt=\"" . T_("Move Here") . "\" title=\"" . T_("Move Here") . "\"></form></td><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\"><input type=\"hidden\" name=\"movetypes\" value=\"0\"><input type=\"hidden\" name=\"moveids\" value=\"0\"><input type=\"image\" src=\"images/books/mvcancel.GIF\" class=\"books_img\" value=\"" . T_("Cancel") . "\" alt=\"" . T_("Cancel") . "\" title=\"" . T_("Cancel") . "\"></form></td><td valign=\"middle\"><img src=\"images/style/$style/folder.GIF\" alt=\"Folder\" class=\"books_fimg\"></td><td colspan=\"2\">$ftitle</td>");
			}
			else
				$found = true;
		}
		//Check if the bookmark / folder still exists
		if($movetype > 0 && !$found)
		{
			$_SESSION["movetype"] = "";
			$_SESSION["moveid"] = "";
			$movestring = "";
		}
	}
}

echo("<table class=\"bookmarks\">");
if($path != null)
	echo("<tr><td class=\"bookmarkstitle\" colspan=\"10\"><span style=\"font-weight: bold\">$path</span></td></tr></table><table class=\"bookmarks\">\n");


if($movestring != null)
	echo("<tr>$movestring</tr>\n");


//Display the folder for the groups, only if in Main
if($folderid==MAIN_FID)
{
	echo("<tr><td></td><td></td><td></td>");
	echo("<td valign=\"middle\"><img src=\"images/style/$style/folder.GIF\" alt=\"" . T_("Folder") . "\" class=\"books_fimg\" /></td><td><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"" . GROUPS_FID . "\" /><input type=\"submit\" class=\"submitLinkBookmarks\" value=\"" . T_("Groups") . "\" /></form></td><td>" . T_("Group folders") . "</td></tr>\n");
}

$anyBooksOrFolder = false;

//Show all the folders
$Query = ("select id, title, description, pid from " . TABLE_PREFIX . "folders where (name='$username' and pid='$folderid') order by title");
//echo($Query . "<br>\n");
$dbResult = $dblink->query($Query);
while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
{
	$anyBooksOrFolder = true;
	$bdescription = "{$row["description"]}";

	//Check if not under folder Groups (-1) to avoid editing
	if($folderid!=GROUPS_FID)
		echo("<tr class=\"bookmarksedit\"><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"delFolderId\" value=\"{$row["id"]}\" /><input type=\"hidden\" name=\"folderid\" value=\"$folderid\" /><input type=\"image\" src=\"images/books/trash.GIF\" class=\"books_img\" value=\"" . T_("Delete") . "\" alt=\"" . T_("Delete") . "\" title=\"" . T_("Delete") . "\" /></form></td><td valign=\"top\"><form action=\"modifyfolder.php\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"{$row["id"]}\" /><input type=\"hidden\" name=\"pid\" value=\"{$row["pid"]}\" /><input type=\"image\" src=\"images/books/modify.GIF\" class=\"books_img\" value=\"" . T_("Modify") . "\" alt=\"" . T_("Modify") . "\" title=\"" . T_("Modify") . "\" /></form></td><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\" /><input type=\"hidden\" name=\"movetypes\" value=\"2\" /><input type=\"hidden\" name=\"moveids\" value=\"{$row["id"]}\" /><input type=\"image\" src=\"images/books/move.GIF\" class=\"books_img\" value=\"" . T_("Move") . "\" alt=\"" . T_("Move") . "\" title=\"" . T_("Move") . "\" /></form></td><td valign=\"middle\"><img src=\"images/style/$style/folder.GIF\" alt=\"Folder\" class=\"books_fimg\" /></td><td><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"{$row["id"]}\" /><input type=\"submit\" class=\"submitLinkBookmarks\" value=\"{$row["title"]}\" /></form></td><td>" . $bdescription . "</td></tr>\n");
	else
		echo("<tr><td valign=\"middle\"><img src=\"images/style/$style/folder.GIF\" alt=\"" . T_("Folder") . "\" class=\"books_fimg\" /></td><td><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"{$row["id"]}\" /><input type=\"submit\" class=\"submitLinkBookmarks\" value=\"{$row["title"]}\" /></form></td><td>" . $bdescription . "</td></tr>\n");
}

//Check if in a group folder
$endOfTable = true;
if($parentid==GROUPS_FID)
{
	if($privOfMember == 0)
	{
		$endOfTable = false;
		echo("</table><p class=\"error\">" . T_("Your account has been disabled for this group") . ".</p>\n");
	}
	else
	{
		//Display all the bookmarks from all the users in this group
		$Query = ("select f.id, f.name, f.title, f.url, description, f.folderid from " . TABLE_PREFIX . "favourites f, " . TABLE_PREFIX . "gfolders g where (g.group_id='" . $group_id . "' and g.folderid = f.folderid) order by title");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$anyBooksOrFolder = true;
			$idurl = "{$row["id"]}";
			$qfolderid = "{$row["folderid"]}";
			$owner = "{$row["name"]}";
			$btitle = "{$row["title"]}";
			$bdescription = "{$row["description"]}";

			//Only allow the member to edit its own bookmarks, unless the member is a moderator or is the admin
			if(($qfolderid == $folderid) || ($privOfMember != 1))
				echo("<tr class=\"bookmarksedit\"><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"delBookId\" value=\"{$row["id"]}\" /><input type=\"hidden\" name=\"folderid\" value=\"$folderid\" /><input type=\"image\" src=\"images/books/trash.GIF\" class=\"books_img\" value=\"" . T_("Delete") . "\" alt=\"" . T_("Delete") . "\" title=\"" . T_("Delete") . "\" /></form></td><td valign=\"top\"><form action=\"modifyfav.php\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"{$row["id"]}\" /><input type=\"hidden\" name=\"pid\" value=\"" . $folderid . "\" /><input type=\"image\" src=\"images/books/modify.GIF\" class=\"books_img\" value=\"" . T_("Modify") . "\" alt=\"" . T_("Modify") . "\" title=\"" . T_("Modify") . "\" /></form></td><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\" /><input type=\"hidden\" name=\"movetypes\" value=\"1\" /><input type=\"hidden\" name=\"moveids\" value=\"{$row["id"]}\" /><input type=\"image\" src=\"images/books/move.GIF\" class=\"books_img\" value=\"" . T_("Move") . "\" alt=\"" . T_("Move") . "\" title=\"" . T_("Move") . "\" /></form></td><td valign=\"middle\"><img src=\"images/style/$style/bookmark.GIF\" alt=\"Bookmark\" class=\"books_img\" /></td><td><a href=\"redirect.php?id=$idurl\" target=\"blank\">" . $btitle . "</a></td><td>" . $bdescription . "</td><td>" . T_("By") . " " . $owner . "</td></tr>\n");
			else
				echo("<tr class=\"bookmarksedit\"><td></td><td></td><td></td><td valign=\"middle\"><img src=\"images/style/$style/bookmark.GIF\" alt=\"Bookmark\" class=\"books_img\" /></td><td><a href=\"redirect.php?id=$idurl\" target=\"blank\">" . $btitle . "</a></td><td>" . $bdescription . "</td><td>By " . $owner . "</td></tr>\n");
		}
	}
}
else
{
	$Query = ("select id, title, url, description, folderid from " . TABLE_PREFIX . "favourites where (name = '" . $username . "' and folderid='" . $folderid . "') order by title");
	//echo($Query . "<br>\n");
	$dbResult = $dblink->query($Query);

	while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	{
		$anyBooksOrFolder = true;
		$idurl = "{$row["id"]}";
		$btitle = "{$row["title"]}";
		$bdescription = "{$row["description"]}";

			echo("<tr class=\"bookmarksedit\"><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"delBookId\" value=\"{$row["id"]}\" /><input type=\"hidden\" name=\"folderid\" value=\"$folderid\" /><input type=\"image\" src=\"images/books/trash.GIF\" class=\"books_img\" value=\"" . T_("Delete") . "\" alt=\"" . T_("Delete") . "\" title=\"" . T_("Delete") . "\" /></form></td><td valign=\"top\"><form action=\"modifyfav.php\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"{$row["id"]}\" /><input type=\"hidden\" name=\"pid\" value=\"{$row["folderid"]}\" /><input type=\"image\" src=\"images/books/modify.GIF\" class=\"books_img\" value=\"" . T_("Modify") . "\" alt=\"" . T_("Modify") . "\" title=\"" . T_("Modify") . "\" /></form></td><td valign=\"top\"><form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"folderid\" value=\"$folderid\" /><input type=\"hidden\" name=\"movetypes\" value=\"1\" /><input type=\"hidden\" name=\"moveids\" value=\"{$row["id"]}\" /><input type=\"image\" src=\"images/books/move.GIF\" class=\"books_img\" value=\"" . T_("Move") . "\" alt=\"" . T_("Move") . "\" title=\"" . T_("Move") . "\" /></form></td><td valign=\"middle\"><img src=\"images/style/$style/bookmark.GIF\" alt=\"Bookmark\" class=\"books_img\" /></td><td><a href=\"redirect.php?id=$idurl\" target=\"blank\">" . $btitle . "</a></td><td>" . $bdescription . "</td></tr>\n");
	}
}

if($endOfTable)
	echo("</table><br>\n");

if(!$anyBooksOrFolder && $folderid==MAIN_FID)
	echo("<b>" . sprintf(T_("Welcome to %s!"), WEBSITE_NAME) . "</b><br>" . T_("It appears that you don't have any bookmarks yet") . ".<br>" . sprintf(T_("In order to add bookmarks and folders, go to <a href=\"%s\">Add</a> from the top menu or <a href=\"%s\">Import</a> your bookmarks from your browser"), "add.php", "import.php") . ".");
else
{
	?>
	<div class="searchbooks" style="text-align: center">
	<?php
		echo("<form action=\"search.php\" method=\"post\">\n");
		?>
		<br><b><?php echo T_("Search Bookmarks");?></b><br>
		<input name="keywords" value="" size="30" class="formtext" onfocus="this.select()">
		<input type="submit" name="submit" value="<?php echo T_("Search");?>" class="genericButton">
		</form>
	</div>
	<?php
	echo("<a href=\"deleteallbooks.php\">" . T_("Delete All Your Bookmarks") . "</a>");
}

echo("</div>");

