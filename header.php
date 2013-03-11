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
 header('Content-Type: text/html; charset=utf-8');
//Relative path modifier
if(!isset($SETTINGS['path_mod']))
	$SETTINGS['path_mod'] = "";
	
// Check if the script has been installed and the config file exists
if( !file_exists($SETTINGS['path_mod'] . "config.inc.php") )
{
	header('Location: install/index.php');
	exit;
}

require_once($SETTINGS['path_mod'] . "includes/user.php");
$user = new User();

//Check if the user is not timeout
if($user->isLoggedIn())
	$user->checkTimeOut($SETTINGS['path_mod']);

//date_default_timezone_set('America/Montreal');

include($SETTINGS['path_mod'] . "config.inc.php");

//Save the page name and query string in case the user tries to access a page he is not logged in
$pathStr = $_SERVER[PHP_SELF];
//$baseNameStr = basename($pathStr);
$QueryStr = $_SERVER['QUERY_STRING'];

$removeInstall = !IS_GETBOO;
// TODO: Hack for local host, please modify according to the path on your local machine (ie /getboo/ folder)
// or if debugging.
$localhost = strpos($pathStr, "/gb/");
if(!($localhost === false))
{
	$pathStr = str_replace("/gb/", "", $pathStr);
	$removeInstall = false;
}
else if(DEBUG)
	$removeInstall = false;
//End of hack

// Check if the installation folder is still there, well at least the index.php file !
if(file_exists($SETTINGS['path_mod'] . "install/index.php") && $removeInstall)
{
	echo("<p class=\"notice\">Please remove the /install folder now</p>");
}

//Set the session vars if the page is not = to login
if(strpos($pathStr, "login.php") === false)
{
	$_SESSION['pathStr'] = $pathStr;
	$_SESSION['queryStr'] = $QueryStr;
}

