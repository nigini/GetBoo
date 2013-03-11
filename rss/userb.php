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

	header('Content-Type: application/xml');
	$SETTINGS['path_mod'] = "../";
	include('../conn.php');
	include('../includes/tags_functions.php');
	include('../includes/protection.php');
	include('rss_functions.php');

	$userName = "";
	if (isset($_GET['uname']))
	{
	    $userName = $_GET['uname'];
	}
	remhtml($userName);

	$tagName = "";
	if (isset($_GET['tag']))
	{
	    $tagName = $_GET['tag'];
	}

	//If there is any tags for the user
	$tagName = filter($tagName);
	//echo("Tag Name:" . $tagName . ":");
	$tagName = strtolower($tagName);

	//$tagNames = split("+", $tagName, 8);
	$tagNames = explode(' ', trim($tagName));

	$tagcount = count($tagNames);
	for ($i = 0; $i < $tagcount; $i ++)
	{
		$tagNames[$i] = trim($tagNames[$i]);
	}

	if($tagName != "")
		$bookmarks_user = getTagsBookmarks($tagNames, 0, TAGS_PER_PAGE, $userName, "../");
	else
		$bookmarks_user = getUserBookmarks($userName, 0, TAGS_PER_PAGE, "../");

	$bookmarks = getFeedVars($bookmarks_user);

	$strPageQuery = ("?uname=" . $userName);
	if($tagName != "")
		$strPageQuery .= ("&amp;tag=" . $tagName);

	$feedTitle = WEBSITE_NAME;
	$feedLink = WEBSITE_ROOT . "userb.php" . $strPageQuery;
	$feedDesc = "Public bookmarks for " . $userName;
	if($tagName != "")
		$feedDesc .= ": " . $tagName;
	$feedTTL = "60";

	include('../templates/rss.tpl.php');
?>