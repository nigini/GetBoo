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

	function addComment($bID, $title, $comment, $author, $parentID = 0)
	{
		include('conn.php');
		$Query = "INSERT INTO " . TABLE_PREFIX . "comments ( BID , Title , Comment , Author , Date , ParentID ) values('" . $bID . "','" . $title . "','" . $comment . "','" . $author . "', now(), '" . $parentID . "')";
		//echo($Query . "<br>\n");
		$AffectedRows = $dblink->exec($Query);
		
		return ($AffectedRows == 1);
	}

	function getComments($bookmarkID)
	{
		include('conn.php');
		$Query = ("select title, comment, author, date from " . TABLE_PREFIX . "comments where bid='$bookmarkID'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		$comments = array();
		$count = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$comments[$count++] = $row;
		}

		
		return $comments;
	}

	function getNbOfComments($bookmarkID)
	{
		include('conn.php');
		$Query = ("select title from " . TABLE_PREFIX . "comments where bid='$bookmarkID'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		$count = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$count++;
		}

		
		return $count;
	}

	function displayComments($comments)
	{
		$strResult = "";
		if($comments != null)
		{
			foreach($comments as $current_row)
			{
				$strResult .= "<div class=\"commentDetails\"><div class=\"commentTitle\">" . $current_row['title'] . "</div><div class=\"commentBody\">" . $current_row['comment'] . "</div><div class=\"commentInfo\">by <a href=\"userb.php?uname=" . $current_row['author'] . "\">" . $current_row['author'] . "</a> on " . $current_row['date'] . "</div></div>\n";
			}
		}
		return $strResult;
	}
?>