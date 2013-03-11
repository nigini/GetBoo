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
// Implements the del.icio.us API request to rename a user's tag.

// del.icio.us behavior:
// - oddly, returns an entirely different result (<result></result>) than the other API calls.

// Force HTTP authentication first!
require_once('httpauth.inc.php');

// Get the tag info.
if (isset($_REQUEST['old']) && (trim($_REQUEST['old']) != ''))
    $old = trim($_REQUEST['old']);
else
    $old = NULL;

if (isset($_REQUEST['new']) && (trim($_REQUEST['new']) != ''))
    $new = trim($_REQUEST['new']);
else
    $new = NULL;

if (is_null($old) || is_null($new)) {
    $renamed = false;
} else {
    // Rename the tag.
	/* GetBoo doesn't implement renaming tags, we simply unlink old tags.
		TODO: See if we need to implement this functionality
    $result = renameTag($old, $new, true);
    $renamed = $result;
	*/
	$renamed = false;
}

// Set up the XML file and output the result.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<result>'. ($renamed ? 'done' : 'something went wrong') .'</result>';
?>
