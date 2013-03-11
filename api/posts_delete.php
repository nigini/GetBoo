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
// Based on Scuttle API classes
// Force HTTP authentication first!
require_once('httpauth.inc.php');
include("../config.inc.php");    
include("../includes/bookmarks.php");
$user = new User();
$userName = $user->getUsername();

// Note that del.icio.us only errors out if no URL was passed in; there's no error on attempting
// to delete a bookmark you don't have.

// Error out if there's no address
if (is_null($_REQUEST['url'])) {
    $deleted = false;
} else {
	$result = b_url_exist($_REQUEST['url'],$userName);
	if ($result['exists'])
	{
	    $bid = $result['bId'];
	    delete_bookmark($bid, "../");
	    $deleted = true;
	}
    else
    	$deleted = false;
}

// Set up the XML file and output the result.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<result code="'. ($deleted ? 'done' : 'something went wrong') .'" />';
?>