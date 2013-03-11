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
// Implements the del.icio.us API request to add a new post.

// del.icio.us behavior:
// - tags can't have spaces
// - address and description are mandatory

// Scuttle behavior:
// - Additional 'status' variable for privacy
// - No support for 'replace' variable

// GetBoo behavior:
// - No status privacy, gets public since has tags
// - support multiple tags, separated by a space or plus (+) sign
// - description becomes title, extented becomes description

// Force HTTP authentication first!
require_once('httpauth.inc.php');
    
// Get all the bookmark's passed-in information
if (isset($_REQUEST['url']) && (trim($_REQUEST['url']) != ''))
    $url = trim(urldecode($_REQUEST['url']));
else
    $url = NULL;

if (isset($_REQUEST['description']) && (trim($_REQUEST['description']) != ''))
    $title = trim($_REQUEST['description']);
else
    $title = NULL;

if (isset($_REQUEST['extended']) && (trim($_REQUEST['extended']) != ""))
    $description = trim($_REQUEST['extended']);
else
    $description = NULL;

if (isset($_REQUEST['tags']) && (trim($_REQUEST['tags']) != '') && (trim($_REQUEST['tags']) != ','))
    $tags = trim($_REQUEST['tags']);
else
    $tags = NULL;

if (isset($_REQUEST['dt']) && (trim($_REQUEST['dt']) != ''))
    $dt = trim($_REQUEST['dt']);
else
    $dt = NULL;

include("../config.inc.php");    
include("../includes/bookmarks.php");
require_once("../includes/protection.php");
$user = new User();
$userName = $user->getUsername();
	
//Make sure the url starts with http:// in the event the user's browser doesn't support javascript
if (strpos($url, ':') === false)
{
	$url = 'http://'. $url;
}
//print_r($GLOBALS);
// Require address and description
if (is_null($url) || is_null($title)) {
    $added = false;
} else {
    // Check that it doesn't exist already
    $result = b_url_exist($url,$userName);
    if ($result['exists']) {
        $added = false;
    // If not, try to add it
    } else {
	    $resultArr = add_bookmark($userName, $title, MAIN_FID, $url, $description, $tags, true, $dt);
        $added = $resultArr['success'];
    }
}

// Set up the XML file and output the result.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo '<result code="'. ($added ? 'done' : 'something went wrong') .'" />';
?>