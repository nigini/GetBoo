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
// Javascript from Scuttle project

	require_once('tags_functions.php');
	$tagsStr = displayPopularTagsCloudSelect(tagCloud(getPopularTags(50, $username), 5, 90, 225, "alphabet"));
	if($tagsStr != null)
	{
		?>
		<script type="text/javascript">
		Array.prototype.contains = function (ele) {
		    for (var i = 0; i < this.length; i++) {
		        if (this[i] == ele) {
		            return true;
		        }
		    }
		    return false;
		};

		Array.prototype.remove = function (ele) {
		    var arr = new Array();
		    var count = 0;
		    for (var i = 0; i < this.length; i++) {
		        if (this[i] != ele) {
		            arr[count] = this[i];
		            count++;
		        }
		    }
		    return arr;
		};

		function addonload(addition) {
		    var existing = window.onload;
		    window.onload = function () {
		        existing();
		        addition();
		    }
		}

		addonload(
		    function () {
		        var taglist = document.getElementById('tags');
		        var tags = taglist.value.split(' ');

		        var populartags = document.getElementById('popularTags').getElementsByTagName('span');

		        for (var i = 0; i < populartags.length; i++) {
		            if (tags.contains(populartags[i].innerHTML)) {
		                populartags[i].className = 'selected';
		            }
		        }
		    }
		);

		function addTag(ele) {
		    var thisTag = ele.innerHTML;
		    var taglist = document.getElementById('tags');
		    var tags = taglist.value.split(' ');

		    //Check if checkTag is selected
		    var tagCheck = document.getElementById('tagCheck');
		    if(!tagCheck.checked)
		    {
		        tagCheck.checked = true;
		        document.getElementById("tags").readOnly = false;
	       }

		    // If tag is already listed, remove it
		    if (tags.contains(thisTag)) {
		        tags = tags.remove(thisTag);
		        ele.className = 'unselected';

		    // Otherwise add it
		    } else {
			     nbtags = tags.length;
		        tags.splice(nbtags, 0, thisTag);
		        ele.className = 'selected';
		    }

		    taglist.value = tags.join(' ');

		    document.getElementById('tags').focus();
		}

		document.write('<div class="collapsible">');
		document.write('<h3 class="bookmarksSelect"><?php echo T_("Your tags");?></h3>');
		document.write('<?php echo("<p id=\"popularTags\" class=\"tags\">" . $tagsStr . "</p>"); ?>');
		document.write('</div>');
		</script>
	<?php
	}
?>