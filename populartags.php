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
include('header.php'); ?>
<?
	include('conn.php');
	include('includes/tags_functions.php');

	$sortOrder = "";
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

	//Display the popular tags
	echo("<h2>" . T_("Popular tags") . "");
	if($userName)
		echo(" -- " . $userName);
	echo("</h2>");

	if($current_page != "")
		$strPopular = displayPopularTagsCloud(tagCloud(getPopularTags(150, $userName), 5, 90, 225, $sortOrder), $current_page);
	else
		$strPopular = displayPopularTagsCloud(tagCloud(getPopularTags(150, $userName), 5, 90, 225, $sortOrder));

	if($strPopular != "")
		echo("<p class=\"tags\">" . $strPopular . "</p>");
?>
<p id="sort">
 <?php echo T_("Sort by");?>:    <a href="?sortOrder=alphabet<?php echo $userStr;?>"><?php echo T_("Alphabet");?></a><span> / </span>

 <a href="?sort=popularity<?php echo $userStr;?>"><?php echo T_("Popularity");?></a>
</p>
<?php include('footer.php'); ?>