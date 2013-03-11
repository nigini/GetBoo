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
	$feedToDisplay['type'] = "recent";
	require_once('header.php');

	/* Page used to display the recent tags
	 *	Started on 16.02.06
	 * TODO
	 */

	$user = new User();
	$username = $user->getUsername();

	include('conn.php');
	require_once('includes/protection.php');
	include('includes/tags_functions.php');

	$pageUrl = "recent_tags.php";
	include('includes/pagenb.php');

	if(!$boolMain)
		echo("<h2>" . T_("Recent Tags") . "</h2>");

	echo("<div class=\"tags_content\">");
	echo("<div class=\"inner\">");

	//If not on the main page since it is already included
	if(!$boolMain)
		include('includes/searchform.php');
	else
		echo("<h2>" . T_("Recent Tags") . "</h2>");

	if (USECACHE) {
		require_once('includes/cache.php');
		$cache =& Cache::getInstance(CACHE_DIR);
		// Generate hash for caching on
		$hashtext = $_SERVER['REQUEST_URI'];
		// Check for page nb
		$hashtext .= ":pageNb:" . $_SESSION['perpagenb'];
		// Check logged in user
		if ($user->isLoggedIn()) {
			$hashtext .= $user->getUsername();
		}
		$hash = md5($hashtext);

		// Cache for 15 minutes
		$cache->Start($hash, 900);
	}

	$anyBooks = false;

	$countBookmarks = numberOfPublicBookmarks();

	$bookmarks = getRecentTags($minTagsNb, $maxTagsNb);

	//Display the bookmarks
	$displayUser = true;
	include('templates/publicb.tpl.php');

	if(!$anyBooks)
		echo("<p class=\"notice\">" . T_("No bookmarks available") . ".</p>");
	else
	{
		echo("<p class=\"paging\">");
		if($pageNb > 1)
			echo("<a accesskey=\"p\" href=\"recent_tags.php?page=" . ($pageNb - 1) . "\">" . T_("Previous") . "</a><span> | </span>");
		else
			echo("<span class=\"disable\">" . T_("Previous") . "</span><span> | </span>");
		if((($pageNb * $perPageNb) < $countBookmarks) && $pageNb < MAXIMUM_PAGES_RECENT_TAGS)
			echo("<a accesskey=\"n\" href=\"recent_tags.php?page=" . ($pageNb + 1) . "\">" . T_("Next") . "</a>");
		else
			echo("<span class=\"disable\">" . T_("Next") . "</span>");
		echo($displayPageStr . "</p>");
		echo("<p><a href=\"rss/recent.php\"><img src=\"images/firefox-rss-icon.png\" alt=\"" . T_("RSS icon") . "\" title=\"" . T_("RSS icon") . "\" width=\"15\" height=\"15\"></a> " . T_("feed for this page") . "</p>");
	}

	echo("</div>\n");
	echo("</div>\n");
	$userName = "";
	//Set the blocks to display
	$blocks = array('popular');
	include('tags_rightmenu.php');
?>
<?php require_once('publicfooter.php');
	if (USECACHE) {
	    // Cache output if existing copy has expired
	    $cache->End($hash);
}
?>