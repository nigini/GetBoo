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
		$user = new User();
		$username = $user->getUsername();
		$actpass = $_POST['pass'];
		include('includes/protection.php');
		remhtml($actpass);

		echo("<h2>" . T_("Delete Bookmarks") . "</h2>\n");
		$success = false;
		if ($_POST['submitted'])
		{
			if($actpass != null)
			{
				include('conn.php');

				$passencrypt = $user->encryptPassword($actpass);
				$Query = "select name from " . TABLE_PREFIX . "session where name='" . $username . "' and pass='" . $passencrypt . "'";
				$dbResult = $dblink->query($Query);
				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					//Delete all books/folders from user's account
					$Query = "delete from " . TABLE_PREFIX . "folders where name='" . $username . "' AND pid != '-1'"; //Avoid deleting group folders
					//echo($Query . "<br>\n");
					$AffectedRows = $dblink->exec($Query);
					if($AffectedRows >= 0)
					{
						if(TAGS)
						{
							include('includes/tags_functions.php');

							//Get all the bookmark ids of this user before deleting, in order to remove the entries in table tags_books
							$Query = "select id from " . TABLE_PREFIX . "favourites where name='" . $username . "'";
							$dbResult = $dblink->query($Query);
							$rows = $dbResult->fetchAll();
							$strTags = "";
							foreach($rows as $row)
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
						$Query = "delete from " . TABLE_PREFIX . "favourites where name='" . $username . "'";
						//echo($Query . "<br>\n");
						$AffectedRows = $dblink->exec($Query);
						if($AffectedRows >= 0)
						{
							echo("<p class=\"success\">" . T_("You have successfully deleted all your folders and bookmarks") . ".</p>\n<p>" . sprintf(T_("You can <a href=\"%s\">go back</a> to your bookmarks"),"books.php") . ".<br>\n</div>");
							$success = true;
						}
						else
							echo("<p class=\"error\">" . T_("There has been a problem with deleting bookmarks") . ".</p>");
					}
					else
						echo("<p class=\"error\">" . T_("There has been a problem with deleting folders") . ".</p>");
				}
				else
					echo("<p class=\"error\">" . T_("The password is incorrect") . "</p>");
			}
			else
			{
				echo("<p class=\"error\">" . T_("Please enter your password") . ".</p>");
			}
		}

		if(!$success)
		{
			if($username == "demo") // Demo account
			{
				echo("<p class=\"error\">" . T_("This is the demo account") . ".</p>");
			}
			else
			{
?>
<form action="deleteallbooks.php" method="post">
<input type="hidden" name="name" value="<?php echo($username) ?>">
<p><?php echo T_("Are you sure you want to delete all your bookmarks and folders in your account");?>?<br>
<?php echo T_("All the bookmarks in your groups will also be deleted");?>.</p>
<p>
<span class="formsLabel"><?php echo T_("Enter Password");?></span>
<input type="password" name="pass" size="20" maxlength="50" class="formtext" onfocus="this.select()">
<input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Yes, I want to delete them all");?>">
</form>
</p>
<?php
			}
		}
	}
?>
<?php include('footer.php'); ?>