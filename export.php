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
		<br><b><?php echo T_("Export");?></b><br><br>
		<?php echo T_("Here you can export your bookmarks to your internet browser");?>.<br>
		<?php echo T_("You can check the tutorial for the browser where you want to import your bookmarks into");?>.<br><br>
		<b><?php echo T_("Browsers supported");?></b><br>
		<div style="margin-left:30px">
		<table>
		<tr><td><img src="images/browsers/ie_16.png" alt="Internet Explorer" title="Internet Explorer"></td><td>Internet Explorer</td><td><a href="http://wiki.getboo.com/help/exportie"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/firefox_16.png" alt="Firefox" title="Firefox"></td><td>Firefox</td><td><a href="http://wiki.getboo.com/help/exportff"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/safari_16.png" alt="Safari" title="Safari"></td><td>Safari</td><td><a href="http://wiki.getboo.com/help/exportsaf"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/opera_16.png" alt="Opera" title="Opera"></td><td>Opera</td><td><a href="http://wiki.getboo.com/help/exportopera"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/mozilla_16.png" alt="Mozilla" title="Mozilla"></td><td>Mozilla</td><td><a href="http://wiki.getboo.com/help/exportmoz"><?php echo T_("Tutorial");?></a></td></tr>
		<tr><td><img src="images/browsers/netscape_16.png" alt="Netscape" title="Netscape"></td><td>Netscape</td><td><a href="http://wiki.getboo.com/help/exportnets"><?php echo T_("Tutorial");?></a></td></tr>
		</table>
		</div>
		<p>
		<form>
			<input type="button" value="<?php echo T_("Generate Export File");?>" class="genericButton" onClick="window.open('includes/netscape_export.php','mywindow','width=600,height=400')">
		</form>
		</p>

		<p><?php echo T_("It generates the file containing your bookmarks");?>.<br><?php echo T_("Store the file on your computer in order to access it from your browser");?>.</p>
		</div>
	<?php } ?>
<?php include('footer.php'); ?>