// Please put your favicon if you have one
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">
<head>
<link rel="shortcut icon" href="/favicon.ICO" type="image/x-icon">
<link rel="icon" type="image/x-icon" href="/favicon.ICO">
<?php
	//Determine which feed to display, depending on the page we have to display
	switch($feedToDisplay['type'])
	{
		case "main": if(NEWS) { echo("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"rss/news.php\" title=\"" . WEBSITE_NAME . " -- " . T_("News") . "\">\n"); }
					 if(IS_GETBOO) { echo("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"http://blog.getboo.com/feed\" title=\"GetBoo news\">\n"); }
		case "recent": echo("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"rss/recent.php\" title=\"" . WEBSITE_NAME . " -- " . T_("Recent bookmarks") . "\">"); break; // Recent bookmarks
		case "news": echo("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"rss/news.php\" title=\"" . WEBSITE_NAME . " -- " . T_("News") . "\">\n"); break;
		case "user": echo("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"rss/userb.php" . $feedToDisplay['value'][0] . "\" title=\"" . WEBSITE_NAME . " " . T_("User") . " -- " . $feedToDisplay['value'][1] . $feedToDisplay['value'][2] . "\">"); break; // User's bookmarks
		case "tags": echo("<link rel=\"alternate\" type=\"application/rss+xml\" href=\"rss/tags.php?tag=" . $feedToDisplay['value'][0] . "\" title=\"" . WEBSITE_NAME . " " . T_("Tags") . " -- " . $feedToDisplay['value'][0] . "\">"); break; // Tag's bookmarks
	}

	//Custom title if any
	if($customTitle)
		echo("<title>" . WEBSITE_NAME . " -- " . $customTitle . "</title>");
	else
		echo("<title>" . WEBSITE_NAME . " - " . T_("Get your bookmarks!") . "</title>");
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $SETTINGS['path_mod']; ?>style.css" media="screen, projection">
<?php if($tree_css) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $SETTINGS['path_mod']; ?>bar/bookmarks.css" media="screen, projection">
<?php }
else if($config_css) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $SETTINGS['path_mod']; ?>config.css" media="screen, projection">
<?php } ?>
<meta name="description" content="<?php echo WEBSITE_NAME;?> is a free online bookmarking service which allows you to store, edit and retrieve your bookmarks from anywhere - online.">
<meta name="keywords" content="social bookmarking, online bookmarks, online bookmarking, bookmark, bookmarks, bookmark manager, social bookmarker, private bookmarking, favorite, favorites, link, links, url, urls, folder, folders, manager, organizer, manage, organize, web, online, browser, toolbar, toolbars, personal, netscape, ie, internet explorer, opera, import, export">
<meta name="robots" content="index, follow">
<script type="text/javascript" src="<?php echo $SETTINGS['path_mod']; ?>includes/javascript/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<script type="text/javascript" src="<?php echo $SETTINGS['path_mod']; ?>includes/javascript/booksjs.php"></script>
<script type="text/javascript" src="<?php echo $SETTINGS['path_mod']; ?>includes/javascript/crumb.js"></script>
<script type="text/javascript" src="<?php echo $SETTINGS['path_mod']; ?>includes/javascript/main.js"></script>
<?php if($sorting_script) { ?>
<!-- sorting javascript -->
<script type='text/javascript' src='<?php echo $SETTINGS['path_mod']; ?>includes/javascript/sorting/common.js'></script>
<script type='text/javascript' src='<?php echo $SETTINGS['path_mod']; ?>includes/javascript/sorting/css.js'></script>
<script type='text/javascript' src='<?php echo $SETTINGS['path_mod']; ?>includes/javascript/sorting/standardista-table-sorting.js'></script>
<?php } ?>
<?php if($jquery_script) { ?>
<!-- jquery javascript -->
<script type='text/javascript' src='<?php echo $SETTINGS['path_mod']; ?>includes/jquery/jquery-latest.pack.js'></script>
<script type='text/javascript' src='<?php echo $SETTINGS['path_mod']; ?>includes/jquery/interface.js'></script>
<script type='text/javascript' src='<?php echo $SETTINGS['path_mod']; ?>includes/jquery/bookmarksjs.php'></script>
<?php } ?>
<?php if($jquery_script_form) { ?>
<!-- jquery javascript -->
<script type='text/javascript' src='<?php echo $SETTINGS['path_mod']; ?>includes/jquery/jquery-1.2.3.pack.js'></script>
<script type='text/javascript' src='<?php echo $SETTINGS['path_mod']; ?>includes/jquery/jquery.form.pack.js'></script>
<?php } ?>
</head>
<?php if(!$sorting_script) { $onload = (" onload=\"sf();\""); } ?>
<body<?php echo $onload; ?>>
<div id="header"<?php if($boolMain) { echo(" style=\"background: transparent;\"");}?>>
	<!-- TODO: Please change with your custom logo! -->
	<div id="getbootitle"><a href="<?php echo WEBSITE_ROOT; ?>"><img src="<?php echo $SETTINGS['path_mod']; ?>images/getboologo.png" alt="GetBoo Logo" title="GetBoo Logo" width="222" height="29"></a></div>
	<p id="navigation">
		<?php
			//If not logged in
			if(!$user->isLoggedIn())
			{
		?>
				<?php if(NEWS) { ?> <a href="<?php echo $SETTINGS['path_mod']; ?>news.php" title="<?php echo T_("Read the news");?>"><?php echo T_("News");?></a><span> / </span> 
				<a href="<?php echo $SETTINGS['path_mod']; ?>about.php" title="<?php echo sprintf(T_("What is %s about?"),WEBSITE_NAME);?>"><?php echo T_("About");?></a><?php }
				else if (IS_GETBOO && $boolMain) { ?>
				<a href="https://sourceforge.net/project/showfiles.php?group_id=194055" class="gb_download" title="<?php echo sprintf(T_("Download a copy of the lastest version on %s!"), "SourceForge.net");?>"><?php echo(sprintf(T_("Download GetBoo %s"),"1.04"));?></a>
		<?php
				}
			}
			//If logged in
			else
			{
		?>
				<a href="<?php echo $SETTINGS['path_mod']; ?>books.php" title="<?php echo T_("Manage your bookmarks");?>"><?php echo T_("Bookmarks");?></a><span> / </span>
				<a href="<?php echo $SETTINGS['path_mod']; ?>add.php" title="<?php echo T_("Add bookmarks and folders");?>"><?php echo T_("Add");?></a><span> / </span>
				<a href="<?php echo $SETTINGS['path_mod']; ?>groups.php" title="<?php echo T_("Manage your groups");?>"><?php echo T_("Groups");?></a>
		<?php
			}
		?>
	</p>
	<p id="access">
		<?php if(!NEWS && !$user->isLoggedIn()) { ?> <a href="<?php echo $SETTINGS['path_mod']; ?>about.php" title="<?php echo sprintf(T_("What is %s about?"),WEBSITE_NAME);?>"><?php echo T_("About");?></a><span> / </span> <?php } ?>
		<a href="http://wiki.getboo.com/help/helpindex" title="<?php echo sprintf(T_("Get help with %s"), WEBSITE_NAME);?>"><?php echo T_("Help"); ?></a><span> / </span>
		<?php
			//If not logged in
			if(!$user->isLoggedIn())
			{
		?>
				<a href="<?php echo $SETTINGS['path_mod']; ?>newuser.php" title="<?php echo T_("Register an account");?>"><?php echo T_("Register");?></a><span> / </span>
				<a href="<?php echo $SETTINGS['path_mod']; ?>login.php" title="<?php echo T_("Login into your account");?>"><?php echo T_("Log In");?></a>
		<?php
			}
			//If logged in
			else
			{
		?>
				<a href="<?php echo $SETTINGS['path_mod']; ?>controlpanel.php" title="<?php echo T_("Manage your account settings");?>"><?php echo T_("Settings");?></a><span> / </span>
				<a href="<?php echo $SETTINGS['path_mod']; ?>logout.php" title="<?php echo T_("Exit your session");?>"><?php echo T_("Logout");?></a>
		<?php
			}

			//Update the LastVisit field in table session if logged in
			if($user->isLoggedIn())
				$user->updateLastVisit(false, $SETTINGS['path_mod']);
		?>
    </p>
</div>