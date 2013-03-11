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
	$sorting_script = true;
	include('header.php');
	include('access.php');
	$access = checkAccess('a');
	if($access)
	{
		include('conn.php');
		include('includes/protection.php');

		$pageNb = "";
		if (isset($_GET['page']))
		{
		    $pageNb = $_GET['page'];
		}
		else
			$pageNb = "1";
		remhtml($pageNb);

		$perpagenb = "25";
		$minTagsNb = ($pageNb - 1) * $perpagenb;
		$maxTagsNb = $perpagenb;

		$orderBy = "LastLog desc";
		if (isset($_GET['order']))
		{
		    $orderBy = $_GET['order'];
		}
		remhtml($orderBy);

		echo("<h2>" . T_("Settings") . " -- " . T_("Manage Users") . "</h2>\n");

		if ($_POST['changeStatus']) // Enable or disable a user account
		{
			$uname = $_POST["name"];
			$currentStatus = $_POST["currentStatus"];
			if($uname!=null)
			{
				$actionStatus = ($currentStatus == "disabled")?"enabled":"disabled";
				$Query = "update " . TABLE_PREFIX . "session set status='$actionStatus' where name='" . $uname . "'";
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows == 1)
				{
					echo("<p class=\"success\">" . sprintf(T_("You have changed %s's account status"),$uname) . ".</p>\n");
					$success = true;
				}
			}
			else
			{
				echo("<p class=\"error\">" . T_("The username is missing") . "</p>\n");
			}
		}

		if ($_POST['makeDonor']) // Make a donor account
		{
			$uname = $_POST["name"];
			if($uname!=null)
			{
				$Query = "update " . TABLE_PREFIX . "session set donor=1 where name='" . $uname . "'";
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows == 1)
				{
					echo("<p class=\"success\">" . sprintf(T_("You have enabled %s's account as a donor"),$uname) . "!</p>\n");
					$success = true;
				}
			}
			else
			{
				echo("<p class=\"error\">" . T_("The username is missing") . "</p>\n");
			}
		}

		$Query = ("select name, lastlog AS formatted_time, status from " . TABLE_PREFIX . "session order by " . $orderBy);// . " limit " . $minTagsNb . ", " . ($maxTagsNb + 1));
		$dblink->setLimit($maxTagsNb+1, $minTagsNb);
		$dbResult = $dblink->query($Query);

		$count = 0;
		require_once('includes/convert_date.php');
	  	while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
	  	{
		  	if($count == 0)
		  	{
			  	echo("<div class=\"content\">\n");
			  	$fields = (IS_GETBOO)?5:4;
		  		echo("<table class='sortable'>\n<thead><tr><th>" . T_("User") . "</th><th colspan='$fields' class='skipsort'>" . T_("Options") . "</th><th class='skipsort'>" . T_("Last Login") . "</th></tr></thead><tbody>\n");
	  		}

	  		if($count == $maxTagsNb)
	  		{
	  			$moreUsers = true;
	  		}
	  		else
	  		{
			  	$date1 = ("{$row["formatted_time"]}");
				$date2 = convert_date($date1);
				if(IS_GETBOO) // Include donation
					$donationStr = "<td>\n<form action=\"manageusers.php\" method=\"post\">\n<input type=\"hidden\" name=\"name\" value=\"{$row["name"]}\" />\n<input type=\"submit\" class=\"genericButton\" name=\"makeDonor\" value=\"" . T_("Donor") . "\" />\n</form>\n</td>\n";

				$status = "{$row["status"]}";
				if($status == "disabled")
					$nameStr = "<span style=\"color: red\">{$row["name"]}</span>";
				else if($status == "admin")
					$nameStr = "<span style=\"color: blue\">{$row["name"]}</span>";
				else
					$nameStr = "{$row["name"]}";

				$statusBtn = ($status=="disabled")?T_("Enable"):T_("Disable");

				echo("<tr>\n<td valign=\"middle\" width=\"125px\">\n<a href=\"userb.php?uname={$row["name"]}\">$nameStr</a>\n</td>\n<td valign=\"bottom\">\n<form action=\"deleteaccount.php\" method=\"post\">\n<input type=\"hidden\" name=\"uname\" value=\"{$row["name"]}\" />\n<input type=\"submit\" name=\"submitted\" class=\"genericButton\" value=\"" . T_("Delete") . "\" />\n</form>\n</td>\n<td valign=\"bottom\">\n<form action=\"modifyaccount.php\" method=\"post\">\n<input type=\"hidden\" name=\"name\" value=\"{$row["name"]}\" />\n<input type=\"submit\" class=\"genericButton\" value=\"" . T_("Modify") . "\" />\n</form>\n</td>\n<td valign=\"bottom\">\n<form action=\"accessaccount.php\" method=\"post\">\n<input type=\"hidden\" name=\"name\" value=\"{$row["name"]}\" />\n<input type=\"submit\" class=\"genericButton\" value=\"" . T_("Access") . "\" />\n</form>\n</td>\n<td>\n<form action=\"manageusers.php\" method=\"post\">\n<input type=\"hidden\" name=\"name\" value=\"{$row["name"]}\" />\n<input type=\"hidden\" name=\"currentStatus\" value=\"$status\" />\n<input type=\"submit\" class=\"genericButton\" style=\"width: 100%\" name=\"changeStatus\" value=\"$statusBtn\" />\n</form>\n</td>\n$donationStr<td valign=\"middle\">\n" . $date2 . "\n</td>\n</tr>\n");
				$count++;
	  		}
	  	}
		if($count == 0)
			echo("<p class=\"error\">" . T_("No Users") . "</p><div class=\"content\">\n");
		else
		{
			echo("</tbody></table>");
			echo("<p>" . T_("Order users by") . " <a href=\"manageusers.php?order=DateJoin\">" . T_("Date Join") . "</a> / <a href=\"manageusers.php?order=LastLog%20desc\">" . T_("Last Login") . "</a></p>\n");
		}

	  	$orderStr = "?order=" . $orderBy;
		echo("<p class=\"paging\">");
		if($pageNb > 1)
			echo("<a accesskey=\"p\" href=\"manageusers.php" . $orderStr . "&amp;page=" . ($pageNb - 1) . "\">" . T_("Previous") . "</a><span> | </span>");
		else
			echo("<span class=\"disable\">" . T_("Previous") . "</span><span> | </span>");
		if($moreUsers)
			echo("<a accesskey=\"n\" href=\"manageusers.php" . $orderStr . "&amp;page=" . ($pageNb + 1) . "\">" . T_("Next") . "</a>");
		else
			echo("<span class=\"disable\">" . T_("Next") . "</span>");
		echo("</p>");
		
		echo("<p><b>" . T_("Other operations") . "</b>");

		echo("<p><a href=\"newuser.php\">" . T_("Add a new user") . "</a><br> - " . T_("Adds a new user to the system") . ".</p>");

		echo("<p><a href=\"removeactivations.php\">" . T_("Remove old registrations") . "</a><br> - " . T_("Lists the accounts not yet activated and provides an option to remove them.") . "<br>\n");
		
		echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to") . " " . T_("Settings") . "</a></p></div>");
	   
	}
?>
<?php include('footer.php'); ?>