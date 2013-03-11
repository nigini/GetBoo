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
//TODO: Check for file validity (eventhough nothing will happen anyways) and check for maximum file size upload if different than default
include('header.php'); ?>
<?php
/* Author: Bj�rn Andersson <gaqzi@linux.se>
 * Modified and adapted by: Maxime Chartrand-Dumas <maxime@getboo.com>
 */
include('access.php');
$access = checkAccess();
if($access)
{
	$success = false;
	set_time_limit(100);
	$user = new User();
	$username = $user->getUsername();

	echo("<h2>" . T_("Import bookmarks") . "</h2>");

	//strip out html
	include('includes/protection.php');

	include('conn.php');

	if (!empty ($_FILES['import']['tmp_name']))
	{
		$file = file ($_FILES['import']['tmp_name']);
		$cathegoriess = array ();
		$bookmarks = array ();

		if (!empty ($_POST['dry_run']))
			define ('DRY_RUN', true);

		if (!empty ($_POST['duplicate']))
		{
			define ('DUPLICATE', true);
			$nbDupFolders = 0;
			$nbDupBooks = 0;
		}

		$cat_id = 0; // The current category ID
		$cat_level = -1; // How deep in the tree we are
		$level_meaning = array (); // A small hasharray that is indexed by the level and
									// gives the cat_id of that level (w00t makes this work!)

		foreach ($file as $row)
		{
			$tmp = array ();
			/* If we see any of these it means we're having a new
			 * category comming up - so indent and put up another cat_id
			 */
			if (preg_match_all ('#<DL><p>#', $row, $tmp))
			{
				$cat_parent = $cat_id;
				$cat_id++;
				$cat_level++;
				$level_meaning[$cat_level] = $cat_id;
			}
			/* If we see this it means we're quitting the current category
			 * so lets put down the level and the child
			 * And change the meaning of the level
			 */
			elseif (preg_match_all ('#</DL><p>|</p></DL>#', $row, $tmp))
			{
				$cat_parent = $cat_id -1;
				$cat_level--;
				if ($cat_level == 0)
					$cat_parent = 0;
				$level_meaning[$cat_level] = $cat_id;
			}
			/* This is the actual category name so save it away safely */
			elseif (preg_match_all ('#<DT><H3.*?>(.*?)</H3>#', $row, $tmp))
			{
				$category[$cat_id] = array (
						'child'	=> (!empty ($level_meaning[$cat_level -1]) ? $level_meaning[$cat_level -1] : 0),
						'name'	=> $tmp['1']['0'],
						'desc'		=> '',
						);
			}
			// Bookmark (add_date, last_modified)
			elseif (preg_match_all ('#<DT><A HREF="(.*?)" ADD_DATE="(.*?)"(.*?)LAST_MODIFIED="(.*?)"(.*?)>(.*?)</A>#', $row, $tmp))
			{
				//added: support for dates (add_date, last_modified)
				$tmp_id = (!empty ($level_meaning[$cat_level -1]) ? $level_meaning[$cat_level -1] : 0);
				$bookmarks[$tmp_id][] = array (
					'url'			=> $tmp['1']['0'],
					'add_date'		=> $tmp['2']['0'],
					'last_modified'	=> $tmp['4']['0'],
					'title'			=> $tmp['6']['0'],
					'desc'			=> '',
					);
			}
			// Bookmark (add_date only)
			elseif (preg_match_all ('#<DT><A HREF="(.*?)" ADD_DATE="(.*?)"(.*?)>(.*?)</A>#', $row, $tmp))
			{
				//added: support for dates (add_date, last_modified)
				$tmp_id = (!empty ($level_meaning[$cat_level -1]) ? $level_meaning[$cat_level -1] : 0);
				$bookmarks[$tmp_id][] = array (
					'url'			=> $tmp['1']['0'],
					'add_date'		=> $tmp['2']['0'],
					'title'			=> $tmp['4']['0'],
					'desc'			=> '',
					);
			}
			/* Save the description for the bookmark
			 elseif (preg_match_all ('#<DD>(.*?)&lt;/dd&gt;|<DD>(.*?)</dd>#i', $row, $tmp))
			 */
			elseif (preg_match_all ('#<DD>(.*?)#', $row, $tmp))
			{
				$desc = str_replace("<DD>", "", $row);
				//echo("$desc");
				$tmp_id = (!empty ($level_meaning[$cat_level -1]) ? $level_meaning[$cat_level -1] : 0);
				if (!empty ($desc))
				{
					$count = count ($bookmarks[$tmp_id]);
					if($count == 0)
					{
						$category[$cat_id]['desc'] = $desc;
					}
					else
						$bookmarks[$tmp_id][$count -1]['desc'] = $desc;
				}
			}
		}
		// Now lets start the deal of importing these buggers.
		
		$db_id = array ();
		if(!empty($category))
		{
			foreach ($category as $id => $cat)
			{
				if (!defined ('DRY_RUN'))
					$child = (($cat['child'] != 0) ? $db_id[$cat['child']] : 0);
				else
					$child = 0;
				$cat['name'] = addslashes ($cat['name']);
				$Query = "INSERT INTO " . TABLE_PREFIX . "folders
									(pid, title, description, name, ADD_DATE)
									VALUES ('{$child}', '{$cat['name']}', '{$cat['desc']}', '$username', now())";
	
				//check if duplicate mode is on and remove duplicate folder
				if (defined ('DUPLICATE'))
				{
					$checkdupQuery = ("delete from " . TABLE_PREFIX . "folders where title = '{$cat['name']}' and pid = '{$child}' and name='$username'");
					$dbResult = $dblink->exec($checkdupQuery);
						//TODO handle error : or die (T_("Error removing duplicate folder") . ".");
					$nbDupFolders++;
				}
	
				if (!defined ('DRY_RUN'))
				{
					$dbResult = $dblink->exec($Query);
						//TODO handle error :or die ("Error creating folders: {$Query}");
					$db_id[$id] = $dblink->lastInsertID(TABLE_PREFIX . "folders", 'ID');
				}
				else
					print 'The query to add a folder: '. $Query .'<br>';
			}
		}
		
		if(!empty($bookmarks))
		{
			$Query = "INSERT INTO " . TABLE_PREFIX . "favourites (folderid, title, url, description, ADD_DATE, LAST_MODIFIED, name) VALUES (?, ?, ?, ?, ?, ?, '$username');";
	
			foreach ($bookmarks as $id => $bookmark_row)
			{
				foreach ($bookmark_row as $bookmark)
				{
					if (!defined ('DRY_RUN'))
						$tmp = (($id != 0) ? $db_id[$id] : 0);
					else
						$tmp = 0;
	
					//check if duplicate mode is on and remove duplicate favourite
					if (defined ('DUPLICATE'))
					{
						//$checkdupQuery = sprintf("delete from " . TABLE_PREFIX . "favourites where url = %s and folderid = '{$tmp}' and name = %s", quote_smart($bookmark['url']), quote_smart($username));
						$checkdupQuery = ("delete from " . TABLE_PREFIX . "favourites where url = '{$bookmark['url']}' and folderid = '{$tmp}' and name = '$username'");
	
						$dbResult = $dblink->exec($checkdupQuery);
							//TODO handle error : or die ('<p class=\"error\">" . T_("Error removing duplicate bookmark") . "</p>');
						$nbDupBooks++;
					}
					// Cut data to respect maximum length
					if(!empty($bookmark['title']))
						$bookmark['title'] = substr($bookmark['title'], 0, 100);
					if(!empty($bookmark['desc']))
						$bookmark['desc'] = substr($bookmark['desc'], 0, 150);
					$bookmark['add_date'] = date("Y-m-d H:i:s", $bookmark['add_date']);
					$bookmark['last_modified'] = date("Y-m-d H:i:s", $bookmark['last_modified']);
					
					$dataBookmarks[] = array($tmp, $bookmark['title'], $bookmark['url'], $bookmark['desc'], $bookmark['add_date'], $bookmark['last_modified']);
				}
			}
		
			//$Query = substr ($Query, 0, strlen ($Query) -1);
			if (!defined ('DRY_RUN'))
			{
				//echo($Query);
				$dblink->loadModule('Extended', null, false);
				$sth = $dblink->prepare($Query);
				$res = $dblink->extended->executeMultiple($sth, $dataBookmarks);
				$sth->free();
				if (PEAR::isError($res)) {
					echo("<p class=\"error\">" . T_("Error when importing the bookmarks") . "</p>");
					echo '<!--ERROR: '. $res->getMessage(). ' :: ' . $res->getUserInfo() . '-->';
				}
				else
				{
					echo("<p class=\"success\">" . T_("You bookmarks and folders have been imported") . "!</p>\n<p><a href=\"books.php\">" . T_("Click here to see your bookmarks") . ".</a></p>\n");
					if (!empty ($_POST['duplicate']))
					{
						echo("<p>" . sprintf(T_("Removal of duplicates: %s folders and %s bookmarks updated"),$nbDupFolders,$nbDupBooks) . ".</p>\n");
						$nbDupFolders = 0;
						$nbDupBooks = 0;
					}
					//Store the import date in table bookexportimport
					$Query = "INSERT INTO " . TABLE_PREFIX . "bookexportimport ( Name , Method , Time , IP ) values ('$username', 'IM', now(), '" . $_SERVER['REMOTE_ADDR'] ."')";
					//echo("$Query<br>\n");
					$dbResult = $dblink->exec($Query);
					$success = true;
				}
	
			}
			else //Debugging message, no need to translate!
				print 'Now you have to remember that we don\'t have the relationship id\'s in controll, that we do in the real run.<br><br>'. $Query;
		}
		else
			echo("<p class=\"error\">" . T_("Error when importing the bookmarks") . "</p><p>" . T_("Make sure the file is the one containing the bookmarks") . ".</p>");
	}
	if(!$success) {?>
		<div class="content">
			<b><?php echo T_("Select the file you want to import your bookmarks from");?>:</b>
			<br><br>
			<form method="post" action="netscape_import.php" enctype="multipart/form-data">
				<input type="file" name="import" size="30" class="formtext" onfocus="this.select()" /><br>
				<span class="formsLabel"><?php echo T_("Remove duplicates");?></span>
				<input type="checkbox" name="duplicate"><br><br>
				<input type="submit" value="<?php echo T_("Import");?>" class="genericButton">
			</form>
		</div>
<?php }
} ?>
<?php include('footer.php'); ?>