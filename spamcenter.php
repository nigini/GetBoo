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

		echo("<h2>" . T_("Settings") . " -- " . T_("Spam Center") . "</h2>\n");

		$Query = ("select  distinct s.name as name, " . DATE_DIFF_SQL . " s.datejoin)/" . DAY_SECONDS . " as diff, count(*) as nbB, count(*)/" . DATE_DIFF_SQL . " s.datejoin)/" . DAY_SECONDS . " as average
						from " . TABLE_PREFIX . "favourites b, " . TABLE_PREFIX . "tags_added ta, " . TABLE_PREFIX . "session s
						where (id = ta.b_id and b.name = s.name and " . DATE_DIFF_SQL . " s.datejoin)/" . DAY_SECONDS . " < 60 and " . DATE_DIFF_SQL . " s.datejoin)/" . DAY_SECONDS . " >3 
						and s.status = 'normal' and " . DATE_DIFF_SQL . " b.add_date)/" . DAY_SECONDS . " <= 10)
						group by s.name, s.datejoin
						having count(*)/" . DATE_DIFF_SQL . " s.datejoin)/" . DAY_SECONDS . " >= 2
						order by average desc");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		$count = 0;
		include('includes/convert_date.php');
	  	while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	  	{
		  	if($count == 0)
		  	{
			  	echo("<div class=\"content\">\n");
		  		echo("<table class='sortable'>\n");
				echo("<thead><tr><th>" . T_("Select") . "</th><th>" . T_("Days") . "</th><th>" . T_("Bookmarks") . "</th><th>" . T_("Average") . "</th></tr></thead><tbody>");
	  		}
		  	$nameS = "{$row["name"]}";
			$nbDays = "{$row["diff"]}";
			$nbBookmarks = "{$row["nbb"]}";
			$average = "{$row["average"]}";

			echo("<tr>\n<td><a href=\"userb.php?uname=" . $nameS . "\">$nameS</a></td>\n<td>$nbDays</td>\n<td>$nbBookmarks</td>\n<td>$average</td>\n\n");
			$count++;
	  	}
		if($count == 0) // YAY No spam.. yet!
		{
			echo("<p class=\"notice\">" . T_("No users matching the SPAM criterias") . "</p><div class=\"content\">\n");
			echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to Settings") . "</a></p>");
		}
		else
			echo("</tbody></table>\n</div>");
	}
?>
<?php include('footer.php'); ?>