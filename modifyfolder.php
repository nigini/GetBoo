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
ob_start();
include('header.php');
include('access.php');
$access = checkAccess();
if($access)
{
	echo("<h2>" . T_("Modify folder") . "</h2>");
	include('conn.php');
	include('includes/bookmarks.php');

	if(isset($_POST["id"]))
		$id = $_POST["id"];
	else if(isset($_GET["id"]))
		$id = $_GET["id"];
	$pid = $_POST["pid"];
	$title = $_POST["title"];
	$description = $_POST["description"];

	$user = new User();
	$username = $user->getUsername();

	$success = false;

	if($id != null && f_belongs_to($id, $username))
	{
		if ($_POST['submitted'])
		{
			if($title != null)
			{
				include('includes/protection.php');
				$title = filter($title);
				if($description != null)
					$description = filter($description);
				$Query = sprintf("update " . TABLE_PREFIX . "folders set title=%s, description=%s where id='" . $id . "'", quote_smart($title), quote_smart($description));
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows >= 0)
				{
					if(!$pid)
						header("Location: books.php");
					else
						echo("<p class=\"success\">" . T_("The folder has been updated") . "</p>");
					$success = true;
				}
				else
					echo("<p class=\"error\">" . T_("There has been a problem when updating the folder") . ".</p>");
			}
			else
				echo("<p class=\"error\">" . T_("The form is incomplete") . "</p>");
		}

		if($id != null)
		{
			$Query = "select title, description from " . TABLE_PREFIX . "folders where id='" . $id . "'";
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);

			$found = false;
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$title = ("{$row["title"]}");
				$description = ("{$row["description"]}");
				$found = true;
			}
			//echo($question . "<br>\n");
			if($found)
			{
				//TODO: Make it go back to the parent folder when modified with success
				$strBack = ($success) ? "<< " . T_("Back to Folder") . "" : "" . T_("Cancel") . "";
				?>
	<form action="modifyfolder.php" method="post">
	<input type="hidden" name="id" value="<?php echo("$id"); ?>">
	<input type="hidden" name="pid" value="<?php echo("$pid"); ?>">
	<table>
		<tr>
				<td><span class="formsLabel"><?php echo T_("Title");?></span></td>
				<td colspan="2"><input type="text" name="title" size="30"  maxlength="30" class="formtext"  value="<?php echo("$title"); ?>" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('30 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
				<td><span class="formsLabel"><?php echo T_("Description");?></span></td>
				<td colspan="2"><input type="text" name="description" size="75" maxlength="150" class="formtext" value="<?php echo("$description"); ?>" onfocus="this.select()" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
		</tr>
		<tr>
				<td></td>
				<td><input type="submit" name="submitted" value="<?php echo T_("Modify Folder");?>" class="genericButton" /></form></td><td><form action="books.php" method="post"><input type="hidden" name="folderid" value="<?php echo($pid);?>"><input type="submit" class="genericButton" value="<?php echo($strBack);?>"></form></td>
		</tr>
	</table>
	</form>

	<?php
			}
		}
		else
		{
			echo("<p class=\"error\">" . T_("The form is incomplete") . ".</p>");
		}
	}
	else
	{
		echo("<p class=\"error\">" . T_("Wrong Folder ID") . ".</p>");
	}
	
}
?>
<?php include('footer.php'); 
ob_end_flush();?>