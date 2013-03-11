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
?>
<div style="clear: both">
<?php
	if(USE_SCREENSHOT && strpos(SCREENSHOT_URL, "artviper") !== false) // As required in their agreements
	{	?>
		<p>
		The screen shots are powered by <a title="http://www.artviper.com/tools.php" href="http://www.artviper.com/tools.php">Artviper</a>.
		</p>
<?php
	}
	if(IS_GETBOO)
	{
		include('gbfooter.php');
	}
	if($boolMain)
	{
		include('conn.php');
		echo("<div id=\"mainHr\"><hr></div>");
		echo("<p style=\"text-align: center;\">");

		$Query = ("select count(name) as total from " . TABLE_PREFIX . "session where status!='disabled'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		$count = 0;
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_total_users = "{$row["total"]}";
		}
		$Query = ("select count(*) as total from " . TABLE_PREFIX . "favourites");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		$count = 0;
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_total_bookmarks = "{$row["total"]}";
		}
		$usersStr = T_ngettext('user', 'users', $rec_total_users);
		$thereStr = T_ngettext('There is', 'There are', $rec_total_users);
		echo("$thereStr <b>" . $rec_total_users . " $usersStr</b> " . sprintf(T_("registered on %s"),WEBSITE_NAME) . ".\n");
		echo("<br>" . sprintf(T_("There are <b>%s bookmarks</b> stored on %s"),$rec_total_bookmarks, WEBSITE_NAME) . ".</p>\n");

		if(IS_GETBOO)
		{
			// Paypal button
			$pAlign = "center";
			include('paypal.php');
		}
	}
?>
</div>
</body>
</html>