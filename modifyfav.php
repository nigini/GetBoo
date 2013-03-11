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
//TODO: I think we should merge it with the add.php file soon
//TODO: When modifed with success, should go back to folder
include('header.php'); ?>
<?php
include('access.php');
$access = checkAccess();
if($access)
{
	$user = new User();
	$username = $user->getUsername();
	include("includes/bookmarks.php");
	echo("<h2>" . T_("Modify bookmark") . "</h2>");

	if(isset($_POST["id"]))
		$id = $_POST["id"];
	else if(isset($_GET["id"]))
		$id = $_GET["id"];

	if($id != null && b_belongs_to($id, $username))
	{
		require_once('includes/protection.php');

		include('conn.php');
		$pid = $_POST["pid"];
		$url = $_POST["url"];
		$title = $_POST["title"];
		$description = $_POST["description"];

		$success = false;

		if ($_POST['submitted'])
		{
			if(TAGS)
			{
				if (!empty ($_POST['publicChk']))
				{
					$newPublic = true;
				}
				else
					$newPublic = false;

				$tags = $_POST["tags"];
			}

			if($id != null && $url != null)
			{
				//Make sure the url starts with http:// in the event the user's browser doesn't support javascript
				if (strpos($url, ':') === false)
				{
					$url = 'http://'. $url;
				}
				$result = b_url_exist($url,$username,$id);
				if(!$result['exists'])
				{
					//strip out html
					$title = filter($title);
					$description = filter($description);
					$url = filter($url);
					
					// Cut data to respect maximum length
					if(!empty($title))
						$title = substr($title, 0, 100);
					if(!empty($description))
						$description = substr($description, 0, 150);
					
					if(TAGS)
					{
						include('includes/tags_functions.php');
						$tags = trim($tags);

						//Remove any commas, dots, quotes, plus signs since the user might use commas to seperate tags rather than spaces
						$toRemove = array('"', "'", ",", "+");
						$tags = str_replace($toRemove, "", $tags);

						$tags = filter($tags);
						
						// cut tags if too long > 150 chars
						$tags = substr($tags, 0, 150);

						if($tags != null)
						{

							//Check if the book was public
							$public = checkIfPublic($id);

							//Was public and still is (P P)
							if($public && $newPublic)
							{
								//Make the changes to the tags, if any
								updateTags($id, $tags);
							}

							//Was not public, and now is (~ P)
							if(!$public && $newPublic)
							{
								//Add the tags
								addTags($tags);
								//Store the tags with the bookmark
								storeTags($id, $tags);
							}

							//Was public, and now is not (P ~)
							if($public && !$newPublic)
							{
								//Remove (unstore) all the tags attached to this bookmark in table tags_books
								unstoreTags($id);
							}
						}
					}

					// update the favourites table
					$Query = sprintf("UPDATE " . TABLE_PREFIX . "favourites SET title=%s, url=%s, description=%s, LAST_MODIFIED = NOW() WHERE ID =" . $id, quote_smart($title), quote_smart($url), quote_smart($description));
					$AffectedRows = $dblink->exec($Query);
					if($AffectedRows == 1)
					{
						echo("<p class=\"success\">" . T_("The bookmark has been updated") . ".</p>");
						$success = true;
					}
					else if($AffectedRows == 0)
					{
						echo("<p class=\"error\">" . T_("Modify this bookmark again since no change has been detected") . ".</p>");
					}
					else
						echo("<p class=\"error\">" . T_("There has been a problem when updating the bookmark") . ".</p>");
					}
				else
				{
					echo("<p class=\"error\">" . T_("A bookmark with the same url has already been submitted") . ".</p><div style=\"margin-left: 2em; padding-bottom: 2em;\"><form action=\"modifyfav.php\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"" . $result['bId'] . "\" /><input type=\"hidden\" name=\"pid\" value=\"" . $result['folderId'] . "\" /><input type=\"submit\" class=\"genericButton\" value=\"" . T_("Edit Original Bookmark") . "\" /></form></div>");
				}
			}
			else
			{
				echo("<p class=\"error\">" . T_("The form is incomplete") . "</p>");
			}
		}

		if($id != null)
		{
			include('conn.php');
			$Query = "select title, url, description from " . TABLE_PREFIX . "favourites where id='" . $id . "'";
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);

			$found = false;
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$title = ("{$row["title"]}");
				$url = ("{$row["url"]}");
				$description = ("{$row["description"]}");
				$found = true;
			}

			//strip out html
			$title = filter($title);
			$description = filter($description);
			$url = filter($url);

			if(TAGS)
			{
				require_once('includes/tags_functions.php');
				$public = checkIfPublic($id);

				if($public)
				{
					$checkedStr = "checked=\"checked\"";
					//Return all tags for this bookmark
					$strTags = returnAllTags($id);
				}
				else
					$readOnlyTags = "readonly";
			}

			if($found && $url!=null)
			{
				$strBack = ($success) ? "<< " . T_("Back to Folder") . "" : "" . T_("Cancel") . "";
				?>
	<form action="modifyfav.php" method="post">
	<input type="hidden" name="id" value="<?php echo($id); ?>">
	<input type="hidden" name="pid" value="<?php echo($pid); ?>">
	<table>
		<tr>
				<td><span class="formsLabel"><?php echo T_("Url");?></span></td>
				<td colspan="3"><input type="text" name="url" id="url" size="75" value="<?php echo("$url"); ?>" class="formtext" onfocus="this.select()" <?php if(CURL_AVAILABLE) {?>onblur="checkurl(this)"<?php }?> />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('<?php echo T_("No chars limit");?>');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
				<td><span class="formsLabel"><?php echo T_("Title");?></span></td>
				<td colspan="3"><input type="text" name="title" id="title" size="75" maxlength="100" value="<?php echo("$title"); ?>" class="formtext" onfocus="this.select()" onkeypress="this.style.backgroundImage = 'none';" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('100 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
				<td><span class="formsLabel"><?php echo T_("Description");?></span></td>
				<td colspan="3"><input type="text" name="description" size="75" maxlength="150" value="<?php echo("$description"); ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
		</tr>
	<?php
				if(TAGS)
				{
	?>
		<tr>
				<td><span class="formsLabel"><?php echo T_("Public");?></span></td>
				<td colspan="3"><input type="checkbox" <?php echo($checkedStr);?> name="publicChk" id="tagCheck" onClick="activateTags(this);" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('For new users, they will appear in recent bookmarks <?php echo PUBLIC_TIMEOUT;?> days after the date you registered');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
				<td><span class="formsLabel"><?php echo T_("Tags");?></span></td>
				<td colspan="3"><input type="text" name="tags" id="tags" size="75"  maxlength="150" value="<?php echo("$strTags"); ?>" class="formtext" <?php echo($readOnlyTags);?> />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo T_("chars max");?>, 30 <?php echo T_("chars per tag max. Tags are keywords");?>.');" onmouseout="return nd();">?</b> <?php echo T_("space separated");?></td>
		</tr>
	<?php
				}
	?>
		<tr>
				<td></td>
				<td style="width: 100px;"><input type="submit" name="submitted" value="<?php echo T_("Modify Bookmark");?>" class="genericButton" /></form></td><td style="width: 250px;"><?php echo("<form action=\"books.php\" method=\"post\"><input type=\"hidden\" name=\"delBookId\" value=\"$id\" /><input type=\"hidden\" name=\"folderid\" value=\"$pid\" /><input type=\"submit\" class=\"genericButton\" value=\"" . T_("Delete Bookmark") . "\" /></form>");?></td><td><form action="books.php" method="post"><input type="hidden" name="folderid" value="<?php echo($pid);?>"><input type="submit" class="genericButton" value="<?php echo($strBack);?>"></form></td>
		</tr>
	</table>

	<?php
				if(TAGS)
				{
					include('includes/dynamicTags.php');
				}

				//Stats
				require_once('includes/convert_date.php');

				$Query = "select ADD_DATE as add_date, LAST_VISIT as last_visit, LAST_MODIFIED as last_modified from " . TABLE_PREFIX . "favourites where id='" . $id . "'";
				//echo($Query . "<br>\n");
				$dbResult = $dblink->query($Query);

				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$rec_add_date = ("{$row["add_date"]}");
					$rec_last_visit = ("{$row["last_visit"]}");
					$rec_last_modified = ("{$row["last_modified"]}");
					$add_date = convert_date($rec_add_date);
					$last_visit = convert_date($rec_last_visit);
					$last_modified = convert_date($rec_last_modified);
				}

				$QueryMain = ("select count(*) as Count from " . TABLE_PREFIX . "bookmarkhits b, " . TABLE_PREFIX . "favourites f where b.bookmarkid = f.id and f.id = '" . $id . "' and ");

				$Query = ($QueryMain . "b.name = '" . $username . "'");
				$dbResult = $dblink->query($Query);

				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$count_user = ("{$row["count"]}");
				}
				$Query = ($QueryMain . "b.name != '" . $username . "' and b.name != 'system:guest'");
				$dbResult = $dblink->query($Query);

				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$count_users = ("{$row["count"]}");
				}
				$Query = ($QueryMain . "b.name = 'system:guest'");
				$dbResult = $dblink->query($Query);

				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$count_guest = ("{$row["count"]}");
				}

				$total_clicks = $count_user + $count_users + $count_guest;

				echo("<div>");
				echo("<h3 class=\"statsModifiyBook\">Stats</h3>");
				echo("<p><b>" . T_("Information") . "</b> " . T_("Add Date") . ": $add_date | " . T_("Last Visit") . ": $last_visit | " . T_("Last Modified") . ": $last_modified</p>");
				if($total_clicks > 0)
					echo("<p><b>" . T_("Clicks") . "</b> " . T_("You") . ": $count_user | " . T_("Users") . ": $count_users | " . T_("Guests") . ": $count_guest | " . T_("Total") . ": $total_clicks</p>");
				else
					echo("<p><b>" . T_("Clicks") . "</b> " . T_("None") . "</p>");
			}
			
		}
		else
		{
			echo("<p class=\"error\">" . T_("Bookmark ID is missing") . "</p>");
		}
	}
	else
	{
		echo("<p class=\"error\">" . T_("Wrong Bookmark ID") . "</p>");
	}
}
?>
<?php include('footer.php'); ?>