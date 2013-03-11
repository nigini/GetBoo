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
		echo("<b>" . T_("Stats") . " -- " . T_("Most used") . "</b><br><br>\n");

		$Query = ("select id, title, description, bookmarkid, count(*) as count from " . TABLE_PREFIX . "bookmarkhits b, " . TABLE_PREFIX . "favourites f where b.bookmarkid = f.id and f.name='$username' group by bookmarkid, id, title, description order by Count desc");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		echo("<table class=\"bookmarks\">");
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$resultsFound = true;
			$idurl = "{$row["id"]}";
			echo("<tr><td><img src=\"images/style/$style/bookmark.GIF\" alt=\"" . T_("Bookmark") . "\" width=\"16\" height=\"16\" /><a href=\"redirect.php?id=$idurl\" target=\"blank\">{$row["title"]}</a></td><td>{$row["description"]}</td><td>{$row["count"]}</td></tr>\n");
		}

		echo("</table>");
		if(!$resultsFound)
			echo('<p class="notice">' . T_("No data") . '</p>');
		echo("</div>");
		
	}
?>
<?php include('footer.php'); ?>