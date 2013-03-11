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

	echo("<h2>" . T_("Bookmarks") . "</h2>");
	if(isset($_SESSION['login_msg']) && $_SESSION['login_msg'] != "")
	{
		echo("<p class=\"success\">" . $_SESSION['login_msg'] . "</p>\n");
		$_SESSION['login_msg'] = null;
	}
	else if(isset($_SESSION['success_msg']) && $_SESSION['success_msg'] != "")
	{
		echo("<p class=\"success\">" . $_SESSION['success_msg'] . "</p>\n");
		$_SESSION['success_msg'] = null;
	}
	$user = new User();
	$username = $user->getUsername();
	$style = $user->getStyle();

	echo("<div class=\"content\"><table class=\"bookmarks\" style=\"width: 100%\">");
	echo("<tr><td class=\"bookmarkstitle\"><a href=\"books.php\">" . T_("Bookmarks") . "</a> | <a href=\"userb.php?uname=" . $username . "\">" . T_("Public") .
		 "</a> | <a href=\"search.php\">" . T_("Search") . "</a> | <a href=\"import.php\">" .
		  T_("Import") . "</a> | <a href=\"importDelicious.php\">" . T_("Import Delicious") . "</a> | <a href=\"export.php\">" . T_("Export") .
		 "</a> | <a href=\"statsb.php\">" . T_("Stats") . "</a> | <a href=\"easybook.php\">" . T_("EasyBook") 
		 . "</a></td><td class=\"bookmarkstitle\" style=\"align: right\">" . T_("Layout") . ": <a href=\"books.php?layout=no_js\">" 
		 . T_("Original") . "</a> | <a href=\"books.php?layout=js\">" . T_("Tree") . "</a></td></tr></table>\n");
?>