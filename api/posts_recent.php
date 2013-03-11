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
// Implements the del.icio.us API request for a user's recent posts, optionally filtered by
// tag and/or number of posts (default 15, max 100, just like del.icio.us).

// Set default and max number of posts
$countDefault = 15;
$countMax = 100;

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

// Check to see if the number of items was specified.
if (isset($_REQUEST['count']) && (intval($_REQUEST['count']) != 0))  {
    $count = intval($_REQUEST['count']);
    if ($count > $countMax)
        $count = $countMax;
    elseif ($count < 0)
        $count = 0;
} else {
    $count = $countDefault;
}

// Get the posts relevant to the passed-in variables.
include("../includes/tags_functions.php");
require_once("../includes/protection.php");
$user = new User();
$userName = $user->getUsername();
if($tagNames)
	$bookmarks = getTagsBookmarks($tagNames, 0, $count, $userName, "../");
else
	$bookmarks = getUserBookmarks($userName, 0, $count, "../");

// Set up the XML file and output all the posts.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<posts update="'. date('Y-m-d\TH:i:s\Z') .'" user="'. htmlspecialchars($userName) .'"'. (is_null($tag) ? '' : ' tag="'. htmlspecialchars($tag) .'"') .">\r\n";
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