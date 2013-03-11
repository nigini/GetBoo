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
// Implements the del.icio.us API request for a user's post counts by date (and optionally
// by tag).

// Force HTTP authentication first!
require_once('httpauth.inc.php');

// Check to see if a tag was specified.
if (isset($_REQUEST['tag']) && (trim($_REQUEST['tag']) != ''))
{
	$tag = trim($_REQUEST['tag']);
	$tagNames = explode(' ', trim($tag));

	$tagcount = count($tagNames);
	for ($i = 0; $i < $tagcount; $i ++)
	{
		$tagNames[$i] = trim($tagNames[$i]);
	}
}
else
    $tagNames = NULL;

// Get the posts relevant to the passed-in variables.
include("../includes/tags_functions.php");
require_once("../includes/protection.php");
$user = new User();
$userName = $user->getUsername();
if($tagNames)
	$bookmarks = getTagsBookmarks($tagNames, 0, MAX_API_BOOKMARKS, $userName, "../");
else
	$bookmarks = getUserBookmarks($userName, 0, MAX_API_BOOKMARKS, "../");

// Set up the XML file and output all the tags.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<dates tag="'. (is_null($tag) ? '' : filter($tag, 'xml')) .'" user="'. filter($userName, 'xml') ."\">\r\n";

$lastdate = NULL;
foreach($bookmarks as $row) {
    $thisdate = date('Y-m-d', strtotime($row['ADD_DATE']));
    if ($thisdate != $lastdate && $lastdate != NULL) {
        echo "\t<date count=\"". $count .'" date="'. $lastdate ."\" />\r\n";
        $count = 1;
    } else {
        $count = $count + 1;
    }
    $lastdate = $thisdate;
}
//Bug in Scuttle: output last bookmarks!
echo "\t<date count=\"". $count .'" date="'. $lastdate ."\" />\r\n";
echo "</dates>";
?>
