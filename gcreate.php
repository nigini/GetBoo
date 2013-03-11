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
		$gname = $_POST["gname"];
		$pass = $_POST["pass"];
		$pass2 = $_POST["pass2"];
		$description = $_POST["description"];

		$user = new User();
		$manager = $user->getUsername();

		$success = false;

		if ($_POST['submitted'])
		{
			include("includes/protection.php");
			if($gname!=null)
				remhtml($gname);
			if($description!=null)
				remhtml($description);
			if($pass!=null)
				remhtml($pass);
			if($pass2!=null)
				remhtml($pass2);

			if($gname != null && $description != null)
			{
				if(!(valid($gname, 20) && valid($pass, 20) && valid($pass2, 20) && strlen($description) <= 100))
				{
					echo("<p class=\"error\">" . T_("Check for invalid characters or length") . ".</p>");
				}
				else
				{
					if($pass != $pass2)
					{
						echo("<p class=\"error\">" . T_("Both passwords have to match") . ".</p>");
					}
					else
					{
						include('conn.php');
						$Query = ("select group_name from " . TABLE_PREFIX . "groups where (group_name='$gname')");
						//echo($Query . "<br>\n");
						$dbResult = $dblink->query($Query);
						$xusers = 0;
						while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
						{
							$xusers++;
						}
						if($xusers == 0)
						{
							//encrypt password
							if($pass != null)
								$passencrypt = $user->encryptPassword($pass);
							else
								$passencrypt = "";
							$Query = ("INSERT INTO " . TABLE_PREFIX . "groups ( Group_Name , Manager , Description , Password , Date_Created ) VALUES ('$gname', '$manager', '$description', '$passencrypt', now())");
							//echo($Query . "<br>\n");
							$AffectedRows = $dblink->exec($Query);
							if($AffectedRows == 1)
							{
								//Create the folder under the member's Groups folder with the Group name and description of the group
								include('includes/groups_functions.php');
								$success = createMemberGroupFolder($manager,$gname,$description);
								if($success == 1)
								{
									$success = true;
									echo("<p class=\"success\">" . T_("The group was successfully created") . ".</p>");
								}
								else
									echo("<p class=\"error\">" . T_("A problem occured when creating the folder of this group") . ".</p>");
							}
							else
								echo("<p class=\"error\">" . T_("A problem occured when creating your group information") . ".</p>");
						}
						else
							echo("<p class=\"error\">" . T_("This group name is already taken") . "!</p><p>" . T_("Please choose a different group name") . ".</p>");
						
					}
				}
			}
			else
			{
				echo("<p class=\"error\">" . T_("The form is incomplete") . ".</p>");
			}
		}

		if(!$success)
		{
	?>
<script type="text/javascript">
<!--
var checkAvailabilityWindow;
function checkGroupName()
{
	var groupToCheck = document.forms[0].gname.value;
	var topost = "checkGroup.php?groupToCheck=" + groupToCheck;
	if (groupToCheck != "")
	{
		checkAvailabilityWindow = window.open(topost,'checkAvailabilityWindow','height=100, width=300, toolbar=no, directories=no, status=no, location=no, menubar=no, scrollbars=no, resizable=no, left=300, top=300, screenX=300, screenY=300');
	}
	else
		alert('<?php echo T_("Enter the group name in the field");?>');
}
//-->
</script>
<form action="gcreate.php" method="post">
<table>
	<tr>
			<td><span class="formsLabelRequired"><?php echo T_("Group name");?></span></td>
			<td><input type="text" name="gname" size="20" maxlength="20" value="<?php echo $gname; ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b>&nbsp;<span class="formsLabelSmall"><a href="javascript:checkGroupName();"><?php echo T_("Check availability");?></a></span></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Password");?>*</span></td>
			<td><input type="password" name="pass" size="20" maxlength="20" value="<?php echo $pass; ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Retype password");?></span></td>
			<td><input type="password" name="pass2" size="20" maxlength="20" value="<?php echo $pass2; ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
			<td><span class="formsLabelRequired"><?php echo T_("Description");?></span></td>
			<td><input type="text" name="description" size="40" maxlength="100" value="<?php echo $description; ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('100 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
			<td></td>
			<td><input type="submit" name="submitted" value="<?php echo T_("Add Group");?>" class="genericButton" /></td>
	</tr>
</table>
</form>
<?php echo T_("All fields in bold are mandatory, and they can only contain letters, digits, '-' and '_' (except for the email)");?>.<br>
* <?php echo T_("Leave blank if you want to make the group public");?><br>
<?php
		}
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>