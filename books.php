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
session_start();
ob_start();?>
<?php
	$defaultLayout = "no_js";
	require_once("includes/user.php");
	$user = new User();
	$layout = $user->getLayout();
	$layouts = array("js", "no_js");

	if(isset($_GET["layout"]) && !empty($_GET["layout"]) && in_array($_GET["layout"], $layouts))
	{
		$layout = $_GET["layout"];
		$user->setLayout($layout);
	}
	$bookmarkPage = $layout;
	if(empty($bookmarkPage))
	{
		$user->setLayout("$defaultLayout");
		$bookmarkPage = "$defaultLayout";
	}

	include("config.inc.php");
	$customTitle = T_("My Bookmarks");
	if($bookmarkPage == "js")
	{
		$jquery_script = true;
		$tree_css = true;
	}

	require_once('header.php');
	require_once('includes/protection.php');

	include('access.php');
	$access = checkAccess('n', 't');
	if($access)
	{
		$user = new User();
		include('conn.php');
		include('bheader.php');
		include('includes/bookmarks.php');
		
		$delBookId = $_POST['delBookId'];
		if($delBookId != null && b_belongs_to($delBookId, $user->getUsername())) //Delete a bookmark
		{
			if(delete_bookmark($delBookId) == 1)
			{
				echo("<p class=\"success\">" . T_("You have successfully deleted this bookmark") . ".</p>");
			}
			else
				echo("<p class=\"error\">" . T_("There has been a problem when deleting the bookmark") . ".</p>");
		}

		//Controller
		if($bookmarkPage == "no_js")
			include("books_nojs.php");
		else if($bookmarkPage == "js")
			include("books_js.php");
		else if($bookmarkPage == "public")
			include("userb.php");
	}
	else
	{
		header("Location: login.php");
	}
	//end of page spacer
	echo("<div><br><br></div>");
	include('footer.php');
	ob_end_flush();
?>