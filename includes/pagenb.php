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

	$pageNb = "";
	if (isset($_GET['page']))
	{
	    $pageNb = $_GET['page'];
	}
	else
		$pageNb = "1";
	remhtml($pageNb);

	$displayNb = "";
	if (isset($_GET['display']))
	{
	    $displayNb = $_GET['display'];
	}
	remhtml($displayNb);

	if($displayNb)
		$_SESSION['perpagenb'] = $displayNb;

	$perPageNb = "10";
	if($_SESSION['perpagenb'])
		$perPageNb = $_SESSION['perpagenb'];
	else
		$perPageNb = TAGS_PER_PAGE;
	$minTagsNb = ($pageNb - 1) * $perPageNb;
	$maxTagsNb = $perPageNb;

	$contaisQuery = strpos($pageUrl, "?");
	if(!$contaisQuery === false)
		$displayUrl = "&amp;display=";
	else
		$displayUrl = "?display=";

	$displayPageStr = " " . T_("Displaying") . " ";
	$nbArray = array('10', '20', '30', '40', '50');
	foreach ($nbArray as $current_nb)
	{
		if($current_nb != $perPageNb)
			$displayPageStr .= (" <a href=\"" . $pageUrl . $displayUrl . $current_nb . "\">" . $current_nb . "</a>");
		else
			$displayPageStr .= (" <span class=\"disable\">" . $current_nb . "</span>");
	}
	$displayPageStr .= " " . T_("per page");
?>