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

	$boolMain = true;
	$feedToDisplay['type'] = "main";
	include('header.php');
	//echo("<h2 style=\"margin-bottom:0px; background: transparent url('http://www.bewelcome.org/styles/YAML/navigation/images/bg.gif') repeat-x scroll center bottom;\">" . T_("Get your bookmarks!") . "</h2>");

?>
<div id="top_index">
	<div class="mainInfo" style="width: 60%; float: left">
	<br>
	<h2 style="text-align: left; margin-left: 20px; font-size: 125%; letter-spacing:0.1em;"><?php echo sprintf(T_("Welcome to %s!"), WEBSITE_NAME);?></h2>
	<h2 style="text-align: left; margin-left: 20px; font-size: 16px; letter-spacing:0.1em;"><?php echo T_("The social bookmarking open-source platform."); ?></h2>
	</div>
	<div class="mainInfo" style="width: auto; float: right;">
<?php
	include('includes/searchform.php');
	echo("</div>");
?>
	</div>
	<div id="bottom_shadow"></div>
<?php
	if(isset($_SESSION['logout_msg']) && $_SESSION['logout_msg'] != "")
	{
		echo("<div>\n");
		echo("<p class=\"success\">" . $_SESSION['logout_msg'] . "</p>\n");
		$_SESSION['logout_msg'] = null;
		echo("</div>\n");
	}
	include('recent_tags.php');
?>
