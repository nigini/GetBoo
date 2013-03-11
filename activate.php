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

//TODO: Add CAPTCHA also for activation?
	session_start();
	require_once('includes/user.php');
	$user = new User();
	if($user->isLoggedIn()) // User logged in
	{
		//Redirect the user to his bookmark's page
		header('Location: books.php');
	}
	else
	{
		include("config.inc.php");
		$customTitle = T_("Activation");
		
		$success = false;
		$firstTime = false;
		$aname = null;
		$id = null;

		if ($_POST['submitted'] != "" || ( isset($_POST['aname']) && isset($_POST['id']) ))
		{

			// Get the user's input from the form
			$aname = $_POST['aname'];
			$id = $_POST['id'];
		}
		else if(isset($_GET['aname']) && isset($_GET['id']))
		{
			// Get the user s input from the url
			$aname = $_GET['aname'];
			$id = $_GET['id'];
		}
		else
			$firstTime = true;

		if(!$firstTime)
		{
			if($aname != "" && $id != "")
			{
				include('conn.php');
				$Query = "select activated from " . TABLE_PREFIX . "activation where (name='" . $aname . "' and id='" . $id . "')";
				//echo($Query . "<br>\n");
				$dbResult = $dblink->query($Query);

				$yesno = '';
				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$yesno = ("{$row["activated"]}");
				}

				if($yesno=='N')
				{
					$Query = "update " . TABLE_PREFIX . "activation set activated='Y' where (name='" . $aname . "')";
					//echo($Query . "<br>\n");
					$AffectedRows = $dblink->exec($Query);
					if($AffectedRows == 1)
					{
						//Enable the user's account
						$Query = "update " . TABLE_PREFIX . "session set Status='normal' where (name='" . $aname . "')";
						//echo($Query . "<br>\n");
						$AffectedRows = $dblink->exec($Query);
						if($AffectedRows == 1)
						{
							$user->forceLogin($aname);
							$success = true;
							include('header.php');
							echo("<h2>" . T_("Account Activation") . "</h2>");
							echo("<p class=\"success\">" . T_("Your account is now activated") . "!</p><p>" . T_("Thank you for registering an account with us") .
							"!<br>" . sprintf(T_("You can now access your account %sbookmarks</a>"),"<a href=\"books.php\">"));
							require_once('includes/browser.php');
							$browser = new Browser;
							//Check if the browser is Firefox, so we show the extension
							if($browser->Name == "Firefox")
							{
								echo(".</p>");
								include("includes/ff_extension.php");
							}
							else
							{
								echo(" " . T_("or add the bookmarklets in your browser") . ".</p>");
								echo("<div class=\"content\">");
								include('includes/easybook_content.php');
							}
						}
						else
						{
							$errorStr = T_("An error occured while activating your accont");
						}
					}
					else
					{
						$errorStr = T_("An error occured while activating your accont");
					}
				}
				else if($yesno=='Y')
				{
					$success = true;
					$errorStr = T_("Your account is already activated") . "!</p><p>" .
					sprintf(T_("You can access it from the <a href=\"%s\">login</a> page"),"login.php");
				}
				else
				{
					$errorStr = T_("The username and activation code don't match, or the user does not exist");
				}
				
			}
			else
			{
				$errorStr = T_("Both the username and activation code are required");
			}
		}
		if(!$success)
		{
			
			include('header.php');
			echo("<h2>" . T_("Account Activation") . "</h2>");
			if($errorStr)
				echo("<p class=\"error\">" . $errorStr . ".</p>");
?>

<p><?php echo T_("You must enter your username <b>and</b> your activation code (the one you received at your email address) to get your account activated");?>.</p>
<form method="post" action="activate.php">
<table>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Username");?></span></td>
			<td><input type="text" name="aname" value="<?php echo $aname; ?>" size="20" class="formtext" maxlength="20" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Activation code");?></span></td>
			<td><input type="text" name="id" value="<?php echo $id; ?>" size="40" maxlength="100" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('100 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
			<td></td>
			<td><input type="submit" name="submitted" value="<?php echo T_("Activate");?>" class="genericButton" /></td>
	</tr>
</table>
</form>
<?php
		}
	}
?>
<?php include('footer.php'); ?>