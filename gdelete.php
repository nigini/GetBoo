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
		$user = new User();
		$username = $user->getUsername();

		if (isset($_POST['group_id']))
		{
			 $group_id = $_POST['group_id'];
		}
		if (isset($_POST['pass']))
		{
			 $actpass = $_POST['pass'];
		}

		$success = false;

		if ($_POST['submitted'] || $user->isAdmin())
		{
			include('conn.php');
			if($group_id != null && ($actpass != null || $user->isAdmin()))
			{
				include('includes/groups_functions.php');
				$partOf = checkIfManager($group_id, $username);
				if(!$partOf && !$user->isAdmin())
					echo("<p class=\"error\">" . T_("Only the manager of this group can delete his group") . ".</p>");
				else
				{
					$passencrypt = $user->encryptPassword($actpass);
					$Query = "select name from " . TABLE_PREFIX . "session where name='" . $username . "' and pass='" . $passencrypt . "'";
					$dbResult = $dblink->query($Query);
					if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC) || $user->isAdmin())
					{
						$Query = ("delete from " . TABLE_PREFIX . "gsubscriptions where group_id = " . $group_id);
						//echo($Query . "<br>\n");
						$AffectedRows = $dblink->exec($Query);

						$Query = ("delete from " . TABLE_PREFIX . "groups where group_id = " . $group_id);
						//echo($Query . "<br>\n");
						$AffectedRows = $dblink->exec($Query);
						if($AffectedRows == 1)
						{
							//Delete all the group folders and their content
							//Get all the folders ids of this group from the gfolders table

							$errorDeleting = false;
							$Query = "select folderid from " . TABLE_PREFIX . "gfolders where group_id='" . $group_id . "'";
							$dbResult = $dblink->query($Query);
							while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
							{
								$folder_id = "{$row["folderid"]}";
								//Remove the folder
								$Query = "delete from " . TABLE_PREFIX . "folders where id='" . $folder_id . "'";
								//echo($Query . "<br>\n");
								$AffectedRows = $dblink->exec($Query);
								if($AffectedRows != 1)
									$errorDeleting = true;

								//Remove all the bookmarks inside the folder
								$Query = "delete from " . TABLE_PREFIX . "favourites where folderid='" . $folder_id . "'";
								//echo($Query . "<br>\n");
								$dbResult = $dblink->query($Query);
							}

							//Delete the subscriptions in gfolders table
							$Query = ("delete from " . TABLE_PREFIX . "gfolders where group_id = " . $group_id);
							//echo($Query . "<br>\n");
							$AffectedRows = $dblink->exec($Query);
							if($AffectedRows < 1)
								$errorDeleting = true;

							if(!$errorDeleting)
							{
								$success = true;
								echo("<p class=\"success\">" . T_("Your group has been deleted") . ".</p>");
							}
							else
								echo("<p class=\"error\">" . T_("A problem occured when deleting the group folders and bookmarks") . ".</p>");
						}
						else
							echo("<p class=\"error\">" . T_("A problem occured when deleting the group information") . ".</p>");
					}
					else
						echo("<p class=\"error\">" . T_("The password is incorrect") . ".</p>");
				}
			}
			else
				echo("<p class=\"error\">" . T_("Please enter your password in order to delete your group") . ".</p>");
			
		}

		if(!$success)
		{
			/* Retrieve the name of the group */
			require_once('includes/groups_functions.php');
			$rec_name = returnGroupName($group_id);
?>
<?php echo sprintf(T_("Are you sure you want to delete the group <b>%s</b> along with all the bookmarks and users"),$rec_name);?>?<br><?php echo T_("Please enter your own password");?>.<br><br>
<form action="gdelete.php" method="post">
<input type="hidden" name="group_id" value="<?php echo("$group_id");?>" />
<span class="formsLabel"><?php echo T_("Enter Password");?></span>
<input type="password" name="pass" class="formtext" onfocus="this.select()" />
<input type="submit" name="submitted" value="<?php echo T_("Yes, I want to delete this group");?>" class="genericButton" />
</form>
<?php
		}
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>