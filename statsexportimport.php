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
//TODO Remove table format here also
include('header.php'); ?>
<?php
	include('access.php');
	$access = checkAccess();
	if($access)
	{
		include('sbheader.php');
		echo("<b>" . T_("Stats") . " -- " . T_("Import/Export") . "</b><br>\n");
		echo("<tr><td><br><tr><td>\n");
		$Query = ("select count(1) as count from " . TABLE_PREFIX . "bookexportimport where name='" . $username . "' having count(1) >0");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$resultsFound = true;
			$countb = "{$row["count"]}";
			echo("- $countb " . T_("imports/exports") . "<br>\n");
		}

		$Query = ("select count(1) as count from " . TABLE_PREFIX . "bookexportimport where name='" . $username . "' and method LIKE 'I%' having count(1) >0");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$resultsFound = true;
			$countb = "{$row["count"]}";
			echo("<tr><td>- $countb " . T_("imports") . "<br>\n");
		}

		$Query = ("select count(1) as count from " . TABLE_PREFIX . "bookexportimport where name='" . $username . "' and method = 'EX' having count(1) >0");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$resultsFound = true;
			$countb = "{$row["count"]}";
			echo("<tr><td>- $countb " . T_("exports") . "<br>\n");
		}

		require_once('includes/convert_date.php');
		
		if($resultsFound)
		{
	
			$Query = ("select max(Time) AS formatted_time from " . TABLE_PREFIX . "bookexportimport where name='" . $username . "' and method LIKE 'I%'");
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$date = ("{$row["formatted_time"]}");
				if($date != null)
				{
					$date2 = convert_date($date);
					echo("<tr><td>- " . T_("Your last import was made on") . " " . $date2. "<br>\n");
				}
			}
	
			$Query = ("select max(Time) AS formatted_time from " . TABLE_PREFIX . "bookexportimport where name='" . $username . "' and method = 'EX'");
			//echo($Query . "<br>\n");
			$dbResult = $dblink->query($Query);
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$date = ("{$row["formatted_time"]}");
				if($date != null)
				{
					$date2 = convert_date($date);
					echo("<tr><td>- " . T_("Your last export was made on") . " " . $date2. "<br>\n");
				}
			}
		}
		echo("</table>");
		if(!$resultsFound)
			echo('<p class="notice">' . T_("No data") . '</p>');
		echo("</div>");
		
	}
?>
<?php include('footer.php'); ?>