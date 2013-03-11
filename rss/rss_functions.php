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
	function getFeedVars($bookmarksToParse)
	{
		include('../includes/convert_date.php');
		$bookmarks = array();
		$count = 0;
		foreach($bookmarksToParse as $row)
		{
			$rec_id = "{$row["id"]}";
			$rec_date = "{$row["formatted_time"]}";
			$bookmarks[$count]['bTime'] = convert_date_feed($rec_date);
			$bookmarks[$count]['bTitle'] = "{$row["title"]}";
			$bookmarks[$count]['bUrl'] = WEBSITE_ROOT . "redirect.php?id=$rec_id";
			$bookmarks[$count]['bDesc'] = "{$row["description"]}";
			$bookmarks[$count]['bCreator'] = "{$row["name"]}";
			$bookmarks[$count++]['allTagsArray'] = returnAllTagsArray($rec_id, "../");
		}
		return $bookmarks;
	}
?>