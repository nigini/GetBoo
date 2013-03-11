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
?>
		<br><b><?php echo T_("EasyBook");?> -- <?php echo T_("Bookmarklets");?></b><br><br>
		<?php echo sprintf(T_("EasyBook is a feature to make it easy to add bookmarks into your %s account"),WEBSITE_NAME);?>.
		<?php echo T_("This feature is known in the community as bookmarklets, where you simply add a bookmark into your browser, enabling you to bookmark the pages you visit more easily");?>.<br>
		<?php echo T_("If you select (highlight) a piece of text on the web page before clicking the EasyBook button, the text will be used as the description for the bookmark");?>.<br><br>
		<?php echo T_("Here are the setup instructions to enable this feature in your browser");?>.<br><br>
		<b><?php echo T_("Instructions");?> -- <?php echo T_("Select your browser");?></b><br>
		<div style="margin-left:30px">
		<table>
		<tr><td><img src="images/browsers/ie_16.png" alt="Internet Explorer" title="Internet Explorer" width="16" height="16"></td><td>Internet Explorer</td><td><a href="http://wiki.getboo.com/help/eb_ie"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/firefox_16.png" alt="Firefox" title="Firefox" width="16" height="16"></td><td>Firefox</td><td><a href="http://wiki.getboo.com/help/eb_ff"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/safari_16.png" alt="Safari" title="Safari" width="16" height="16"></td><td>Safari</td><td><a href="http://wiki.getboo.com/help/eb_saf"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/opera_16.png" alt="Opera" title="Opera" width="16" height="16"></td><td>Opera</td><td><a href="http://wiki.getboo.com/help/eb_opera"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/netscape_16.png" alt="Netscape" title="Netscape" width="16" height="16"></td><td>Netscape</td><td><a href="http://wiki.getboo.com/help/eb_nets"><?php echo T_("Tutorial");?></a></td></tr>
		</table>
		</div>
		<br>
		<b><?php echo T_("Links");?></b><br>
		<div style="margin-left:30px">
		<script type="text/javascript">
		var selection = '';
		if (window.getSelection) {
		    selection = 'window.getSelection()';
		} else if (document.getSelection) {
		    selection = 'document.getSelection()';
		} else if (document.selection) {
		    selection = 'document.selection.createRange().text';
		}
		document.write('<p align="left">');
		document.write('<a href="javascript:x=document;a=encodeURIComponent(x.location.href);t=encodeURIComponent(x.title);d=encodeURIComponent('+selection+');location.href=\'<?php echo WEBSITE_ROOT; ?>add.php?g_title=\'+t+\'&amp;g_url=\'+a+\'&amp;g_desc=\'+d;void 0;"><?php echo T_("EasyBook");?><\/a><\/p>');
		document.write('<p align="left">');
		document.write('<a href="javascript:x=document;a=encodeURIComponent(x.location.href);t=encodeURIComponent(x.title);d=encodeURIComponent('+selection+');open(\'<?php echo WEBSITE_ROOT; ?>add.php?popup=y&amp;g_title=\'+t+\'&amp;g_url=\'+a+\'&amp;g_desc=\'+d,\'<?php echo WEBSITE_NAME; ?>\',\'modal=1,status=0,scrollbars=1,toolbar=0,resizable=1,height=550,width=825,left=\'+(screen.width-825)/2+\',top=\'+(screen.height-550)/2);void 0;"><?php echo T_("EasyBook (Pop-up)");?><\/a> <?php echo T_("(opens in pop-up)");?>');
		document.write('<\/p>');
		</script>
		</div>