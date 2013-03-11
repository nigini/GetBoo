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
include('header.php'); ?>
<?php
	include('access.php');
	$access = checkAccess();
	if($access)
	{
		include('bheader.php');
?>
	<br><b><?php echo T_("Import");?></b><br><br>
	<?php echo T_("Here you can import your bookmarks from your internet browser");?>.<br>
	<?php echo sprintf(T_("You can check the tutorial for the browser from which you want to import your bookmarks to %s"), WEBSITE_NAME);?>.<br><br>
	<b><?php echo T_("Browsers supported");?></b><br>
	<div style="margin-left:30px">
	<table>
	<tr><td><img src="images/browsers/ie_16.png" alt="Internet Explorer" title="Internet Explorer" width="16" height="16"></td><td>Internet Explorer</td><td><a href="http://wiki.getboo.com/help/importie"><?php echo T_("Tutorial");?></a></td></tr>
	<tr><td><img src="images/browsers/firefox_16.png" alt="Firefox" title="Firefox" width="16" height="16"></td><td>Firefox</td><td><a href="http://wiki.getboo.com/help/importff"><?php echo T_("Tutorial");?></a></td></tr>
	<tr><td><img src="images/browsers/safari_16.png" alt="Safari" title="Safari" width="16" height="16"></td><td>Safari</td><td><a href="http://wiki.getboo.com/help/importsaf"><?php echo T_("Tutorial");?></a></td></tr>
	<tr><td><img src="images/browsers/mozilla_16.png" alt="Mozilla" title="Mozilla" width="16" height="16"></td><td>Mozilla</td><td><a href="http://wiki.getboo.com/help/importmoz"><?php echo T_("Tutorial");?></a></td></tr>
	<tr><td><img src="images/browsers/netscape_16.png" alt="Netscape" title="Netscape" width="16" height="16"></td><td>Netscape</td><td><a href="http://wiki.getboo.com/help/importnets"><?php echo T_("Tutorial");?></a></td></tr>
	</table>
	</div>
	</div>
	<div class="content">
			<b><?php echo T_("Select the file you want to import your bookmarks from");?>:</b>
			<br><br>
			<form method="post" action="netscape_import.php" enctype="multipart/form-data">
				<input type="file" name="import" size="30" class="formtext" onfocus="this.select()"><br>
				<span class="formsLabel"><?php echo T_("Remove duplicates");?></span>
				<input type="checkbox" name="duplicate"><br><br>
				<input type="submit" value="<?php echo T_("Import");?>" class="genericButton">
			</form>
		</div>
<?php } include('footer.php'); ?>