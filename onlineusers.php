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
		echo("<h2>" . T_("Settings") . " -- " . T_("Online Users") . "</h2>");
		echo("<p>" . sprintf(T_("You can see a list of users who have been active in the past %s minutes"),(ONLINE_TIMEOUT / 60)) . ".</p>\n");
		include('conn.php');
		$Query = "select name, LastActivity as formatted_time from " . TABLE_PREFIX . "session where " . DATE_DIFF_SQL . " LastActivity) < " . ONLINE_TIMEOUT . " and status <> 'disabled' order by LastActivity";
		$dbResult = $dblink->query($Query);
		//echo($Query . "<br>");
		$count = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			if($count == 0)
			{
				echo("<div class=\"content\"><table class='sortable'>\n<thead><tr><th>" . T_("User") . "</th><th>" . T_("Last Activity") . "</th></tr></thead><tbody>\n");
				require_once('includes/convert_date.php');
			}
			$user_rec = ("{$row["name"]}");
			$date1 = ("{$row["formatted_time"]}");

			$date2 = convert_date($date1);
			echo("<tr><td><a href=\"userb.php?uname=" . $user_rec . "\">" . $user_rec . "</a></td><td>" . $date2 . "</td></tr>\n");
			$count++;
		}

		if($count == 0)
			echo("<p>" . sprintf(T_("No users have been active in the past %s minutes"),(ONLINE_TIMEOUT / 60)) . ".</p>\n");
		else
			echo("</tbody></table></div>\n");

		echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to Settings") . "</a></p>");

		
	}
?>
<?php include('footer.php'); ?>