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
		include('conn.php');

		if (isset($_POST['group_id']))
		{
			 $group_id = $_POST['group_id'];
		}

		$user = new User();
		$username = $user->getUsername();

		// Check if manager joined
		include('includes/groups_functions.php');
		$partOf = checkIfManager($group_id, $username);

		if ($_POST['changeDesc'])
		{
			if (isset($_POST['desc']))
			{
				 $description = $_POST['desc'];
			}
			include("includes/protection.php");
			if($description!=null)
				remhtml($description);

			if(!$partOf)
				echo("<p class=\"error\">" . T_("You must be the manager of this group") . ".</p>");
			else
			{
				if($description != null && group_id != null && strlen($description) <= 100)
				{
					//Change the description
					$Query = ("UPDATE " . TABLE_PREFIX . "groups SET Description = '". $description . "' where group_id = " . $group_id);
					//echo($Query . "<br>\n");
					$AffectedRows = $dblink->exec($Query);
					if($AffectedRows == 1)
					{
						echo("<p class=\"success\">" . T_("You have changed the description of this group") . ".</p>");
					}
					else
						echo("<p class=\"error\">" . T_("Make sure you don't write the same description as it was before") . ".</p>");
				}
				else
					echo("<p class=\"error\">" . T_("Please enter the description in the corresponding field") . ".</p>");
			}
		}
		else if ($_POST['changePass'])
		{
			if (isset($_POST['actpass']))
			{
				 $actpass = $_POST['actpass'];
			}
			if (isset($_POST['newpass']))
			{
				 $newpass = $_POST['newpass'];
			}
			if (isset($_POST['renewpass']))
			{
				 $renewpass = $_POST['renewpass'];
			}

			include("includes/protection.php");
			if($actpass!=null)
				remhtml($actpass);
			if($newpass!=null)
				remhtml($newpass);
			if($renewpass!=null)
				remhtml($renewpass);

			if(!$partOf)
				echo("<p class=\"error\">" . T_("You must be the manager of this group") . ".</p>");
			else
			{
				if($actpass != null && $newpass != null && $renewpass != null && valid($actpass, 20) && valid($newpass, 20) && valid($renewpass, 20))
				{
					//Change the password
					$passencrypt = $user->encryptPassword($actpass);
					$Query = "select manager from " . TABLE_PREFIX . "groups where group_id=" . $group_id . " and password='" . $passencrypt . "'";
					$dbResult = $dblink->query($Query);

					$count = 0;
					while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
					{
						$count++;
					}

					if($count==0)
					{
						echo("<p class=\"error\">" . T_("The actual password is incorrect") . ".</p>");
					}
					else if($newpass != $renewpass)
					{
						echo("<p class=\"error\">" . T_("The new password does not match in both fields") . ".</p>");
					}
					else
					{
						$newpassencrypt = sha1($newpass);
						$Query = "update " . TABLE_PREFIX . "groups set password='" . $newpassencrypt . "' where group_id='" . $group_id . "'";
						$AffectedRows = $dblink->exec($Query);
						if($AffectedRows == 1)
							echo("<p class=\"success\">" . T_("You have successfully changed the password") . ".</p><p>" . T_("Please notify the members subscribing to your group") . ".</p>");
						else
							echo("<p class=\"error\">" . T_("There has been a problem while updating the password") . ".</p><p>" . T_("Make sure you don't type the same password as the actual one") . ".</p>");
					}
				}
				else
					echo("<p class=\"error\">" . T_("Missing values or invalid length for the password") . "</p>");
			}
		}

		/* Retrieve the name of the group */
		$rec_name = returnGroupName($group_id);
		echo("<b>" . T_("Edit information for group") . " ". $rec_name . "</b><br><br>\n");
		if(!$partOf)
			echo("<p class=\"error\">" . T_("You must be the manager of this group") . ".</p>");
		else
		{
			//Retrieve the description of the group
			$Query = ("select description from " . TABLE_PREFIX . "groups where (group_id = '$group_id')");
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);

			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$rec_desc = "{$row["description"]}";
			}

			//Check if the group is public or not
			$private = isGroupPrivate($group_id);
			if($private)
			{
?>
<b><?php echo T_("Change password");?></b>
<br><br>
<?php echo T_("In order to change the group password, enter the current one and then type the new one twice to confirm");?>.<br><br>
<form action="gedit.php" method="post">
<input type="hidden" name="group_id" value="<?php echo("$group_id");?>">
	<table>
		<tr>
			<td><span class="formsLabel"><?php echo T_("Current Password");?></span></td>
			<td><input type="password" name="actpass" maxlength="50" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
			<td><span class="formsLabel"><?php echo T_("New Password");?></span></td>
			<td><input type="password" name="newpass" maxlength="20" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
			<td><span class="formsLabel"><?php echo T_("Retype New Password");?></span></td>
			<td><input type="password" name="renewpass" maxlength="20" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
			<td</td>
			<td><input type="submit" class="genericButton" name="changePass" value="<?php echo T_("Change Password");?>"></td>
		</tr>
	</table>
</form>
<br>
<?php
			}
?>
<b><?php echo T_("Change description");?></b>
<form action="gedit.php" method="post">
<input type="hidden" name="group_id" value="<?php echo("$group_id");?>">
	<table>
		<tr>
			<td><span class="formsLabel"><?php echo T_("Description");?></span></td>
			<td><input type="text" name="desc" maxlength="100" size="40" value="<?php echo($rec_desc); ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('100 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
			<td</td>
			<td><input type="submit" class="genericButton" name="changeDesc" value="<?php echo T_("Change Description");?>"></td>
		</tr>
	</table>
</form>
<?php
		}
		
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>