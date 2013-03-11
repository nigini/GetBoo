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
	/* Page used for commenting a public bookmark
	 *	Started on 10.01.06
	 * TODO
	 * Add possibility for replies to certain comments. Field in the db is ready.
	 */

	require_once('access.php');
	require_once("includes/comment_functions.php");
	define("MAX_TITLE_LENGTH", 70);
	$bookmarkID = "";
	if (isset($_GET['bID']))
	{
	    $bookmarkID = $_GET['bID'];
	}

	include('conn.php');
	require_once('includes/protection.php');
	include('includes/bookmarks.php');
	include('includes/tags_functions.php');

	$success;

	$bookmarkID = filter($bookmarkID);

	if ($_POST['submitted'])
	{
		//Store the message if logged in
		require_once('includes/user.php');
		$user = new User();
		if($user->isLoggedIn())
		{
			$username = $user->getUsername();
			
			$cTitle = "";
			if (isset($_POST['ctitle']))
			{
			    $cTitle = $_POST['ctitle'];
			    $cTitle = filter($cTitle);
			}
			$cMessage = "";
			if (isset($_POST['cmessage']))
			{
			    $cMessage = $_POST['cmessage'];
			    $cMessage = filter($cMessage);
			}
	
			$cParentID = "";
			if (isset($_POST['cparentid']))
			{
			    $cParentID = $_POST['cparentid'];
			}
	
			//Convert line breaks to <br>
			$cMessage = nl2br($cMessage);
	
			if(!empty($bookmarkID) && !empty($cTitle) && !empty($cMessage))
				$success = addComment($bookmarkID, $cTitle, $cMessage, $username, $cParentID);
			else
				$msgError = T_("The form is incomplete");
		}
		else
			$msgError = T_("You are not logged in, or your session has expired");
	}

	$exists = false;
	$public = false;
	if($bookmarkID != null)
	{
		$exists = b_exists($bookmarkID);
		$public = checkIfPublic($bookmarkID);
	}

	if($bookmarkID != null && $exists && $public)
	{
		//Get bookmark title
		$bTitle = get_btitle($bookmarkID);

		//Display bookmarks
		// Strip title if too long!
		if(strlen($bTitle)>MAX_TITLE_LENGTH)
			$bTitle = substr($bTitle, 0, MAX_TITLE_LENGTH) . "..";
		$customTitle = T_("Comments on bookmark:") . " " . $bTitle;
		include('header.php');
		echo("<h2>" . $customTitle . "</h2>");

		if ($_POST['submitted'])
		{
			if($success)
				echo("<p class=\"success\">" . T_("Your comment has been added") . ".</p>");
			else if($msgError)
				echo("<p class=\"error\">$msgError.</p>");
			else
				echo("<p class=\"error\">" . T_("Error when adding the comment") . ".</p>");
		}

		$pageUrl = "tags.php?tag=" . $tagName;

		$bookmarks = getSingleBookmark($bookmarkID);

		//Display the bookmark
		$displayUser = true;
		$displayDivs = true;
		include('templates/publicb.tpl.php');

		//Display the comments if any
		$comments = getComments($bookmarkID);
		$displayCommentsStr = displayComments($comments);
		echo("<div class=\"commentContent\">");
		if($displayCommentsStr)
			echo("<b>" . T_("Comments") . "</b>$displayCommentsStr");
		else
			echo("<b>" . T_("No comments for this bookmark") . ".</b>");
		echo("<br>");

		//If a member, can add or reply to a news
		$access = checkAccess('n', 't');
		if($access)
		{
		?>
		<br>
		<div class="submitComment">
		<b><?php echo T_("Submit a comment");?></b><br><br>
		<form action="comment.php?bID=<?php echo($bookmarkID);?>" method="post">
		<input type="hidden" name="cparentid" value="0">
		<span class="formsLabel"><?php echo T_("Title");?></span><br>
		<input type="text" name="ctitle" size="50" class="formtext" onfocus="this.select()" /><br>
		<span class="formsLabel"><?php echo T_("Message");?></span><br>
		<textarea cols="60" rows="10" name="cmessage" wrap="virtual" class="formtext" onfocus="this.select()" /></textarea>
		<br><input type="submit" name="submitted" class="genericButton" value="<?php echo T_("Submit Comment");?>">
		</form>
		</div>
		<?
		}
		else
		{
			echo("<p class=\"error\">" . sprintf(T_("Please <a href=\"%s\">login</a> in order to comment this bookmark"),"login.php") . "</p>");
		}
		echo("</div>\n");
		echo("</div>\n");
		echo("</div>\n");

		//Set the blocks to display
		$blocks = array('popular');
		include('tags_rightmenu.php');

		include('publicfooter.php');
	}
	else
	{
		header('Location: index.php');
	}
?>
