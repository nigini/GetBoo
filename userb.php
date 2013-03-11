<?php
/***************************************************************************
Copyright (C) 2006 Scuttle project
http://sourceforge.net/projects/scuttle/
http://scuttle.org/

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
	/* Page used for the tags, to display user's public bookmarks
	 *	Started on 27.01.06
	 * TODO
	 */
	session_start();
	$userName = "";
	if (isset($_GET['uname']))
	{
	    $userName = $_GET['uname'];
	}

	include('conn.php');
	require_once('includes/protection.php');
	include('includes/tags_functions.php');

	remhtml($userName);

	$userNotFound = false;

	$tagName = "";
	if (isset($_GET['tag']))
	{
	    $tagName = $_GET['tag'];
	}

	//If there is any tags for the user
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
	
	require_once('includes/user.php');
	$user = new User();
	$currentUsername = $user->getUsername();
	$pageUrl = "userb.php?uname=" . $userName . "&amp;tag=" . $tagName;
	include('includes/pagenb.php');
	
	if(!$userName && $currentUsername != null)
	{
		$userName = $currentUsername;
	}

	$strPageQuery = ("?uname=" . $userName);
	if($tagName != "")
		$strPageQuery .= ("&amp;tag=" . $tagName);
	//Feed to display in header
	$feedToDisplay = array();
	$feedToDisplay['type'] = "user";
	$feedToDisplay['value'][0] = $strPageQuery;
	$feedToDisplay['value'][1] = $userName;
	$feedToDisplay['value'][2] = ": " . $tagName;

	$tagTitle = str_replace(' ', ' + ', $tagName);
	if($userName)
		$customTitle = $userName;
	if($tagTitle)
		$customTitle .= ": " . $tagTitle;

	include('header.php');

	$endcache = false;
	if (USECACHE) {
		require_once('includes/cache.php');
		$cache =& Cache::getInstance(CACHE_DIR);
		// Generate hash for caching on
		$hashtext = $_SERVER['REQUEST_URI'];
		// Check for page nb
		$hashtext .= ":pageNb:" . $_SESSION['perpagenb'];
		// Generate hash for caching on
		$hash = md5($hashtext);
		if ($user->isLoggedIn()) {
			if ($currentUsername != $userName) {
	         // Cache for 15 minutes
	         $cache->Start($hash, 900);
	         $endcache = true;
     		}
		 } else {
		     // Cache for 30 minutes
		     $cache->Start($hash, 1800);
		     $endcache = true;
		 }
	}

	echo("<h2>");
	
	if($userName != null)
	{
		//Display bookmarks

		$anyBooks = false;
		// Get the "real name" (case sensitive)
		$Query = ("select name, status from " . TABLE_PREFIX . "session where (name='" . $userName . "')");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$rec_name = "{$row["name"]}";
			$rec_status = "{$row["status"]}";
			$userName = $rec_name;
			$exists = true;
		}
		else
			$exists = false;

		echo($userName);
		if($tagTitle != "")
			echo(" -- <span id=\"crumb\">" . $tagTitle . "</span><script type=\"text/javascript\">if(window.Crumb) Crumb.go('userb.php?uname=" . $userName . "&tag=')</script>");
		echo("</h2>");

		if($exists) // If the user exists
		{
			if($rec_status != "disabled")
			{
				$moreBooks = false;
				if($tagName != "")
				{
					$bookmarks = getTagsBookmarks($tagNames, $minTagsNb, $maxTagsNb, $userName);
					$bookmarkToCome = getTagsBookmarks($tagNames, ($minTagsNb + $maxTagsNb), 1, $userName);
					$moreBooks = count($bookmarkToCome) != 0;
				}
				else
				{
					$bookmarks = getUserBookmarks($userName, $minTagsNb, $maxTagsNb);
					$countBookmarks = numberOfPublicBookmarksUser($userName);
					$moreBooks = ($pageNb * $perPageNb) < $countBookmarks;
				}
	
				$current_page = ("userb.php?uname=" . $userName . "&amp;tag=");
				//Display the bookmarks
				$displayUser = false;
				$displayDivs = true;
				include('templates/publicb.tpl.php');
			}
			else
			{
				echo("<p class=\"notice\">" . sprintf(T_("User %s's account is disabled"),$userName) . ".</p>");
				$userNotFound = true;
			}
		}
		else
		{
			echo("<p class=\"error\">" . sprintf(T_("User %s was not found"),$userName) . ".</p>");
			$userNotFound = true;
		}
		if(!$anyBooks && $exists && $rec_status != "disabled")
		{
			if(empty($tagName))
				echo("<p class=\"notice\">" . T_("No bookmarks are public with this user") . "</p>");
			else
				echo("<p class=\"notice\">" . T_("No bookmarks with these tags for this user") . "</p>");
		}
			
		else if(!$userNotFound)
		{
			if($tagName != "")
					$tagsQuery = "&amp;tag=" . $tagName;

			echo("<p class=\"paging\">");
			if($pageNb > 1)
				echo("<a accesskey=\"p\" href=\"userb.php?uname=" . $userName . "&amp;page=" . ($pageNb - 1) . $tagsQuery . "\">" . T_("Previous") . "</a><span> | </span>");
			else
				echo("<span class=\"disable\">" . T_("Previous") . "</span><span> | </span>");
			if($moreBooks)
				echo("<a accesskey=\"n\" href=\"userb.php?uname=" . $userName . "&amp;page=" . ($pageNb + 1) . $tagsQuery . "\">" . T_("Next") . "</a>");
			else
				echo("<span class=\"disable\">" . T_("Next") . "</span>");
			echo($displayPageStr . "</p>");
			echo("<p><a href=\"rss/userb.php" . $strPageQuery . "\"><img src=\"images/firefox-rss-icon.png\" alt=\"" . T_("RSS icon") . "\" title=\"" . T_("RSS icon") . "\" width=\"15\" height=\"15\"></a> " . T_("feed for this page") . "</p>");
			echo("</div>\n");
			echo("</div>\n");
			//Set the blocks to display
			$blocks = array('profile', 'popular');
			if($tagName != "")
				array_push($blocks, 'related');
			//print_r($blocks);
			include('tags_rightmenu.php');
		}
	}
	else
	{
		echo("" . T_("Public Bookmarks") . "</h2><p class=\"error\">" . T_("No username to display bookmarks") . "</p>");
	}
	include('publicfooter.php');
	if (USECACHE && $endcache) {
	    // Cache output if existing copy has expired
	    $cache->End($hash);
	}
?>