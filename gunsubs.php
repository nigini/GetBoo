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
		include('gheader.php');

		if (isset($_POST['group_id']))
		{
			 $group_id = $_POST['group_id'];
		}

		$user = new User();
		$username = $user->getUsername();

		$success = false;

		if ($_POST['submitted'])
		{
			include('conn.php');

			if (isset($_POST['username']))
			{
				 $username = $_POST['username'];
			}
			else
				$username = $user->getUsername();

			if (isset($_POST['manager']))
			{
				 $isManager = true;
			}
			else
				$isManager = false;

			if (!empty ($_POST['removeBooks']))
			{
				define ('REMOVE', true);
			}

			if($group_id != null && $username != null)
			{
				include('includes/groups_functions.php');
				$partOf = checkIfManager($group_id, $username);
				if($partOf)
					echo("<p class=\"error\">" . T_("Since you are the manager of this group, you cannot unsubscribe from it. You can only delete the group") . ".</p>");
				else
				{
					//Remove the subscription from the table
					$Query = ("delete from " . TABLE_PREFIX . "gsubscriptions where group_id = " . $group_id . " and name = '" . $username . "'");
					//echo($Query . "<br>\n");
					$AffectedRows = $dblink->exec($Query);
					if($AffectedRows == 1)
					{
						//Get the folder_id of the member's group folder
						$gfolder_id = returnGroupFolderWithID($group_id, $username);

						$errorDeleting = false;

						//Remove the folder
						$Query = "delete from " . TABLE_PREFIX . "folders where name='" . $username . "' and id='" . $gfolder_id . "'";
						//echo($Query . "<br>\n");
						$AffectedRows = $dblink->exec($Query);
						if($AffectedRows != 1)
							$errorDeleting = true;

						if (defined ('REMOVE'))
						{
							//Remove all the bookmarks inside the folder
							$Query = "delete from " . TABLE_PREFIX . "favourites where name='" . $username . "' and folderid='" . $gfolder_id . "'";
							//echo($Query . "<br>\n");
							$dbResult = $dblink->query($Query);

							//Delete the subscription of the folder in gfolders
							$Query = ("delete from " . TABLE_PREFIX . "gfolders where group_id = " . $group_id . " and folderid = '" . $gfolder_id . "'");
							//echo($Query . "<br>\n");
							$AffectedRows = $dblink->exec($Query);
							if($AffectedRows != 1)
								$errorDeleting = true;
						}

						//If success
						if(!$errorDeleting)
						{
							if(!$isManager)
								echo("<p class=\"success\">" . T_("You have unsubscribed from this group") . ".</p>");
							else
							{
								echo("<p class=\"success\">" . T_("You have unsubscribed the user from your group") . ".</p>");
								echo("<br><br><form action=\"gmembers.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"submit\" class=\"genericButton\" value=\"<< " . T_("Members") . "\"></form>");
							}
							$success = true;
						}
						else
							echo("<p class=\"error\">" . T_("A problem occured when deleting the folder of this group") . ".</p>");
					}
					else
						echo("<p class=\"error\">" . T_("A problem occured when deleting you from this group") . ".<br>" . T_("If you are the manager of this group, you cannot unsubscribe from it") . ".</p>");
				}
			}
			else
				echo("<p class=\"error\">" . T_("Missing infomation") . "</p><p>" . T_("Make sure you don't resubmit the form") . ".</p></div>");
			
		}

		if(!$success)
		{
			/* Retrieve the name of the group */
			include('includes/groups_functions.php');
			$rec_name = returnGroupName($group_id);
?>
		<b><?php echo T_("Unsubscribe from this group");?> (<?php echo("$rec_name");?>)</b><br><br>
		<?php echo T_("Are you sure you want to unsubscribe from this group");?>?<br><br>
<form action="gunsubs.php" method="post">
<input type="hidden" name="group_id" value="<?php echo("$group_id");?>">
<span class="formsLabel"><?php echo T_("Remove my bookmarks in this group");?></span>
<input type="checkbox" name="removeBooks"><br><br>
<input type="submit" name="submitted" value="<?php echo T_("Yes, I want to unsubscribe");?>" class="genericButton" />
</form>

<?php
			echo("<br><form action=\"gdetails.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$group_id\"><input type=\"submit\" class=\"genericButton\" value=\"<< " . T_("Group Details") . "\"></form>");
		}
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>