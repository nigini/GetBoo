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
	include('access.php');
	$access = checkAccess('a');
	if($access)
	{
		include('conn.php');
		include('includes/protection.php');

		echo("<h2>" . T_("Settings") . " -- " . T_("Manage News") . "</h2>\n");
		echo("<div class=\"content\">");

		$delNewsId = $_POST['delNewsId'];
		if($delNewsId != null) //Delete a news
		{
			$Query = ("delete from " . TABLE_PREFIX . "news where newsid='" . $delNewsId . "'");
			//echo($Query . "<br>\n");
			$AffectedRows = $dblink->exec($Query);
			if($AffectedRows == 1)
			{
				echo("<p class=\"success\">" . T_("You have successfully deleted this news") . ".</p>\n");
			}
			else
				echo("<p class=\"error\">" . T_("There has been a problem when deleting the news") . ".</p>\n");
		}

		$user = new User();
		$username = $user->getUsername();

		if ($_POST['add_news'])
		{
			$title = $_POST["add_title"];
			$msg = $_POST["add_msg"];
			if($title != null && $msg != null)
			{
				if(!empty($title))
					$title = substr($title, 0, 75);
				$Query = sprintf("INSERT INTO " . TABLE_PREFIX . "news ( Author, Title, Msg, Date) VALUES ('" . $username . "', %s, %s, now())", quote_smart($title), quote_smart($msg));
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows == 1)
				{
					echo("<p class=\"success\">" . T_("You have successfully added this news") . ".</p>\n");
				}
				else
					echo("<p class=\"error\">" . T_("There has been a problem when adding the news") . ".</p>\n");
			}
			else
				echo("<p class=\"error\">" . T_("The form is incomplete") . ".</p>\n");
		}

		// TODO: What should we do if we have many admins? Should they be able to edit other admins posts?
		// TODO: BUG: When the admin accesses another user's account, the username is just wrong!
		$Query = ("select title, newsid from " . TABLE_PREFIX . "news where author='$username' order by newsid desc");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		$countNews = 0;
	  	while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	  	{
		  	if($countNews == 0)
		  		echo("<table class='sortable'>\n<thead><tr><th>" . T_("Number") . "</th><th>" . T_("Title") . "</th><th colspan='2' class='skipsort'>" . T_("Options") . "</th></tr></thead><tbody>");
			echo("<tr><td valign=\"top\" style=\"text-align: center\">{$row["newsid"]}</td><td>{$row["title"]}</td><td valign=\"bottom\"><form action=\"managenews.php\" method=\"post\"><input type=\"hidden\" name=\"delNewsId\" value=\"{$row["newsid"]}\"><input type=\"submit\" class=\"genericButton\" value=\"" . T_("Delete") . "\"></form></td><td valign=\"bottom\"><form action=\"newsmodify.php\" method=\"post\"><input type=\"hidden\" name=\"newsid\" value=\"{$row["newsid"]}\"><input type=\"submit\" class=\"genericButton\" value=\"" . T_("Modify") . "\"></form></td></tr>\n");
			$countNews++;
	  	}

	  	if($countNews == 0)
	  		echo("<p class=\"notice\">" . T_("You didn't write any news yet") . ".</p>\n");
	  	else
			echo("</tbody></table><br>\n");
		
?>
<b><?php echo T_("Add another news");?></b><br>
<form name="news" action="managenews.php" method="post">
<table>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Title");?></span></td>
			<td><input type="text" name="add_title" size="50" class="formtext" onfocus="this.select()" maxlength="75" /></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Message");?></span></td>
			<td><textarea cols="85" rows="10" name="add_msg" wrap="virtual" class="formtext" onfocus="this.select()" /></textarea></td>
	</tr>
	<tr>
			<td></td>
			<td><input type="submit" name="add_news" class="genericButton" value="Add News"></td>
	</tr>
</table>
</form>
<?php
		echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to Settings") . "</a></p>");
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>