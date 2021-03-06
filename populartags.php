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
//TODO: Should we do caching on this page
include('header.php');
echo("<h2>" . T_("Tags") . " -- <span id=\"crumb\">" . $tagTitle . "</span>
<script type=\"text/javascript\">if(window.Crumb) Crumb.go('tags.php?tag=')</script></h2>");
?>
<?
	include('conn.php');
	include('includes/tags_functions.php');

	$sortOrder = "alphabet";
	if (isset($_GET['sortOrder']))
	{
	    $sortOrder = $_GET['sortOrder'];
	}
	include('includes/protection.php');
	remhtml($sortOrder);

	$userName = "";
	if (isset($_GET['uname']))
	{
	    $userName = $_GET['uname'];
	}
	remhtml($userName);
	if($userName != "")
	{
		$userStr = "&amp;uname=" . $userName;
		$current_page = "userb.php?uname=" . $userName . "&amp;tag=";
	}

	if($current_page != "")
		$strPopular = displayPopularTagsCloud(tagCloud(getPopularTags(150, $userName), 5, 90, 225, $sortOrder), $current_page);
	else
		$strPopular = displayTagsCloud(tagCloud(getPopularTags(150, $userName), 5, 90, 225, $sortOrder));

	if($strPopular != "")
		echo("<p class=\"tags\">" . $strPopular . "</p>");
?>
<?php include('footer.php'); ?>
