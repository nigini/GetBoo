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
// Using treeview from Myles Angell examples with jQuery (http://be.twixt.us/jquery/)
// Free Logos from http://www.neatui.com/neat-icons-core-set/
session_start();
$SETTINGS['path_mod'] = "../";
require_once($SETTINGS['path_mod'] . 'config.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="shortcut icon" href="/favicon.ICO" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo (WEBSITE_NAME) ?> -- <?php echo T_("Bar");?></title>
<link rel="stylesheet" media="screen" title="<?php echo (WEBSITE_NAME) ?>" type="text/css" href="../stylemain.css" />
<script src="../includes/jquery/jquery-latest.pack.js" type="text/javascript"></script>
<script src="../includes/jquery/jquery.treeview.pack.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("body > ul:eq(0), body > ul:eq(1)").Treeview({ control: "#treecontrol", collapsed: true, speed: "fast" });
});
function open_popup () {
	x=document;a=encodeURIComponent(x.location.href);t=encodeURIComponent(x.title);d=encodeURIComponent(window.getSelection());open('<?php echo WEBSITE_ROOT; ?>add.php?popup=y&g_title='+t+'&g_url='+a+'&g_desc='+d,'<?php echo WEBSITE_NAME; ?>','modal=1,status=0,scrollbars=1,toolbar=0,resizable=1,height=550,width=785,left='+(screen.width-785)/2+',top='+(screen.height-550)/2);void 0;
}
</script>
<style type="text/css">
	html, body {height:100%; margin: 0; padding: 0; }

	body {
		font-family: Verdana, helvetica, arial, sans-serif;
		font-size: 11px;
		background: #fff;
		color: #333;
		padding-left: 5px;
	} /* Reset Font Size */

	.treeview, .treeview ul {
		padding: 0;
		margin: 0;
		list-style: none;
	}

	.treeview li {
		margin: 0;
		padding: 3px 0pt 3px 16px;
	}

	ul.dir li { padding: 2px 0 0 16px; }

  	#black.treeview li { background: url(images/black/tv-item.gif) 0 0 no-repeat; }
  	#black.treeview .collapsable { background-image: url(images/black/tv-collapsable.gif); }
  	#black.treeview .expandable { background-image: url(images/black/tv-expandable.gif); }
  	#black.treeview .last { background-image: url(images/black/tv-item-last.gif); }
  	#black.treeview .lastCollapsable { background-image: url(images/black/tv-collapsable-last.gif); }
  	#black.treeview .lastExpandable { background-image: url(images/black/tv-expandable-last.gif); }

  	#treecontrol { margin: 1em 0; display:inline;}

  	a:link, a:hover, a:visited
  	{
	  	color: blue;
		text-decoration: none;
  	}

</style>
</head>
<body>
<?php
require_once($SETTINGS['path_mod'] . 'includes/user.php');
$user = new User();
$headerString = ("<h2>" . WEBSITE_NAME . " " . T_("bar login") . "</h2>");

// Logout action
if($_GET['action'] == "logout")
	$user->logout();

// Login action
if ($_POST['submitted'])
{

	$resultArr = $user->login($_POST['name'], $_POST['pass']);
	if(!$resultArr['success'])
	{
		echo $headerString;
		$headerDisplayed = true;
		echo("<b>" . $resultArr['message'] . "</b><br>");
	}
}

include($SETTINGS['path_mod'] . 'access.php');
$access = checkAccess('n','t');
if($access || $resultArr['success'])
{
	$style = $user->getStyle();
	$username = $user->getUsername();
?>
	<div id="treecontrol">
		<a href="#">Collapse</a> |
		<a href="#">Expand</a> |
		<a href="#">Toggle</a> |
	</div>
	<div style="display:inline;">
		<a onclick="javascript:window.location = 'bar.php';" href="#"><img src="icons/refresh.png" alt="<?php echo T_("Refresh");?>" title="<?php echo T_("Refresh");?>" style="border: 0; width: 16px; height: 16px;" /></a>
		<a onclick="javascript:open_popup();" href="#"><img src="icons/add.png" alt="<?php echo T_("Add Bookmark");?>" title="<?php echo T_("Add Bookmark");?>" style="border: 0; width: 16px; height: 16px;" /></a>
		<a onclick="javascript:window.location = 'bar.php?action=logout';" href="#"><img src="icons/logout.png" alt="<?php echo T_("Log Out");?>" title="<?php echo T_("Log Out");?>" style="border: 0; width: 16px; height: 16px;" /></a>
	</div>
	<br><br>
	<ul id="black">
<?php
	include('../includes/bookmarks.php');

	// Call for main folder
	listFolderContent(MAIN_FID, $username, $SETTINGS['path_mod']);
	listFolderBookmarks(MAIN_FID, $username, $SETTINGS['path_mod']);

	echo("</ul>");
}
else // Display mini login page
{
	if(!$headerDisplayed)
		echo($headerString);
?>
<form method="post" action="bar.php">
<table>
	<tr>
			<td><span class="formsLabel"><label for="login_usrname"><?php echo T_("Username");?></label></span></td>
			<td><input type="text" name="name" size="20" maxlength="20" class="formtext" onfocus="this.select()" id="login_usrname" /></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Password");?></span></td>
			<td><input type="password" name="pass" size="20" maxlength="50" class="formtext" onfocus="this.select()" /></td>
	</tr>
	<tr>
			<td></td>
			<td>
				<input type="submit" name="submitted" value="<?php echo T_("Log In");?>" class="genericButton" />
			</td>
	</tr>
</table>
</form>
<p><a href="<?php echo $SETTINGS['path_mod']; ?>newuser.php" target="_content"><?php echo T_("New User");?>?</a> | <a href="<?php echo $SETTINGS['path_mod']; ?>forgotpass.php" target="_content"><?php echo T_("Forgot password");?>?</a></p>
<?php
}
?>
</body>
</html>
