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

	$SETTINGS['path_mod'] = "../";
	include('../conn.php');
	header('Content-Type: application/xml');

	$writeStr = ("<?xml version=\"1.0\" ?>\n");

	$writeStr .= ("<rss version=\"2.0\">\n");

	$writeStr .= ("<channel>\n");
	$writeStr .= ("<title>" . WEBSITE_NAME . " News</title>\n");
	$writeStr .= ("<description>News for " . WEBSITE_NAME . "!</description>\n");
	$writeStr .= ("<link>" . WEBSITE_ROOT . "</link>\n");
	$writeStr .= ("<language>" . WEBSITE_LOCALE . "</language>\n");

	$builddate = date("D, d M Y H:i:s O");

	$writeStr .= ("<lastBuildDate>$builddate</lastBuildDate>\n\n");

	include('../includes/convert_date.php');

	$Query = ("select newsID, date as formatted_time, author, title, date, msg from " . TABLE_PREFIX . "news order by formatted_time DESC");
	//echo($Query . "<br>\n");
	$dbResult = $dblink->query($Query);
	include('../includes/protection.php');
	while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	{
		$date = ("{$row["formatted_time"]}");
		$date2 = convert_date_feed($date);
		$writeStr .= ("<item>\n");
		$writeStr .= ("<pubDate>$date2</pubDate>\n");
		$writeStr .= ("<title>{$row["title"]}</title>\n");
		$desc = "{$row["msg"]}";
		remhtml($desc);
		$writeStr .= ("<description>$desc</description>\n");
		$writeStr .= ("<link>" . WEBSITE_ROOT . "newsdetails.php?id={$row["newsid"]}&amp;src=f</link>\n");
		$writeStr .= ("<guid>" . WEBSITE_ROOT . "newsdetails.php?id={$row["newsid"]}&amp;src=f</guid>\n");
		$writeStr .= ("</item>\n");
	}

	$writeStr .= ("</channel>\n");
	$writeStr .= ("</rss>\n");
	echo($writeStr);
?>