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
$sorting_script = true;
include('header.php'); ?>
<?php

	$oldname = $_POST["oldname"];
	$uname = $_POST["name"];
	$pass = $_POST["pass"];
	$email = $_POST["email"];
	$passhint = $_POST["passhint"];
	$lastlog = $_POST["lastlog"];
	$datejoin = $_POST["datejoin"];
	$status  = $_POST["status"];
	$style  = $_POST["style"];
	$lastactivity  = $_POST["lastactivity"];
	$activationcode = $_POST["activationcode"];
	$IP = $_POST["IP"];
	$outlogin = "";

	include('access.php');
	$access = checkAccess();
	if($access)
	{
		$success = false;
		echo("<h2>" . T_("Settings") . " -- " . T_("Modify user account") . "</h2>");
		include('conn.php');
		require_once('includes/convert_date.php');

		if ($_POST['submitted'])
		{
			if($uname!=null && $pass!=null && $email!=null && $status!=null && $style!=null)
			{
				if(IS_GETBOO) // Donor info
				{
					$donor = $_POST["donor"];
					$donorQry = ", donor='$donor'";
				}
				$Query = "update " . TABLE_PREFIX . "session set name='$uname', pass='$pass', email='$email', passhint='$passhint', status='$status', style='$style'$donorQry where name='" . $oldname . "'";
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows == 1)
				{
					echo("<p class=\"success\">" . T_("You have updated this account") . "</p>\n");
					$success = true;
				}
				else
					echo("<p class=\"error\">" . T_("Make sure you don't enter the same account information as the current one") . ".</p>\n");
			}
			else
			{
				echo("<p class=\"error\">" . T_("The form is incomplete") . ".</p>\n");
			}
		}

		if(IS_GETBOO)
			$donorQry = ", s.donor";
		$Query = "select s.pass, s.email, s.passhint, lastlog as lastlog, datejoin as datejoin,
						 status, style, lastactivity as lastactivity, id as activationcode, ip$donorQry
					 from " . TABLE_PREFIX . "session s, " . TABLE_PREFIX . "activation a
					 where s.name='" . $uname . "' and s.name = a.name";
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$pass = ("{$row["pass"]}");
			$email = ("{$row["email"]}");
			$passhint = ("{$row["passhint"]}");
			$lastlog = ("{$row["lastlog"]}");
			$datejoin = ("{$row["datejoin"]}");
			$status  = ("{$row["status"]}");
			$style = ("{$row["style"]}");
			$lastactivity = ("{$row["lastactivity"]}");
			$activationcode = ("{$row["activationcode"]}");
			$IP = ("{$row["ip"]}");
			if(IS_GETBOO)
				$donor = ("{$row["donor"]}");
		}

		if($uname!=null && $pass!=null && $email!=null && $lastlog!=null && $datejoin!=null && $status!=null)
		{
			$Auto = "Auto";
			$IE = "IE";
			$Firefox = "Firefox";

			if($style=="Auto")
				$Auto .= ("\" selected=\"selected");
			else if($style=="IE")
				$IE .= ("\" selected=\"selected");
			else if($style=="Firefox")
				$Firefox .= ("\" selected=\"selected");

			$adminStr = "admin";
			$normalStr = "normal";
			$disabledStr = "disabled";

			if($status=="admin")
				$adminStr .= ("\" selected=\"selected");
			else if($status=="normal")
				$normalStr .= ("\" selected=\"selected");
			else if($status=="disabled")
				$disabledStr .= ("\" selected=\"selected");

			$lastlogC = convert_date($lastlog);
			$datejoinC = convert_date($datejoin);
			if($lastactivity != 0)
				$lastactivityC = convert_date($lastactivity);
			else
				$lastactivityC = "The user logged out properly";

			?>
<form action="modifyaccount.php" method="post">
<input type="hidden" name="oldname" value="<?php echo("$uname"); ?>">
<table>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Name");?></span></td>
			<td colspan="3"><input type="text" name="name" size="20" class="formtext" onfocus="this.select()" value="<?php echo("$uname"); ?>"></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Password");?></span></td>
			<td colspan="3"><input type="text" name="pass" size="40" class="formtext" onfocus="this.select()" value="<?php echo("$pass"); ?>"></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Email");?></span></td>
			<td colspan="3"><input type="text" name="email" size="40" class="formtext" onfocus="this.select()" value="<?php echo("$email"); ?>"></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Password Hint");?></span></td>
			<td colspan="3"><input type="text" name="passhint" size="30" class="formtext" onfocus="this.select()" value="<?php echo("$passhint"); ?>"></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Last Login");?></span></td>
			<td colspan="3"><?php echo("$lastlogC"); ?></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Date Joined");?></span></td>
			<td colspan="3"><?php echo("$datejoinC"); ?></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Status");?></span></td>
			<td colspan="3"><select name="status" class="formtext" onfocus="this.select()" /><option value="<?php echo("$adminStr"); ?>"><?php echo T_("Admin");?></option><option value="<?php echo("$normalStr"); ?>"><?php echo T_("User");?></option><option value="<?php echo("$disabledStr"); ?>"><?php echo T_("Disabled");?></option></select></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Style");?></span></td>
			<td colspan="3"><select name="style" class="formtext" onfocus="this.select()" /><option value="<?php echo("$Auto"); ?>"><?php echo T_("Auto");?></option><option value="<?php echo("$IE"); ?>"><?php echo T_("Internet Explorer");?></option><option value="<?php echo("$Firefox"); ?>"><?php echo T_("Firefox");?></option></select></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Last Activity");?></span></td>
			<td colspan="3"><?php echo("$lastactivityC"); ?></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Activation Code");?></span></td>
			<td colspan="3"><?php echo("$activationcode"); ?></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("IP");?></span></td>
			<td colspan="3"><?php echo("$IP"); ?></td>
	</tr>
	<?php if(IS_GETBOO) { ?>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Donor");?></span></td>
			<td colspan="3"><select name="donor" class="formtext" onfocus="this.select()" /><option value="1"<?php echo ($donor==1)?" selected=selected":""; ?>><?php echo T_("Yes");?></option><option value="0"<?php echo ($donor==0)?" selected=selected":""; ?>><?php echo T_("No");?></option></select></td>
	</tr>
	<?php } ?>
	<tr>
			<td></td>
			<td><input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Modify");?>"></form></td>
			<td><form action="accessaccount.php" method="post"><input type="hidden" name="name" value="<?php echo("$uname"); ?>">
			<input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Access");?>"></form></td>
			<td>
			<form action="deleteaccount.php" method="post"><input type="hidden" name="uname" value="<?php echo("$uname"); ?>">
			<input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Delete");?>"></form></td>
	</tr>
</table>

<?php
			if($lastlogC  != "")
				echo("<p>" . sprintf(T_("User's last login was on %s"),$lastlogC) . "</p>\n");
		}
		echo("<p><a href=\"manageusers.php\"><< " . T_("Back") . "</a></p>");

		//Display users login hits
		$Query = "select time as timeL, ip, success from " . TABLE_PREFIX . "loginhits where name='" . $uname . "' order by time desc";
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		$tableHeader = ("<table class='sortable'>\n<thead>\n<tr>\n<th>Time</th>\n<th>IP</th>\n<th>Success</th>\n</tr>\n</thead>\n<tbody>\n");
		$count = 0;

		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			if($count == 0)
				echo($tableHeader);
			$time_r = ("{$row["timel"]}");
			$time_rC = convert_date($time_r);
			$ip_r = ("{$row["ip"]}");
			$success_r = ("{$row["success"]}");
			echo("<tr>\n<td>$time_rC</td>\n<td>$ip_r</td>\n<td>$success_r</td>\n</tr>\n");
			$count++;
		}
		if($count > 0)
			echo "</tbody>\n</table>\n";

		
	}
?>
<?php include('footer.php'); ?>