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

	/* Page used for searching through public bookmarks
	 *	Started on 17.04.06
	 * TODO
	 */

	$pageNb = "";
	if (isset($_GET['page']))
	{
	    $pageNb = $_GET['page'];
	}
	else
		$pageNb = "1";


	if (isset($_GET['keywords']))
	{
		 $keywords = $_GET['keywords'];
	}

	$keywords_original = $keywords;

	if($keywords != null)
	{
		$keywords = trim($keywords);
		$keywords = preg_replace("/ +/", " ", $keywords);
	}

	include('conn.php');
	include('includes/protection.php');
	include('includes/tags_functions.php');

	remhtml($pageNb);
	$keywords = filter($keywords);

	$minTagsNb = ($pageNb - 1) * TAGS_PER_PAGE;
	$maxTagsNb = TAGS_PER_PAGE;


	if($keywords != null)
	{
		include('header.php');
		echo("<h2>" . T_("Search") . " -- " . $keywords . "</h2>");
		$anyBooks = false;

		$countBookmarks = 0;

		//$words = split(" ", $keywords, 8);
		$bookmarks = getSearchBookmarks($keywords, $minTagsNb, $maxTagsNb);

		//Display the bookmarks
		$displayUser = true;
		$displayDivs = true;
		include('templates/publicb.tpl.php');

		if(!$anyBooks)
		{
			echo("<div class=\"tags_content\">");
			echo("<div class=\"inner\">");
			include('includes/searchform.php');
			echo("<p class=\"notice\">" . T_("No bookmarks available") . ".</p>");
		}
		else
		{
			//Check if more results to come
			if(count($bookmarks) < TAGS_PER_PAGE)
				$moreBooks = false;
			else
			{
				$bookmarkToCome = getSearchBookmarks($keywords, ($minTagsNb + $maxTagsNb), 1);
	
				$moreBooks = count($bookmarkToCome) != 0;
			}

			echo("<p class=\"paging\">");
			if($pageNb > 1)
				echo("<a accesskey=\"p\" href=\"psearch.php?keywords=" . $keywords_original . "&amp;page=" . ($pageNb - 1) . "\">" . T_("Previous") . "</a><span> | </span>");
			else
				echo("<span class=\"disable\">" . T_("Previous") . "</span><span> | </span>");
			if($moreBooks)
				echo("<a accesskey=\"n\" href=\"psearch.php?keywords=" . $keywords_original . "&amp;page=" . ($pageNb + 1) . "\">" . T_("Next") . "</a>");
			else
				echo("<span class=\"disable\">" . T_("Next") . "</span>");
			echo("</p>");
		}
		echo("</div>\n");
		echo("</div>\n");
		$userName = "";
		//Set the blocks to display
		$blocks = array('popular');
		include('tags_rightmenu.php');
		include('footer.php');
	}
	else
	{
		header('Location: index.php');
	}
?>
