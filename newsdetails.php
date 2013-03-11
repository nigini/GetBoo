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
//TODO: Could be merged with news.php page, and check if we have some specific news (id) to display
include('header.php'); ?>
<?php
$id = 0;
if (isset($_GET['id']))
{
    $id = $_GET['id'];
}
$src = 0;
if (isset($_GET['src']))
{
    $src = $_GET['src'];
}
	include('conn.php');
	include('includes/protection.php');
?>
<h2><?php echo T_("News");?></h2>
<div class="news_content">
<?php
	if(valid($id, 4) && ($src=='m' || $src=='f'))
	{
		$Query = ("select date as formatted_time, author, title, date, msg from " . TABLE_PREFIX . "news where newsid = $id order by formatted_time DESC");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$date1 = ("{$row["formatted_time"]}");
			include('includes/convert_date.php');
			$date2 = convert_date($date1);
			echo("<div class=\"msgtitle\">{$row["title"]}</div>\n");
			echo("<div class=\"msgsubtitleL\">" . T_("Author") . ": <b>{$row["author"]}</b></div><div class=\"msgsubtitleR\">" . T_("Date") . ": <b>$date2</b></div>\n");
			echo("<div class=\"msgbody\">{$row["msg"]}</div>\n");
			// Count news hits
			$Query = "
			INSERT INTO " . TABLE_PREFIX . "newshits
			(NewsID, Source, Time, IP)
			VALUES
			('$id', '$src', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "')
			";

	 		$dbResult = $dblink->query($Query);
		}
		else
		{
			echo("<p class=\"error\">" . T_("The news can't be found") . ".</p>");
		}

	}
	else
	{
		echo("<p class=\"error\">" . T_("Invalid URL values.") . "</p>");
	}
	echo("</div>");
?>
<p style="text-align: center">
<a href="news.php"><< <?php echo T_("News");?> ...</a>
</p>
<?php include('footer.php'); ?>