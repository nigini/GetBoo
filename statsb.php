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
// TODO: Remove the table format
include('header.php'); ?>
<?php
	include('access.php');
	$access = checkAccess();
	if($access)
	{
		include('sbheader.php');
		echo("<b>" . T_("Stats") . " -- " . T_("General") . "</b><br>\n");
		echo("<br>\n");
		$Query = ("select count(1) as count from " . TABLE_PREFIX . "favourites f where f.name='$username' having count(1) >0");
		$dbResult = $dblink->query($Query);
		$row = $dbResult->fetchRow();
		if($row['count'] > 0)
		{
			$resultsFound = true;
			echo("- " .$row['count'] . " " . T_("bookmarks") . "<br>\n");
		}

		$Query = ("select count(1) as count from " . TABLE_PREFIX . "folders f where f.name='$username' having count(1) >0");
		$dbResult = $dblink->query($Query);
		$row = $dbResult->fetchRow();
		if($row['count'] > 0)
		{
			$resultsFound = true;
			echo("- " .$row['count'] . " " . T_("folders") . "<br>\n");
		}

		$Query = ("select count(1) as count from " . TABLE_PREFIX . "bookmarkhits b, " . TABLE_PREFIX . "favourites f where b.bookmarkid = f.id and f.name='$username' and b.name='$username' having count(1) >0");
		$dbResult = $dblink->query($Query);
		$row = $dbResult->fetchRow();
		if($row['count'] > 0)
		{
			$resultsFound = true;
			echo("- " .$row['count'] . " " . T_("bookmarks accessed") . "<br>\n");
		}

		require_once('includes/convert_date.php');

		$Query = ("SELECT max(Time) AS formatted_time from " . TABLE_PREFIX . "bookmarkhits where name='" . $username ."'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$date = ("{$row["formatted_time"]}");
			if($date != null)
			{
				$resultsFound = true;
				$date2 = convert_date($date);
				echo("- " . T_("Your last accessed bookmark was on") . " " . $date2. "<br>\n");
			}
		}

		$Query = ("SELECT min(ADD_DATE) AS formatted_time, max(ADD_DATE) AS formatted_time2 from " . TABLE_PREFIX . "favourites where name='" . $username ."'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$date = ("{$row["formatted_time"]}");
			if($date != null)
			{
				$resultsFound = true;
				$date2 = convert_date($date);
				echo("- " . T_("Your first bookmark was added on") . " " . $date2 . "<br>");
			}
			$date3 = ("{$row["formatted_time2"]}");
			if($date3 != null)
			{
				$resultsFound = true;
				$date4 = convert_date($date3);
				echo("- " . T_("Your last bookmark was added on") . " " . $date4. "<br>\n");
			}
		}
		if(!$resultsFound)
			echo('<p class="notice">' . T_("No data") . '</p>');
		echo("</div>");
		
	}
?>
<?php include('footer.php'); ?>