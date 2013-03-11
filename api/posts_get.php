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
// Implements the del.icio.us API request for a user's posts, optionally filtered by tag and/or
// date. Note that when using a date to select the posts returned, del.icio.us uses GMT dates --
// so we do too.

// del.icio.us behavior:
// - includes an empty tag attribute on the root element when it hasn't been specified

// Scuttle behavior:
// - Uses today, instead of the last bookmarked date, if no date is specified

// GetBoo behavior:
// - support multiple tags, separated by a space or plus (+) sign

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

// Check to see if a date was specified; the format should be YYYY-MM-DD
if (isset($_REQUEST['dt']) && (trim($_REQUEST['dt']) != ""))
    $dtstart = trim($_REQUEST['dt']);
else
    $dtstart = date('Y-m-d H:i:s');
$dtend = date('Y-m-d H:i:s', strtotime($dtstart .'+1 day'));

// Filter by URL
if (isset($_REQUEST['url']) && strlen($_REQUEST['url']) > 0) {
    $hash = ($_REQUEST['url']);
} else {
    $hash = NULL;
}

// Get the posts relevant to the passed-in variables.
include("../includes/tags_functions.php");
require_once("../includes/protection.php");
$user = new User();
$userName = $user->getUsername();
if($tagNames)
	$bookmarks = getTagsBookmarks($tagNames, 0, MAX_API_BOOKMARKS, $userName, "../", $dtstart, $dtend, $hash);
else
	$bookmarks = getUserBookmarks($userName, 0, MAX_API_BOOKMARKS, "../", $dtstart, $dtend, $hash);

// Set up the XML file and output all the tags.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<posts'. (is_null($dtstart) ? '' : ' dt="'. $dtstart .'"') .' tag="'. (is_null($tag) ? '' : filter($tag, 'xml')) .'" user="'. filter($userName, 'xml') ."\">\r\n";

foreach($bookmarks as $row) {
    if (is_null($row['description']) || (trim($row['description']) == ''))
        $description = '';
    else
        $description = 'extended="'. filter($row['description'], 'xml') .'" ';
    
    $taglist = returnAllTags($row['id'], "../");

    echo "\t<post href=\"". filter($row['url'], 'xml') .'" description="'. filter($row['title'], 'xml') .'" '. $description .'hash="'. md5($row['url']) .'" tag="'. filter($taglist, 'xml') .'" time="'. date('Y-m-d\TH:i:s\Z', strtotime($row['ADD_DATE'])) ."\" />\r\n";
}

echo '</posts>';
?>