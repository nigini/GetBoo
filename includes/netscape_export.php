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

/* Author: Bj�rn Andersson <gaqzi@linux.se>
 * Modified and adapted by: Maxime Chartrand-Dumas <maxime@getboo.com>
 *
 * Just put this page in your apb root directory and call it when
 * logged in and it'll print out a file that you can import using Netscape compatible
 * browsers. (Firefox, Mozilla :P)
 */

//strip out html
require_once('protection.php');

include('../access.php');
$access = checkAccess();
if($access)
{
	$user = new User();
	$username = $user->getUsername();
	include('../conn.php');
	include('convert_date.php');


	$Query = "SELECT title, add_date as add_date, last_visit as last_visit,
				last_modified as last_modified, url, description, folderid
				FROM " . TABLE_PREFIX . "favourites where name = '$username' order by title";
	$data = $dblink->query($Query) or die ('Error with query');
	$bookmarks = array ();

	// Sort up the bookmarks by their group_id for later when we're printing groups
	while ($row =& $data->fetchRow (DB_FETCHMODE_ASSOC))
	{
		$bookmarks[$row['folderid']][] = array (
			'url'			=> $row['url'],
			'title'			=> $row['title'],
			'add_date'		=> $row['add_date'],
			'last_visit'	=> $row['last_visit'],
			'last_modified'=> $row['last_modified'],
			'description'	=> $row['description'],
			);
	}

	// Now for the groups
	$group_query = "SELECT id, add_date as add_date, pid, title, description
						FROM " . TABLE_PREFIX . "folders where (name = '$username' and pid!='-1') order by title";
	$group_data = $dblink->query($group_query) or die ('Error with group query');
	$groups = array ();

	while ($row =& $group_data->fetchRow (DB_FETCHMODE_ASSOC))
	{
		/* We need to know the relationsship of a subgroup if there is one
		 * so we save the subgroup in the maingroup so we can call them
		 * on their own when we call them.
		 */
		if (!empty ($row['pid']))
			$groups[$row['pid']]['childs'][] = $row['id'];

		$groups[$row['id']]['data'] = array (
				'title'			=> $row['title'],
				'add_date'		=> $row['add_date'],
				'last_visit'	=> $row['last_visit'],
				'last_modified'	=> $row['last_modified'],
				'description'	=> ((!empty ($row['description'])) ? $row['description'] : false),
				'child'			=> ((!empty ($row['pid'])) ? true : false),
				);
	}

	/* Prints out the bookmark itself */
	function print_row_data ($arr, $iter = 0)
	{
		print str_repeat ("\t", $iter);
		printf ("<DT><A HREF=\"%s\" ADD_DATE=\"". $arr['add_date'] ."\" LAST_VISIT=\"". $arr['last_visit'] ."\" LAST_MODIFIED=\"". $arr['last_modified'] ."\">%s</A>%s", $arr['url'], $arr['title'], ((!empty ($arr['description'])) ? "\n". str_repeat ("\t", $iter +1) ."<DD>{$arr['description']}" : ''));
		print "\n";
	}

	/* Iterates through a cathegory.
	 * Takes the id of the cathegory to search and a number which indicates
	 * the levels of iterations (or number of tabs we want ;) )
	 */
	function iterate_cat ($id, $iter = 0)
	{
		global $groups;
		global $bookmarks;
		$cathegory = $groups[$id];
		if (!empty ($cathegory['data']['title']))
			print str_repeat ("\t", $iter) .'<DT><H3 ADD_DATE="'. $cathegory['data']['add_date'] .'">'. $cathegory['data']['title'] ."</H3>\n";
		if (!empty ($cathegory['data']['description']))
			print str_repeat ("\t", $iter +1) .'<DD>'. $cathegory['data']['description'] ."\n";

		if (!empty ($cathegory['data']['title']))
			print str_repeat ("\t", $iter). "<DL><p>\n";

		if (!empty ($cathegory['childs']))
		{
			foreach ($cathegory['childs'] as $child_id)
			{
				iterate_cat ($child_id, $iter +1);
			}
		}

		if (!empty ($bookmarks[$id]))
		{
			foreach ($bookmarks[$id] as $data)
			{
				print_row_data ($data, $iter);
			}
		}

		print str_repeat ("\t", $iter) ."</DL><p>\n";
	}

  header("Content-Type: text/html");
  header("Cache-control: private");
  header("Pragma: ");
  header("Content-Disposition: attachment; filename=\"bookmarks.html\";");
	?>
<!DOCTYPE NETSCAPE-Bookmark-file-1>
<!-- This is an automatically generated file.
	  It will be read and overwritten.
	  DO NOT EDIT! -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE>Bookmarks</TITLE>
<H1 LAST_MODIFIED="<?=time ()?>">Bookmarks</H1>

<DL>
	<?php
	/* Pretty simple, just prints out all the groups thats not a child of anyone
	 * and conforms to the netscape format.
	 */

	foreach ($groups as $id => $stuff)
	{
		if ($stuff['data']['child'])
			continue;
		iterate_cat ($id);
	}
	iterate_cat ("0");
	print "\n";
}

		//Store the export date in table bookexportimport
		$Query = "INSERT INTO " . TABLE_PREFIX . "bookexportimport ( Name , Method , Time , IP ) values ('$username', 'EX', now(), '" . $_SERVER['REMOTE_ADDR'] . "')";
		//echo("$Query<br>\n");
		$dbResult = $dblink->query($Query) or die ('Error updating bookexportimport table');

?>
