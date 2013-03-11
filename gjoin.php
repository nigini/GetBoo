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
		include('includes/protection.php');

		if (isset($_POST['group_id']))
		{
			 $group_id = $_POST['group_id'];
		}

		$user = new User();
		$username = $user->getUsername();

		// Check if already joined
		include('includes/groups_functions.php');
		$partOf = (checkIfManager($group_id, $username) || checkIfMember($group_id, $username));
		if($partOf)
		{
			echo("<p class=\"error\">" . T_("You are already a member of this group") . ".</p>");
?>
	<br>
	<form action="gunsubs.php" method="post">
	<input type="hidden" name="group_id" value="<?php echo("$group_id");?>">
	<input type="submit" value="<?php echo T_("Unsubscribe from this group");?>" class="genericButton">
	</form>
<?php
		}
		else
		{
			$success = false;
			if ($_POST['joingGroup'])
			{
				if (isset($_POST['pass']))
				{
					 $password = $_POST['pass'];
				}

				if($password != null)
					$passencrypt = sha1($password);
				else
					$passencrypt = "";

				//Check if the pass string is valid (for protection)
				if(!(valid($password, 20)))
				{
					echo("<p class=\"error\">" . T_("The password you entered is invalid") . ".</p>");
				}
				else
				{
					// Check if the user is already part of the group
					if($partOf)
					{
						echo("<p class=\"error\">" . T_("You are already a member of this group") . ".</p>");
					}
					else
					{
						$Query = sprintf("select group_name from " . TABLE_PREFIX . "groups where group_id=" . $group_id . " and password='" . $passencrypt . "'");
						//echo($Query . "<br>\n");
						$dbResult = $dblink->query($Query);
						$check = 0;
						while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
						{
							$check++;
						}

						if($check == 1)
						{
							//Insert member into the gsubscriptions table
							$Query = ("INSERT INTO " . TABLE_PREFIX . "gsubscriptions ( Group_ID , Name , Date_Join , Priv ) VALUES ('$group_id', '$username', now(), '1')");
							//echo($Query . "<br>\n");
							$AffectedRows = $dblink->exec($Query);
							if($AffectedRows == 1)
							{
								//Create the folder under the member's Groups folder with the Group name and description of the group
								$success = createMemberGroupFolder($username,returnGroupName($group_id),returnGroupDesc($group_id));
								if($success == 1)
								{
									$success = true;
									echo("<p class=\"success\">" . T_("Your are now a member of this group") . ".</p><p><b>" . T_("Details of the group") . "</b><br><br>");
									include('includes/gdetails_body.php');
								}
								else
									echo("<p class=\"error\">" . T_("A problem occured when creating the folder of this group") . ".</p>");
							}
							else
								echo("<p class=\"error\">" . T_("A problem occured when adding you to this group") . ".</p>");
						}
						else
							echo("<p class=\"error\">" . T_("The password you entered is invalid") . ".</p>");
					}
				}
			}
			if(!$success)
			{
				/* Check if the user needs to enter a password to join the group */

				$Query = ("select group_name, group_id, description, manager, password from " . TABLE_PREFIX . "groups where (group_id = '$group_id')");
				//echo($Query . "<br>\n");
				$dbResult = $dblink->query($Query);

				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$rec_id = "{$row["group_id"]}";
					$rec_name = "{$row["group_name"]}";
					$rec_password = "{$row["password"]}";
				}
?>
				<b><?php echo T_("Join this group");?> (<?php echo("$rec_name");?>)</b><br><br>
<?php
				if($rec_password != "")
				{
					echo(T_("Private group (password required)") . ":<br><br>");
?>
<form action="gjoin.php" method="post">
<input type="hidden" name="group_id" value="<?php echo("$rec_id");?>">
<table>
	<tr>
			<td><span class="formsLabel"><b><?php echo T_("Enter Password");?></span></td>
			<td><input type="password" name="pass" size="20" maxlength="20" value="<?php echo $pass; ?>" class="formtext" onfocus="this.select()" /></td>
	</tr>
	<tr>
			<td></td>
			<td><input type="submit" name="joingGroup" value="<?php echo T_("Join Group");?>" class="genericButton" /></td>
	</tr>
</table>
</form>
<?php
				}
				else
				{
					echo(T_("Public group (no password required)") . "<br><br>");
?>
<form action="gjoin.php" method="post">
<input type="hidden" name="group_id" value="<?php echo("$rec_id");?>">
<input type="submit" name="joingGroup" value="<?php echo T_("Join Group");?>" class="genericButton" /></td>
</FORM>
<?php
				}
			}
			
		}
		echo("<br><form action=\"gdetails.php\" method=\"post\"><input type=\"hidden\" name=\"group_id\" value=\"$rec_id\"><input type=\"submit\" class=\"genericButton\" value=\"<< " . T_("Go Back to Group Details") . "\"></form>");
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>