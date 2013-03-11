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

echo '<?xml version="1.0" encoding="UTF-8" ?'.">\n";
?>

<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
    <title><?php echo $feedTitle; ?></title>
    <link><?php echo $feedLink; ?></link>
    <description><?php echo $feedDesc; ?></description>
    <ttl><?php echo $feedTTL; ?></ttl>

<?php

	//Check if we have bookmarks
	if(count($bookmarks) != 0)
	{
		foreach($bookmarks as $currentB)
		{
			?>
			<item>
				<title><?php echo $currentB['bTitle']; ?></title>
				<link><?php echo $currentB['bUrl']; ?></link>
				<guid><?php echo $currentB['bUrl']; ?></guid>
				<description><?php echo $currentB['bDesc']; ?></description>
				<dc:creator><?php echo $currentB['bCreator']; ?></dc:creator>
				<pubDate><?php echo $currentB['bTime']; ?></pubDate>
				<?php
				foreach($currentB['allTagsArray'] as $tagName) {
				   echo "\t\t<category>". $tagName ."</category>\n";
				}
				?>
			</item>
	<?php
		}
	}
	else //No bookmarks to display
	{
		?>
			<item>
				<title>No bookmarks found</title>
				<link><?php echo $feedLink; ?></link>
				<guid><?php echo $feedLink; ?></guid>
				<description>No bookmarks found</description>
			</item>
<?php
 	}
?>

</channel>
</rss>