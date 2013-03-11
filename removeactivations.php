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
	//Removes old activations

	include('access.php');
	$access = checkAccess('a');
	if($access)
	{
		include('conn.php');

		echo("<h2>" . T_("Settings") . " -- " . T_("Inactive Users") . "</h2>\n");

		$Query = ("select session.name, session.datejoin AS formatted_time from " . TABLE_PREFIX . "session as session, " . TABLE_PREFIX . "activation as activation where (session.name = activation.name and session.status='disabled' and activation.activated='N')");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		$count = 0;
	  	while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	  	{
		  	if($count == 0)
		  		echo("<div class=\"content\"><table>\n");
	  		$date1 = ("{$row["formatted_time"]}");
	  		include('includes/convert_date.php');
			$date2 = convert_date($date1);
			echo("<tr><td valign=\"top\">{$row["name"]}</td><td valign=\"bottom\"><form action=\"deleteaccount.php\" method=\"post\"><input type=\"hidden\" name=\"uname\" value=\"{$row["name"]}\"><input type=\"submit\" name=\"submitted\" class=\"genericButton\" value=\"Delete\"></form></td><td valign=\"bottom\"><form action=\"modifyaccount.php\" method=\"post\"><input type=\"hidden\" name=\"name\" value=\"{$row["name"]}\"><input type=\"submit\" class=\"genericButton\" value=\"Modify\"></form></td><td valign=\"top\">$date2</td></tr>\n");
			$count++;
	  	}
		if($count == 0)
		{
			echo("<p class=\"notice\">" . T_("No users in the database who have not activated yet their account") . ".</p>");
			echo("<p><a href=\"manageusers.php\"><< " . T_("Back") . "</a></p>");
		}
		else
		{
			echo("</table>");
			// Show the option to mass remove old unactivated accounts
?>
		<form action="removeactivations_delete.php" method="POST">
		<?php echo T_("Delete users who registered without activating their account in the last number of days");?>:
  		<input type="text" name="dateDiff" value="60" size="2" maxlength="3"/>
		<input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Remove");?>"/>
		</form>
<?php
			echo("</div>\n");

		}
	}
?>
<?php include('footer.php'); ?>