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
include('header.php');
// TODO: Check if we still need this page, now that feeds are quite common to people 04.16.2007
?>
<h2><?php echo T_("Available feed");?></h2>
<div class="content">
<?php echo sprintf(T_("The feed below enables you to read the news on %s with any feed reading software"),WEBSITE_NAME);?>.<br>
<?php echo sprintf(T_("Because %s has a feed available, people can subscribe to it using software for reading syndicated content called a \"newsreader.\" Currently, %s News feed is written in RSS only"),WEBSITE_NAME,WEBSITE_NAME);?>.
<br><br>
<b>RSS 2.0</b>
<br>
<a href="rss/news.php">All news</a>
<br><br>
<table width="340" style="border:1px solid #e0e0e0;">
	<tr>
		<td><b><?php echo T_("Feed-reading software");?></b></td>
	</tr>
	<tr>
	<td valign="top"><table border="0" cellspacing="0" cellpadding="2">
	<tr valign="top">
	<td>&#149;</td>
	<td><?php echo T_("Web-based");?>: <a href="http://www.bloglines.com/">Bloglines</a>, <a href="http://www.google.com/reader/">Google Reader</a></td>
	</tr>

	<tr valign="top">
	<td>&#149;</td>
	<td><?php echo T_("Cross platform");?>: <a href="http://www.cincomsmalltalk.com/BottomFeeder/">BottomFeeder</a>, <a href="http://www.newsmonster.org/">NewsMonster</a> </td>
	</tr>
	<tr valign="top">
	<td>&#149;</td>

	<td>Windows: <a href="http://www.feeddemon.com/">FeedDemon</a>, <a href="http://www.newsgator.com/">NewsGator</a> </td>
	</tr>
	<tr valign="top">
	<td>&#149;</td>
	<td>MacOS X: <a href="http://ranchero.com/software/netnewswire/">NetNewsWire</a></td>

	</tr>
	<tr valign="top">
	<td></td>
	<td align="right"><i><?php echo T_("Source");?>: google.com</i></td>
	</tr>
</table>
</td>
</tr>
</table>
<br><?php echo T_("Other than usign these softwares to read the feed, some browser support feeds such as");?> <b>Firefox</b>.
<br>(<?php echo sprintf(T_("Look for the %s icon in the browser"),"<img src=\"images/firefox-rss-icon.png\" alt=\"" . T_("RSS icon") . "\" title=\"" . T_("RSS icon") . "\" width=\"15\" height=\"15\">");?>)
<br><br>
<a style="text-decoration:none; color: white;" href="http://feedvalidator.org/check.cgi?url=<?php echo WEBSITE_ROOT; ?>rss/news.php">
<img src="images/valid-rss.png" alt="<?php echo T_("[Valid RSS]");?>" title="<?php echo T_("Validate my RSS feed");?>" width="88" height="31" /></a>
</div>
<?php include('footer.php'); ?>