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

	//TODO: Change the layout because I don't like it anymore! :)
	$feedToDisplay['type'] = "news";
	include('header.php');
	// Please change the news description, matching your own website if you ever need to display news. Otherwise simply remove the news button in header.php page
?>
<h2><?php echo T_("News");?></h2>
<div class="content">
<?php
	include('conn.php');

	$Query = ("select newsID, date as formatted_time, author, title, date, msg from " . TABLE_PREFIX . "news order by formatted_time DESC");
	if($_SESSION['news_per_page'] != "-1")
		$dblink->setLimit(NEWS_PER_PAGE, 0);
		//$Query .= (" limit 0, " . NEWS_PER_PAGE);
	//echo($Query . "<br>\n");
	$dbResult = $dblink->query($Query);
	$count = 0;

?>
<div class="news_content">
<?php
	require_once('includes/convert_date.php');
	while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	{
		$date1 = ("{$row["formatted_time"]}");
		$date2 = convert_date($date1);
		$message = $row["msg"];
		$size = strlen($message);
		$message = substr($message, 0, NEWS_MSG_LENGTH);
		//TODO: Should use function strip_tags to calculate the length without html markup
		if($size > NEWS_MSG_LENGTH)
			$message .= "...";
		echo("<div class=\"msgtitle\">{$row["title"]}</div>\n");
		echo("<div class=\"msgsubtitleL\">" . T_("Author") . ": <b>{$row["author"]}</b></div><div class=\"msgsubtitleR\">" . T_("Date") . ": <b>$date2</b></div>\n");
		echo("<div class=\"msgbody\">" . $message . "</div>\n");
		//no follow so that search engines don't increment the count
		echo("<div class=\"msgfooter\"><a href=\"newsdetails.php?id={$row["newsid"]}&amp;src=m\" rel=\"nofollow\">" . T_("Read more") . "...</a></div>\n");
		echo("<br>\n");
		$count++;
	}

	if($_SESSION['news_per_page'] != "-1" && $count == NEWS_PER_PAGE)
	{
		$_SESSION['news_per_page'] = "-1";
		echo("<div class=\"msgfooter\"><a href=\"news.php\">" . T_("All News") . "...</a></div>");
	}
	
	if($count == 0) //no news to display
	{
		echo("<p class=\"notice\">" . T_("No news yet!") . "</p></div>");
	}
	else
	{
?>
</div>
<p style="text-align:center">
<?php echo("<p><a href=\"feed.php\"><img src=\"images/firefox-rss-icon.png\" alt=\"" . T_("RSS icon") . "\" title=\"" . T_("RSS icon") . "\" width=\"15\" height=\"15\"></a> " . T_("feed for this page") . "</p>");
 } ?>
</div>
<?php include('footer.php'); ?>