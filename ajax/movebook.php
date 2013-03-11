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
header('Content-Type: text/xml; charset=UTF-8');
header("Last-Modified: ". date("D, d M Y H:i:s") ." GMT");
header("Cache-Control: no-cache, must-revalidate");


function move($var1, $var2)
{
	if($var1 != "" && $var2 != "")
	{
		include('../includes/bookmarks.php');
		$action = substr($var1, 0, 1);
		$var1 = substr($var1, 1);
		$var2 = substr($var2, 1);
		switch($action)
		{
			case 'b': $result = move_bookmark($var1, $var2); break;
			case 'f': $result = move_folder($var1, $var2); break;
		}
		return $result;

	} else {
	  return false;
	}
}
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<response>
  <method>move</method>
  <result><?php echo move($_GET['var1'], $_GET['var2']); ?></result>
</response>