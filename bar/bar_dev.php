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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="shortcut icon" href="/favicon.ICO" type="image/x-icon">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo (WEBSITE_NAME) ?> -- <?php echo T_("Bar");?></title>
<link rel="stylesheet" media="screen" title="<?php echo (WEBSITE_NAME) ?>" type="text/css" href="../stylemain.css">
<link rel="stylesheet" media="screen" title="<?php echo (WEBSITE_NAME) ?>" type="text/css" href="bookmarks.css">
<script src="../includes/jquery/jquery-latest.pack.js" type="text/javascript"></script>
<script src="../includes/jquery/interface.js" type="text/javascript"></script>
<style type="text/css">
	html, body {height:100%; margin: 0; padding: 0; }

	body {
		font-family: Verdana, helvetica, arial, sans-serif;
		font-size: 11px;
		background: #fff;
		color: #333;
		padding-left: 5px;
	} /* Reset Font Size */

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

	$style = $user->getStyle();
	$username = $user->getUsername();
?>
	<ul class="myTree">
<?php
	include("../includes/bookmarks.php");
	// Call for main folder
	echo ("\t<li class=\"treeItem\"><img src=\"../images/style/$style/folder.GIF\" alt=\"Folder\" title=\"Folder\"/> <span class=\"textHolder\">" . T_("Main") . "</span>\n\t\t<ul id=\"f0\">\n");
	listFolderContent(MAIN_FID, $username, "../");
	listFolderBookmarks(MAIN_FID, $username, "../");

	echo("</ul></ul>");
?>

<script type="text/javascript">
$(document).ready(
	function()
	{
		tree = $('#myTree');
		$('li', tree.get(0)).each(
			function()
			{
				subbranch = $('ul', this);
				if (subbranch.size() > 0) {
					if (subbranch.eq(0).css('display') == 'none') {
						$(this).prepend('<img src="../images/tree/bullet_toggle_plus.png" width="16" height="16" class="expandImage" />');
					} else {
						$(this).prepend('<img src="../images/tree/bullet_toggle_minus.png" width="16" height="16" class="expandImage" />');
					}
				} else {
					$(this).prepend('<img src="../images/tree/spacer.gif" width="16" height="16" class="expandImage" />');
				}
			}
		);
		$('img.expandImage', tree.get(0)).click(
			function()
			{
				if (this.src.indexOf('spacer') == -1) {
					subbranch = $('ul', this.parentNode).eq(0);
					if (subbranch.css('display') == 'none') {
						subbranch.show();
						this.src = '../images/tree/bullet_toggle_minus.png';
					} else {
						subbranch.hide();
						this.src = '../images/tree/bullet_toggle_plus.png';
					}
				}
			}
		);
		$('span.textHolder').Droppable(
			{
				accept			: 'treeItem',
				hoverclass		: 'dropOver',
				activeclass		: 'fakeClass',
				tollerance		: 'pointer',
				onhover			: function(dragged)
				{
					if (!this.expanded) {
						subbranches = $('ul', this.parentNode);
						if (subbranches.size() > 0) {
							subbranch = subbranches.eq(0);
							this.expanded = true;
							if (subbranch.css('display') == 'none') {
								var targetBranch = subbranch.get(0);
								this.expanderTime = window.setTimeout(
									function()
									{
										$(targetBranch).show();
										$('img.expandImage', targetBranch.parentNode).eq(0).attr('src', '../images/tree/bullet_toggle_minus.png');
										$.recallDroppables();
									},
									500
								);
							}
						}
					}
				},
				onout			: function()
				{
					if (this.expanderTime){
						window.clearTimeout(this.expanderTime);
						this.expanded = false;
					}
				},
				ondrop			: function(dropped)
				{
					if(this.parentNode == dropped)
						return;
					if (this.expanderTime){
						window.clearTimeout(this.expanderTime);
						this.expanded = false;
					}
					subbranch = $('ul', this.parentNode);
					if (subbranch.size() == 0) {
						$(this).after('<ul></ul>');
						subbranch = $('ul', this.parentNode);
					}
					oldParent = dropped.parentNode;
					subbranch.eq(0).prepend(dropped);
					oldBranches = $('li', oldParent);
					if (oldBranches.size() == 0) {
						$('img.expandImage', oldParent.parentNode).src('../images/tree/spacer.gif');
						$(oldParent).remove();
					}
					expander = $('img.expandImage', this.parentNode);
					if (expander.get(0).src.indexOf('spacer') > -1)
						expander.get(0).src = '../images/tree/bullet_toggle_minus.png';

					var1 = dropped.id;
					var2 = subbranch.get(0).id;
					// Move it in the database
					 $.ajax({
					   type: "GET",
					   url: "../ajax/movebook.php",
					   data: "var1=" + var1 + "&var2=" + var2
					 });
				}
			}
		);
		$('li.treeItem').Draggable(
			{
				revert		: true,
				autoSize	: true,
				ghosting	: true/*,
				onStop		: function()
				{
					$('span.textHolder').each(
						function()
						{
							this.expanded = false;
						}
					);
				}*/
			}
		);
	}
);
</script>
</body>
</html>
