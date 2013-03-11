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
	$actpass = $_POST["actpass"];
	$newpass = $_POST["newpass"];
	$renewpass = $_POST["renewpass"];

	include('access.php');
	$access = checkAccess();

	if($access)
	{
		echo("<h2>" . T_("Settings") . " -- " . T_("Change password") . "</h2>");

		$user = new User();
		$username = $user->getUsername();

		if($username == "demo") // Demo account
		{
			echo("<p class=\"error\">" . T_("This is the demo account") . ".</p>");
		}
		else
		{
			$success = false;
			include("includes/protection.php");
			if($actpass!=null)
				remhtml($actpass);
			if($newpass!=null)
				remhtml($newpass);
			if($renewpass!=null)
				remhtml($renewpass);

			if ($_POST['submitted'])
			{
				$user = new User();

				$resultArr = $user->changePassword($actpass, $newpass, $renewpass);
				$success = $resultArr['success'];

				if($success)
				{
					echo("<p class=\"success\">" . $resultArr['message'] . "</p>");
					echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to") . " " . T_("Settings") . "</a></p>");
				}
			}

			if(!$success)
			{
				if($resultArr['message'] != null)
					echo("<p class=\"error\">" . $resultArr['message'] . "</p>");
			?>

				<p><?php echo T_("In order to change your password, enter the current one and then type the new	one twice to confirm");?>.</p>
				<form action="changepass.php" method="post">
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
							<td></td>
							<td><input type="submit" class="genericButton" name="submitted" value="<?php echo T_("Change Password");?>" /></td>
						</tr>
					</table>
				</form>
	<?php
				echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to Settings") . "</a></p>");
			}
		}
	}
?>
<?php include('footer.php'); ?>