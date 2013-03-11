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
session_start();
require_once('includes/user.php');
$user = new User();

// If connected, redirect
if($user->isLoggedIn())
	header("Location: books.php");
include('header.php'); ?>
<h2><?php echo T_("Forgot password");?></h2>
<?php
$user = new User();
// Get the user s input from the form
$aname = $_POST['aname'];
$email = $_POST['email'];

	if($aname != null && $email != null)
	{
		include('conn.php');
		//Generate a new pass with a random hash
		$newpass = uniqid(rand());
		$domain = $_SERVER['REMOTE_ADDR'];
		$passencrypt = $user->encryptPassword($newpass);
		$Query = "update " . TABLE_PREFIX . "session set pass='$passencrypt' where name='$aname'";
		$AffectedRows = $dblink->exec($Query);
		if($AffectedRows == 1)
		{
			$mailheaders = "From: " . WEBSITE_NAME . " Support <> \r\n";
			// Please change to your email (for support, abuse or anything else)
			$mailheaders .= "Reply-To: support@getboo.com\r\n";
			$emailmsg = sprintf(T_("There has been a password change request.\n\nYour account information is:\n\n--------\nUsername: %s\nPassword: %s\n--------\n\nPlease keep this information.\nOnce you log in with the new password, you can change it in your settings.\n\nIf you didn't ask for a new password, please forward this message to: abuse@getboo.com\nIP address of the user requesting a new password: $domain\n\nSincerely,\n" . WEBSITE_NAME),$aname, $newpass);
			
			//Message in case the php mail function doesn't work
			$dieMessage = str_replace("\n", "<br>", $emailmsg);
			
			@mail($email, sprintf(T_("New password for your %s account"),WEBSITE_NAME), $emailmsg, $mailheaders) or die("<p class=\"notice\">" . T_("Could not send the email: Here is a copy of the email") . ":</p><p>$dieMessage</p>");;
			echo("<p class=\"success\">" . T_("New password generated") . "!</p><p>" . T_("Please check your email and log in with the new password") . ".<br>\n" . T_("The email is already sent, but with some free email providers, it might take a few hours") . ".</p>");

		}
		else
		{
			echo("<p class=\"error\">" . T_("Could not store the new password") . ".</p>\n");
		}
	}
	else
	{
		echo("<p class=\"error\">" . T_("Error") . "</p>\n");
	}
?>
<?php include('footer.php'); ?>