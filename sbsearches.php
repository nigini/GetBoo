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
		include('sbheader.php');
		include('includes/convert_date.php');
		echo("<b>" . T_("Stats") . " -- " . T_("Searches") . "</b><br><br>\n");

		$Query = ("select keyword, count(*) as Count, max(time) as Time from " . TABLE_PREFIX . "searches s where s.name='$username' group by keyword order by Count desc");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		$count = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			if($count == 0)
				echo("<table><tr><td><u>" . T_("Keyword") . "</u></td><td><u>" . T_("Count") . "</u></td><td><u>" . T_("Last Search Date") . "</u></td></tr>\n");
			$keyword = "{$row["keyword"]}";
			$count = "{$row["count"]}";
			$time = "{$row["time"]}";
			$time2 = convert_date($time);
			echo("<tr><td>$keyword</td><td>$count</td><td>$time2</td></tr>\n");
			$count++;
		}

		echo("</table>");
		if($count == 0)
			echo('<p class="notice">' . T_("No searches") . '</p>');
		echo("</div>");
		
	}
?>
<?php include('footer.php'); ?>