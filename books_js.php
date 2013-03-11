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
// Using javascript (drag and drop + tooltips) from http://interface.eyecon.ro/ implementing jQuery
?>
	<ul class="myTree">
<?php
		// Call for main folder
		set_time_limit(0); // Unlimited execution time, will need to be fixed eventually (with AJAX maybe)
		echo ("\t<li class=\"treeItem\"><img src=\"images/style/$style/folder.GIF\" alt=\"Folder\" title=\"Folder\"/> <span class=\"textHolder\">" . T_("Main") . "</span>\n\t\t<ul id=\"f0\">\n");
		listFolderContent(MAIN_FID, $username, "", true, "blank");
		listFolderBookmarks(MAIN_FID, $username, "", true, "blank");

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
						$(this).prepend('<img src="images/tree/bullet_toggle_plus.png" width="16" height="16" class="expandImage" />');
					} else {
						$(this).prepend('<img src="images/tree/bullet_toggle_minus.png" width="16" height="16" class="expandImage" />');
					}
				} else {
					$(this).prepend('<img src="images/tree/spacer.gif" width="16" height="16" class="expandImage" />');
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
						this.src = 'images/tree/bullet_toggle_minus.png';
					} else {
						subbranch.hide();
						this.src = 'images/tree/bullet_toggle_plus.png';
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
										$('img.expandImage', targetBranch.parentNode).eq(0).attr('src', 'images/tree/bullet_toggle_minus.png');
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
					$("a.deleteB", this).hide();
			        $("a.editB", this).hide();
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
						$('img.expandImage', oldParent.parentNode).src('images/tree/spacer.gif');
						$(oldParent).remove();
					}
					expander = $('img.expandImage', this.parentNode);
					if (expander.get(0).src.indexOf('spacer') > -1)
						expander.get(0).src = 'images/tree/bullet_toggle_minus.png';

					var1 = dropped.id;
					var2 = subbranch.get(0).id;
					// Move it in the database
					 $.ajax({
					   type: "GET",
					   url: "ajax/movebook.php",
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
	    // Bookmarks hover
	    $("li.bookHover").hover(function() {
	        $("a.deleteB", this).css("display", "inline");
	        $("a.editB", this).css("display", "inline");
	    },function() {
	        $("a.deleteB", this).hide();
	        $("a.editB", this).hide();
	        $("span.confirm").remove();
	    });

	    // Hover effect for bookmark delete buttons
	    $("a.deleteB").hover(function() {
	        $("img", this).attr("src", "images/books/delete.GIF");
	    },function() {
	        $("img", this).attr("src", "images/books/deleteGray.GIF");
	    });

	    // Hover effect for bookmark edit buttons
	    $("a.editB").hover(function() {
	        $("img", this).attr("src", "images/books/modify.GIF");
	    },function() {
	        $("img", this).attr("src", "images/books/modifyGray.GIF");
	    });

	    // Folders hover
	    $("li.folderHover").hover(function() {
	        $("a.deleteF", this).css("display", "inline");
	        $("a.editF", this).css("display", "inline");
	    },function() {
	        $("a.deleteF", this).hide();
	        $("a.editF", this).hide();
	        $("span.confirm").remove();
	    });
	    // Hover effect for folder delete buttons
	    $("a.deleteF").hover(function() {
	        $("img", this).attr("src", "images/books/delete.GIF");
	    },function() {
	        $("img", this).attr("src", "images/books/deleteGray.GIF");
	    });

	    // Hover effect for folder edit buttons
	    $("a.editF").hover(function() {
	        $("img", this).attr("src", "images/books/modify.GIF");
	    },function() {
	        $("img", this).attr("src", "images/books/modifyGray.GIF");
	    });

	    $('a.books_desc').ToolTip(
		{
			className: 'linksTooltip',
			position: 'mouse',
			delay: 150
		});
		
		$('span.folder_desc').ToolTip(
		{
			className: 'linksTooltip',
			position: 'mouse',
			delay: 150
		});
	}
);
</script>
<?php
	echo("<a href=\"deleteallbooks.php\">" . T_("Delete All Your Bookmarks") . "</a>");
?>