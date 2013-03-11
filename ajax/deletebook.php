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
header('Content-Type: text/xml; charset=UTF-8');
header("Last-Modified: ". date("D, d M Y H:i:s") ." GMT");
header("Cache-Control: no-cache, must-revalidate");

include("../conn.php");
function delete($bookmarkID)
{
	$result = false;
	if($bookmarkID != "")
	{
		include('../access.php');
		$access = checkAccess('n', 't');
		if($access)
		{
			$user = new User();
			$username = $user->getUsername();
			include("../includes/bookmarks.php");
			if(b_belongs_to($bookmarkID, $username, "../"))
				$result = delete_bookmark($bookmarkID, "../");
		}
	}
	return $result;
}
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<response>
  <method>delete</method>
  <result><?php echo delete($_GET['bookID']); ?></result>
</response>