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

	require_once("includes/comment_functions.php");
	require_once('includes/user.php');
	define("MAX_TITLE_LENGTH", 70);
	if($bookmarks != null)
	{
		foreach($bookmarks as $row)
		{
			if(!$anyBooks)
			{
				$anyBooks = true;
				require_once('includes/convert_date.php');
				if($displayDivs)
				{
					echo("<div class=\"tags_content\">\n");
					echo("<div class=\"inner\">\n");
					include('includes/searchform.php');
				}
			}
			$rec_date = "{$row["formatted_time"]}";
			$time_between = get_formatted_timediff($rec_date);
			$date_added = convert_date_tags($rec_date);
			$rec_id = "{$row["id"]}";
			$rec_title = "{$row["title"]}";
			$rec_url = "{$row["url"]}";
			$rec_desc = "{$row["description"]}";
			
			// Strip title if too long!
			if(strlen($rec_title)>MAX_TITLE_LENGTH)
				$rec_title = substr($rec_title, 0, MAX_TITLE_LENGTH) . "..";
			if($current_page != "")
				$allTagsLinks = returnAllTagsLinks($rec_id, $current_page);
			else
				$allTagsLinks = returnAllTagsLinks($rec_id);
	
			//no follow so that search engines don't increment the count
			echo("<div class=\"tagtitle\"><a href=\"redirect.php?id=" . $rec_id . "\" rel=\"nofollow\">". $rec_title . "</a></div>\n");
			if(USE_SCREENSHOT && SCREENSHOT_URL)
			{
				//Convert all ampersands to &amp;
				$screenshot_url = str_replace("&","&amp;", SCREENSHOT_URL);
				echo("<div style=\"min-height: 80px\"><a href=\"redirect.php?id=" . $rec_id . "\" rel=\"nofollow\">");
				echo(sprintf("<img width=\"120px\" height=\"90px\" alt=\"\" src=\"" . $screenshot_url . "\" style=\"padding-right: 10px; float: left;\"></a>",$rec_url));
			}
			else
			{
				$descCSS = " style=\"border: 1px dashed #ccc; border-width: 0 0 0 1px;\"";
				$footerCSS = " style=\"border: 1px dashed #ccc; border-width: 0 0 1px 1px;\"";
			}
			if($rec_desc != null) // If there is a description
				echo("<div class=\"tagdesc\"$descCSS>" . $rec_desc . "</div>\n");
			echo("<div class=\"tagdesc\"$descCSS><span class=\"tags\">" . $allTagsLinks . "</span>");
			//<img src=\"images/icons/tag_orange.png\">
			if($displayUser)
			{
				$userName = "{$row["name"]}";
				if(IS_GETBOO)
					$isDonor = User::isDonor($userName);
				if($isDonor)
					$donorLogo = " <img src=\"images/donor-mini.gif\" alt=\"" . T_("Donor") . "\" title=\"" . T_("Donor") . "\" />";
				else
					$donorLogo = "";
				echo(T_("by") . " <a href=\"userb.php?uname=" . $userName . "\">" . $userName . "$donorLogo</a> ");
			}
			echo("... ");
			if($time_between)
				echo(T_("added") . " " . $time_between);
			else
				echo(T_("on") . " " . $date_added);
			/*if($username == $userName)
				echo(" / Delete");*/
			echo("</div>\n");
	
			//Comments
			$nbOfComments = getNbOfComments($rec_id);
			echo("<div class=\"tagfooter\"$footerCSS><a href=\"comment.php?bID=" . $rec_id . "\" ");
			if($nbOfComments == 0)
			{
				echo "class=\"tagCommentSingle\">" . T_("submit comment");
			}
			else
			{
				$resultsStr = T_ngettext('comment', 'comments', $nbOfComments);
				echo("class=\"tagCommentMultiple\">" . "$resultsStr(" . $nbOfComments . ")");
			}
			echo("</a></div>\n");
			if(USE_SCREENSHOT && SCREENSHOT_URL)
				echo("</div>\n");
	
			echo("<br>\n");
		}
	}
?>
