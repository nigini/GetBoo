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
		include('includes/f_deleteaccount.php');
		echo("<h2>" . T_("Settings") . " -- " . T_("Delete Account") . "</h2>");

		$user = new User();
		$username = $user->getUsername();

		$success = false;
		
		if($username == "demo") // Demo account
		{
			echo("<p class=\"error\">" . T_("This is the demo account") . ".</p>");
		}
		else
		{
			if ($_POST['submitted'])
			{
				$uname = $_POST['uname'];
				$actpass = $_POST['pass'];
	
				include('conn.php');
				if($user->isAdmin())
				{
					deleteUserAccount($uname);
					$success = true;
				}
				else if($actpass != null)
				{
					$passencrypt = $user->encryptPassword($actpass);
					$Query = "select name from " . TABLE_PREFIX . "session where name='" . $uname . "' and pass='" . $passencrypt . "'";
					$dbResult = $dblink->query($Query);
					if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
					{
						deleteUserAccount($uname);
						$success = true;
					}
					else
						echo("<p class=\"error\">" . T_("The password is incorrect") . "</p>");
				}
				else
					echo("<p class=\"error\">" . T_("Please enter your password in order to delete your account") . ".</p>");
			}
	
			if(!$success)
			{
?>

<form action="deleteaccount.php" method="post">
<input type="hidden" name="uname" value="<?php $user = new User(); echo($user->getUsername()); ?>">
<p><?php echo T_("Are you sure you want to delete your account");?>?</p>
<p><span class="formsLabel"><?php echo T_("Enter Password");?></span>
<input type="password" name="pass" maxlength="20" class="formtext" onfocus="this.select()" />
<input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Yes, I want to delete my account");?>">
</form>
</p>
<?php
				echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to Settings") . "</a></p>");
			}
		}
	}
?>
<?php include('footer.php'); ?>