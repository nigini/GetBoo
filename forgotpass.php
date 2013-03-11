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
	// Get the user s input from the form
	$aname = $_POST['aname'];
	$email = $_POST['email'];
	$success = false;

	if ($_POST['submitted'])
	{
		if($aname != null && $email != null)
		{
			include('conn.php');
			$Query = "select PassHint from " . TABLE_PREFIX . "session where (name='" . $aname . "' and email='" . $email . "')";
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);

			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$success = true;
				$question = ("{$row["passhint"]}");

				//Check if the user has provided a password hint question
				if($question)
					echo("<p class=\"notice\">" . T_("Your password hint question is") . ": <b>$question</b></p>\n");
				else
					echo("<p class=\"error\">" . T_("You didn't provide any hint question") . ".</p>\n");

				if(strtolower($aname) != "demo")
				{
					echo("<p>" . T_("If this hint doesn't help you, we can email you a new password") . ".</p>");
					?>
					<form action="emailpass.php" method="post">
					<input type="hidden" name="aname" value="<?php echo("$aname"); ?>" />
					<input type="hidden" name="email" value="<?php echo("$email"); ?>" />
					<table><tr><td><input type="submit" class="genericButton" name="newBtn" value="<?php echo T_("New Password");?>!" /></td></tr></table>
					</form>
	<?php
				}
			}
			else
			{
				echo("<p class=\"error\">" . T_("The username and the email don't match, or the user does not exist"). "</p>");
			}
			
		}
		else
		{
			echo("<p class=\"error\">" . T_("The form is incomplete") . ".</p><p>" . T_("You must provide both fields, the username and email (either provided during registration or updated after)") . "</p>");
		}
	}
	if(!$success)
	{
?>
<p><?php echo T_("You must enter your username <b>and</b> your email to get your password hint question");?>.<br>
<?php echo T_("If it doesn't help you recover your password, you will get the possibility to receive a new password");?>.</p>
<form method="post" action="forgotpass.php">
<table>
	<tr>
		<td><span class="formsLabel"><?php echo T_("Username");?></span></td>
		<td><input type="text" name="aname" maxlength="20" value="<?php echo $aname; ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
		<td><span class="formsLabel"><?php echo T_("Email address");?></span></td>
		<td><input type="text" name="email" size="40" maxlength="150" value="<?php echo $email; ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Hint question");?>" /></td>
	</tr>
</table>
</form>
<p><a href="newuser.php"><?php echo T_("New User");?>?</a></p>
<?php
	}
?>
<?php include('footer.php'); ?>