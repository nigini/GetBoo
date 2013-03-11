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

		$uname = $user->getUsername();
		$pass ="";
		$email ="";
		$passhint ="";
		$lastlog ="";
		$datejoin ="";
		$style = "";
		$email = $_POST["email"];
		$passhint = $_POST["passhint"];
		$style = $_POST["style"];

		$donor = $_POST["donor"];
		if($donor)
		{
			$realname = $_POST["realname"];
			$website = $_POST["website"];
			$displayemail = $_POST["displayemail"];
			$information = $_POST["information"];
		}

		include('includes/protection.php');
		if($email!=null)
			remhtml($email);
		if($passhint!=null)
			remhtml($passhint);
		if($realname!=null)
			remhtml($realname);
		if($website!=null)
			remhtml($website);
		if($information!=null)
			remhtml($information);

		echo("<h2>" . T_("Settings") . " -- " . T_("Modify account information") . "</h2>");

		if ($_POST['submitted'])
		{
			$displayemail = ($displayemail == "on")?1:0;
			if($donor)
				$resultArr = $user->changeAccountInfo($email, $passhint, $style, $donor, $realname, $displayemail, $website, $information);
			else
				$resultArr = $user->changeAccountInfo($email, $passhint, $style);
			$success = $resultArr['success'];

			if($success)
			{
				echo("<p class=\"success\">" . $resultArr['message'] . "</p>");
				if($resultArr['optmessage'] != null)
					echo("<p class=\"notice\">" . $resultArr['optmessage'] . "</p>");
			}
			else
			{
				echo("<p class=\"error\">" . $resultArr['message'] . "</p>");
			}
		}
		else
		{
			include('conn.php');
			if(IS_GETBOO)
				$donorStr = ", donor, realname, displayemail, website, information";
			$Query = "select email, passhint, style$donorStr from " . TABLE_PREFIX . "session where name='" . $uname . "'";
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);

			$count = 0;
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$email = ("{$row["email"]}");
				$passhint = ("{$row["passhint"]}");
				$style = ("{$row["style"]}");

				if(IS_GETBOO)
				{
					$donor = ("{$row["donor"]}");
					if($donor) // Retrieve donor info
					{
						$realname = ("{$row["realname"]}");
						$displayemail = ("{$row["displayemail"]}");
						$website = ("{$row["website"]}");
						$information = ("{$row["information"]}");
					}
				}
				$count++;
			}
			
		}
		$Auto = "Auto";
		$IE = "IE";
		$Firefox = "Firefox";
		$Opera = "Opera";

		if($style=="Auto")
			$Auto .= ("\" selected=\"selected");
		else if($style=="IE")
			$IE .= ("\" selected=\"selected");
		else if($style=="Firefox")
			$Firefox .= ("\" selected=\"selected");
		else if($style=="Opera")
			$Opera .= ("\" selected=\"selected");


		$checkedStatus = ($displayemail)?"checked=checked":"";
?>

<form action="umodifyaccount.php" method="post">
<input type="hidden" name="name" value="<?php echo("$uname"); ?>">
<table>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Email");?></span></td>
			<td><input type="text" name="email" size="40" maxlength="100" value="<?php echo("$email"); ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('100 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Password hint");?></span></td>
			<td><input type="text" name="passhint" size="30" maxlength="150" value="<?php echo("$passhint"); ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Style");?></span></td>
			<td><select name="style" class="formtext" onfocus="this.select()" /><option value="<?php echo("$Auto"); ?>"><?php echo T_("Auto");?></option><option value="<?php echo("$IE"); ?>"><?php echo T_("Internet Explorer");?></option><option value="<?php echo("$Firefox"); ?>"><?php echo T_("Firefox");?></option><option value="<?php echo("$Opera"); ?>"><?php echo T_("Opera");?></option></select></td>
	</tr>
<?php if($donor && IS_GETBOO) {?>
	<tr>
		<td colspan="2">
			<table style="background-color: white; border:1px #26a solid; margin: 0; padding: 0;">
			<input type="hidden" name="donor" value="<?php echo("$donor"); ?>" />
				<tr>
						<td colspan="2"><img src="images/donor-mini.gif" alt="Donor logo" title="Donor logo" /><?php echo T_("Donor options");?></td>
				</tr>
				<tr>
						<td><span class="formsLabel"><?php echo T_("Real Name");?></span></td>
						<td><input type="text" name="realname" size="25" maxlength="40" value="<?php echo("$realname"); ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('40 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
				</tr>
				<tr>
						<td><span class="formsLabel"><?php echo T_("Display email");?></span></td>
						<td><input type="checkbox" name="displayemail" <?php echo("$checkedStatus"); ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('<?php echo T_("Will display your email in your profile");?>');" onmouseout="return nd();">?</b></td>
				</tr>
				<tr>
						<td><span class="formsLabel"><?php echo T_("Website URL");?></span></td>
						<td><input type="text" name="website" size="40" value="<?php echo("$website"); ?>" class="formtext" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('<?php echo T_("No chars limit");?>');" onmouseout="return nd();">?</b></td>
				</tr>
				<tr>
						<td><span class="formsLabel"><?php echo T_("Information");?></span></td>
						<td><textarea name="information" cols="50" rows="4" style="font-size: small;" /><?php echo $information;?></textarea><br></td>
				</tr>
			</table>
		</td>
	</tr>
<?php } ?>
	<tr>
			<td></td>
			<td><input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Update");?>"></td>
	</tr>
</table>
</form>

<?php
			include('includes/browser.php');
			$br = new Browser;
			echo("<p>" . T_("Browser detected") . ":<br>\n");
			echo "$br->Platform, $br->Name " . T_("version") . " $br->Version";
			echo("</p>");
			echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to") . " " . T_("Settings") . "</a></p>");
	}
?>
<?php include('footer.php'); ?>