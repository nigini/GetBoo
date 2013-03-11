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

	if(!$boolMain)
	{
?>
		<form id="search" action="psearch.php" method="get">
			<label for="search_box" style="font-size: 20px"><?php echo T_("Discover");?> </label>
			<input type="text" name="keywords" size="16" value="<?php echo $keywords_original; ?>" maxlength="50" style="padding-right: 29px; margin-left: 10px; padding-top:3px;" onfocus="this.select()" id="search_box" class="search_box"><input type="image" src="images/search.gif" class="imgButtonSearch">
		</form>
<?php
	}
	else
	{
?>
		<form action="psearch.php" method="get">
		<h2 style="text-align: center;"><?php echo T_("Discover");?></h2>
		<p style="text-align: right;"><input type="text" name="keywords" size="16" value="<?php echo $keywords_original; ?>" maxlength="50" style="padding-right: 29px; margin-left: 29px; padding-top:3px;" onfocus="this.select()" id="search_box" class="search_box"><input type="image" src="images/search.gif" class="imgButtonSearch"></p>
		</form>
		<br>
		<br>
<?php
	}
?>