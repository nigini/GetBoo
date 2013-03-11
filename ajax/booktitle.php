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
// Based on Scuttle ajax title, but usign Curl library
header('Content-Type: text/xml; charset=UTF-8');
header("Last-Modified: ". date("D, d M Y H:i:s") ." GMT");
header("Cache-Control: no-cache, must-revalidate");


function getTitle($url)
{
	include('../includes/curl.php');
	$newc = new curl();
	$fd = $newc->getFile($url);

	if ($fd)
	{
	  // Get title from title tag
	  preg_match_all('/<title>(.*)<\/title>/si', $fd, $matches);
	  $title = $matches[1][0];
	  // Get encoding from charset attribute
	  preg_match_all('/<meta.*charset=([^;"]*)">/i', $fd, $matches);
	  $encoding = strtoupper($matches[1][0]);
	  // Convert to UTF-8 from the original encoding
	  $title = @mb_convert_encoding($title, 'UTF-8', $encoding);

	  if (strlen($title) > 0)
	  {
	      return $title;
	  } else {
	      // No title, so return filename
	      $uriparts = explode('/', $url);
	      $filename = end($uriparts);
	      unset($uriparts);

	      return $filename;
	  }
	} else {
	  return false;
	}
}
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<response>
  <method>getTitle</method>
  <result><?php echo getTitle($_GET['url']); ?></result>
</response>