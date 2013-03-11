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
	echo("<h2>" . T_("Modify news") . "</h2>");

	$newsid = $_POST["newsid"];
	$title = $_POST["title"];
	$msg = $_POST["msg"];

	$success = false;

	include('conn.php');

	if ($_POST['submitted'])
	{
		if($title != null && $newsid != null && $msg != null)
		{
			if(!empty($title))
				$title = substr($title, 0, 75);
			// TODO: Check if we need to retrieve date when updating a news. Otherwise remove query
			$Query = ("select date from " . TABLE_PREFIX . "news where newsid = '" . $newsid . "'");
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				$rec_date = "{$row["date"]}";

			$Query = "update " . TABLE_PREFIX . "news set title='" . $title . "', msg='" . $msg . "', date='" . $rec_date . "' where newsid='" . $newsid . "'";
			//echo($Query . "<br>\n");
			$AffectedRows = $dblink->exec($Query);
			if($AffectedRows == 1)
			{
				$success = true;
				echo("<p class=\"success\">" . T_("You have successfully updated this news") . ".</p>");
			}
			else
				echo("<p class=\"error\">" . T_("Modify this news again since no change has been detected") . ".</p>");
		}
		else
		{
			echo("<p class=\"error\">" . T_("The form is incomplete") . ".</p>");
		}
	}

	if($newsid != null)
	{
		$Query = "select title, msg from " . TABLE_PREFIX . "news where newsid='" . $newsid . "'";
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		$found = false;
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$title = ("{$row["title"]}");
			$msg  = ("{$row["msg"]}");
			$found = true;
		}

		if($found && $newsid!=null && $title!=null && $msg!=null)
		{
			$strBack = ($success) ? "<< " . T_("Back to News") . "" : "" . T_("Cancel") . "";
			?>
<form action="newsmodify.php" method="post">
<input type="hidden" name="newsid" value="<?php echo("$newsid"); ?>">
<table>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Title");?></span></td>
			<td colspan="2"><input type="text" name="title" size="50" value="<?php echo("$title"); ?>" class="formtext" onfocus="this.select()" maxlength="75" /></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Message");?></span></td>
			<td colspan="2"><textarea cols="85" rows="10" name="msg" wrap="virtual" class="formtext" onfocus="this.select()" /><?php echo("$msg"); ?></textarea></td>
	</tr>
	<tr>
			<td></td>
			<td><input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Modify News");?>"></form></td><td><form action="managenews.php" method="post"><input type="submit" class="genericButton" value="<?php echo($strBack);?>"></form></td>
	</tr>
</table>
<?php
		}
	}
	else
	{
		echo("<p class=\"error\">" . T_("The ID of the news is missing") . "</p>");
	}
	
}
?>
<?php include('footer.php'); ?>