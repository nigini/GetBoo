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
	require_once('includes/tags_functions.php');
?>
<div class="tags_rightmenu">
<?php
	if(in_array('profile', $blocks))
	{
		require_once('includes/user.php');
		if(IS_GETBOO) {
		if(User::isDonor($userName)) // Check if user is a donor
		{
			// Retrieve profile info
			$fields = array('realname', 'displayemail', 'website', 'information', 'email');
			$where = array('name'=>$userName);
			$data = Prototype::queryData($fields, "session", $where);
			$donorContent = "" . T_("Name") . ": " . $data[0]['realname'] . "<br>\n";
			$donorContent .= "" . T_("Website") . ": <a href=\"" . $data[0]['website'] . "\">" . $data[0]['website'] . "</a><br>\n";
			$donorContent .= "" . T_("Information") . ":<br>\n<textarea cols=\"30\" rows=\"4\" readonly=\"readonly\">" . $data[0]['information'] . "</textarea><br>\n";

		?>
	<div class="rm_heading"><?php echo T_("Donor Profile");?></div>
	<div class="rm_content"><?php echo("<p>" . $donorContent . "</p>"); ?></div>
	<?php if($data[0]['displayemail']) { ?>
	<p class="rm_bottom"><a href="mailto:<?php echo $data[0]['email'];?>"><?php echo T_("Contact User");?></a></p>
<?php
			}
			else
				echo("<p>&nbsp;</p>");
		}
		}
	}
	if(in_array('related', $blocks))
	{
		if($current_page != "")
		{
			$strRelated = displayRelatedTagsList(getRelatedTags($tagNames, 10, $userName), $tagName, $current_page);
			$strRelatedMinus = displayRelatedTagsListMinus ($tagName, $tagNames, $current_page);
		}
		else
		{
			$strRelated = displayRelatedTagsList(getRelatedTags($tagNames, 10, $userName), $tagName);
			$strRelatedMinus = displayRelatedTagsListMinus ($tagName, $tagNames);
		}
		if($strRelated || $strRelatedMinus)
		{
			if($current_page == "")
				$current_page = "recent_tags.php"; // If in the tags page, display the recent tags page
			echo("<div class=\"rm_heading\">" . T_("Related Tags") . "</div>");
			if($strRelatedMinus)
			{
				if($userName != "")
					$forAllStr = (" <a href=\"tags.php?tag=" . $tagName . "\" title=\"" . T_("Display tags for all users") . "\">[" . T_("all users") . "]</a>");
				echo("<div class=\"rm_content\"><a href=\"" . $current_page . "\" title=\"" . T_("Remove all tags") . "\">[" . T_("remove all") . "]</a>\n" . $forAllStr . $strRelatedMinus . $strRelated . "</div><br>");

			}
			else
				echo("<div class=\"rm_content\">" . $strRelated . "</div><br>");
		}
	}
	if(in_array('popular', $blocks))
	{
		if(!(IS_GETBOO && $userName == ""))
		{
			if($current_page != "" && $current_page != "recent_tags.php")
				$strPopular = displayPopularTagsCloud(tagCloud(getPopularTags(35, $userName), 5, 85, 170, "alphabet"), $current_page);
			else if(!$boolMain)
				$strPopular = displayPopularTagsCloud(tagCloud(getPopularTags(35, $userName), 5, 85, 170, "alphabet"));
			else
				$strPopular = displayPopularTagsCloud(tagCloud(getPopularTags(50, $userName), 5, 85, 250, "alphabet"));
			if($strPopular != "")
			{
				if($userName != "")
					$userStr = "?uname=" . $userName;
			?>
		<div class="rm_heading"><?php echo T_("Popular tags");?></div>
		<div class="rm_content"><?php echo("<p class=\"menu_tags\">" . $strPopular . "</p>"); ?></div>
		
<?php
			}
		}
		if(IS_GETBOO)
		{
			$adsSpot = "rightmenu";
			include("gbads.php");
		}
		if($strPopular != "")
		{?>
		<p class="rm_bottom"><a href="populartags.php<?php echo $userStr;?>"><?php echo T_("Popular Tags");?></a></p>
<?php
		}
	}
?>
</div>