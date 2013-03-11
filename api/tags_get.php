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
// Implements the del.icio.us API request for all a user's tags.

// del.icio.us behavior:
// - tags can't have spaces

// Force HTTP authentication first!
require_once('httpauth.inc.php');

include("../includes/tags_functions.php");

$user = new User();
$userName = $user->getUsername();

$tags = getPopularTags("-1", $userName);

// Set up the XML file and output all the tags.
header('Content-Type: text/xml');
echo '<?xml version="1.0" standalone="yes" ?'.">\r\n";
echo "<tags>\r\n";
foreach($tags as $row) {
    echo "\t<tag count=\"". $row['amount'] .'" tag="'. filter($row['title'], 'xml') ."\" />\r\n";
}
echo "</tags>";
?>