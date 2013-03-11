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

	/* Page used for the tags
	 *	Started on 26.01.06
	 * TODO
	 * Do a search through public books if time
	 */

	require_once('includes/user.php');
	$user = new User();
	$username = $user->getUsername();

	$tagName = "";
	if (isset($_GET['tag']))
	{
	    $tagName = $_GET['tag'];
	}

	include('conn.php');
	require_once('includes/protection.php');
	include('includes/tags_functions.php');

	$tagName = filter($tagName);
	$tagName = strtolower($tagName);

	$exists = true;

	$tagNames = explode(' ', trim($tagName));

	$tagcount = count($tagNames);
	for ($i = 0; $i < $tagcount; $i ++)
	{
		$tagNames[$i] = trim($tagNames[$i]);
	}

	foreach($tagNames as $currentTag)
	{
		$exists = tagExists($currentTag);
	}

	if($tagName != null)
	{
		//Feed to display in header
		$feedToDisplay = array();
		$feedToDisplay['type'] = "tags";
		$feedToDisplay['value'][0] = $tagName;

		//Display bookmarks
		$tagTitle = str_replace(' ', ' + ', $tagName);
		$customTitle = "Tags: " . $tagTitle;
		include('header.php');
		$tagTitle = str_replace(' + ', '+', $tagTitle);
		echo("<h2>" . T_("Tags") . " -- <span id=\"crumb\">" . $tagTitle . "</span><script type=\"text/javascript\">if(window.Crumb) Crumb.go('tags.php?tag=')</script></h2>");

		$pageUrl = "tags.php?tag=" . $tagName;
		include('includes/pagenb.php');

		$user = new User();
		$username = $user->getUsername();

		if (USECACHE) {
			require_once('includes/cache.php');
			$cache =& Cache::getInstance(CACHE_DIR);
			// Generate hash for caching on
			$hashtext = $_SERVER['REQUEST_URI'];
			// Check for page nb
			$hashtext .= ":pageNb:" . $_SESSION['perpagenb'];
			if ($user->isLoggedIn()) {
				$hashtext .= $user->getUsername();
			}
			$hash = md5($hashtext);

			// Cache for 15 minutes
			$cache->Start($hash, 900);
		}

		$anyBooks = false;
		//$exists = tagExists($tagName);
		$countBookmarks = 0;

		if($exists)
		{
			$bookmarks = getTagsBookmarks($tagNames, $minTagsNb, $maxTagsNb);

			//Display the bookmarks
			$displayUser = true;
			$displayDivs = true;
			include('templates/publicb.tpl.php');
		}
		if(!$anyBooks)
			echo("<p class=\"notice\">No bookmarks available</p>");
		else
		{
			//TODO: Need to tweak that. We shouldn't query just to know that!
			$bookmarkToCome = getTagsBookmarks($tagNames, ($minTagsNb + $maxTagsNb), 1);

			$moreBooks = count($bookmarkToCome) != 0;

			echo("<p class=\"paging\">");
			if($pageNb > 1)
				echo("<a accesskey=\"p\" href=\"tags.php?tag=" . $tagName . "&amp;page=" . ($pageNb - 1) . "\">" . T_("Previous") . "</a><span> | </span>");
			else
				echo("<span class=\"disable\">" . T_("Previous") . "</span><span> | </span>");
			if($moreBooks)
				echo("<a accesskey=\"n\" href=\"tags.php?tag=" . $tagName . "&amp;page=" . ($pageNb + 1) . "\">" . T_("Next") . "</a>");
			else
				echo("<span class=\"disable\">" . T_("Next") . "</span>");
			echo($displayPageStr . "</p>");
			echo("<p><a href=\"rss/tags.php?tag=" . $tagName . "\"><img src=\"images/firefox-rss-icon.png\" alt=\"" . T_("RSS icon") . "\" title=\"" . T_("RSS icon") . "\" width=\"15\" height=\"15\"></a> " . T_("feed for this page") . "</p>");
			echo("</div>\n");
			echo("</div>\n");
			$userName = "";
			//Set the blocks to display
			$blocks = array('related', 'popular');
			include('tags_rightmenu.php');
		}
		include('publicfooter.php');
		if (USECACHE) {
		    // Cache output if existing copy has expired
		    $cache->End($hash);
		}
	}
	else
	{
		header('Location: index.php');
	}
?>