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
	function add_bookmark ($uname, $title, $folderid, $url, $description, $tags = "", $newPublic = false, $date = NULL)
	{
		$resultArr = array();
		$resultArr['success'] = false;
		include('conn.php');
		require_once(dirname(__FILE__) .'/protection.php');
		
		if($date != "")
			$date = "'$date'";
		else
			$date = "now()";
			
		// Cut data to respect maximum length
		if(!empty($title))
			$title = substr($title, 0, 100);
		if(!empty($description))
			$description = substr($description, 0, 150);
		
		//$Query = sprintf("INSERT INTO " . TABLE_PREFIX . "favourites (Name , Title , FolderID , Url , Description, ADD_DATE) " . "values('" . $uname . "', %s,'" . $folderid . "', %s, %s, $date) ", quote_smart($title), quote_smart($url), quote_smart($description));
		$Query = ("INSERT INTO " . TABLE_PREFIX . "favourites (Name , Title , FolderID , Url , Description, ADD_DATE) values(?, ?, ?, ?, ?, $date)");
		$sth = $dblink->prepare($Query);
		$dataBookmark = array($uname, $title, $folderid, $url, $description);
		$AffectedRows = $sth->execute($dataBookmark);
		$rec_id = $dblink->lastInsertID(TABLE_PREFIX . "favourites", 'ID');
		
		if (PEAR::isError($AffectedRows)) {
			$resultArr['success'] = true;
			//echo 'ERROR: '. $AffectedRows->getMessage(). ' :: ' . $AffectedRows->getUserInfo();
		}
		else
		{
			$resultArr['success'] = true;

			$tags = trim($tags);
			if(TAGS && $tags != "")
			{
				require_once(dirname(__FILE__) .'/tags_functions.php');
		
				//Remove any commas, dots, quotes, plus signs since the user might use commas to seperate tags rather than spaces
				$toRemove = array('"', "'", ",", "+");
				$tags = str_replace($toRemove, "", $tags);
		
				$tags = filter($tags);
		
				if($tags != null && $newPublic)
				{
					// cut tags if too long > 150 chars
					$tags = substr($tags, 0, 150);
					
					//Add the tags
					addTags($tags);
		
					//Store the tags with the bookmark
					storeTags($rec_id, $tags);
				}
				
				if(USE_SCREENSHOT && CURL_AVAILABLE)
				{
					require_once(dirname(__FILE__) .'/curl.php');
					$newc = new curl();
					$urlScreenshot = sprintf(SCREENSHOT_URL, $url);
					//echo $urlScreenshot;
					$fd = $newc->getFile($urlScreenshot);
				}
			}
		}
		return $resultArr;
	}
	
	function get_bfolderid ($bookmarkid, $uname)
	{
		include('conn.php');
		$Query = ("select folderid from " . TABLE_PREFIX . "favourites where id='$bookmarkid' and name='$uname'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$folderid = "{$row["folderid"]}";
		}
		
		return $folderid;
	}

	function get_bdescription ($bookmarkid, $uname)
	{
		include('conn.php');
		$Query = ("select description from " . TABLE_PREFIX . "favourites where id='$bookmarkid' and name='$uname'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$description = "{$row["description"]}";
		}
		
		return $description;
	}

	function get_btitle ($bookmarkid, $uname="")
	{
		include('conn.php');
		$Query = ("select title from " . TABLE_PREFIX . "favourites where id='$bookmarkid'");
		if($uname != "")
			$Query .= (" and name='$uname'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$title = "{$row["title"]}";
		}
		
		return $title;
	}

	function copy_bookmark($id, $folderid)
	{
		include('conn.php');
		$Query = ("select name, title, description, url from " . TABLE_PREFIX . "favourites where id='" . $id . "'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$username = "{$row["name"]}";
			$title = "{$row["title"]}";
			$description = "{$row["description"]}";
			$url = "{$row["url"]}";
		}
		$Query = "INSERT INTO " . TABLE_PREFIX . "favourites ( Name , Title , FolderID , Url , Description , ADD_DATE ) " . "values('" . $username . "','" . $title . "','" . $folderid . "','" . $url . "','" . $description . "', now()) ";
		//echo($Query . "<br>\n");
		$AffectedRows = $dblink->exec($Query);
		
		return ($AffectedRows == 1);
	}

	function move_bookmark($bookID, $folderID)
	{
		include('../conn.php');
		$Query = "update " . TABLE_PREFIX . "favourites set folderid = '$folderID' where id='$bookID'";
		//echo($Query . "<br>\n");
		$dbResult = $dblink->exec($Query);
		return $dbResult;
	}

	function move_folder($folderFrom, $folderTo, $path = "../")
	{
		include($path . '/conn.php');
		$Query = "update " . TABLE_PREFIX . "folders set pid = '$folderTo' where id='" . $folderFrom . "'";
		//echo($Query . "<br>\n");
		$dbResult = $dblink->exec($Query);
		return $dbResult;
	}

	function delete_bookmark($bookmarkid, $path = "")
	{
		include($path . 'conn.php');
		if(TAGS)
		{
			require_once('tags_functions.php');

			$public = checkIfPublic($bookmarkid, $path);

			if($public)
			{
				//Remove (unstore) all the tags attached to this bookmark in table tags_books
				unstoreTags($bookmarkid, $path);
			}
		}

		$Query = "delete from " . TABLE_PREFIX . "favourites where id='" . $bookmarkid . "'";
		//echo($Query . "<br>\n");
		$AffectedRows = $dblink->exec($Query);
		
		return $AffectedRows;
	}

	function b_exists($bookmarkid)
	{
		include('conn.php');
		$exists = false;
		$Query = ("select title from " . TABLE_PREFIX . "favourites where id='$bookmarkid'");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$exists = true;
		}
		
		return $exists;
	}

	function b_url_exist($url, $username, $id = "")
	{
		include('conn.php');
		$resultArr = array();
		$resultArr['exists'] = false;
		$resultArr['bId'] = "";
		$exists = false;
		$Query = ("select id, folderid from " . TABLE_PREFIX . "favourites where url='$url' and name='$username'");
		if($id != "")
			$Query .= " and id != $id";
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$resultArr['bId'] = "{$row["id"]}";
			$resultArr['folderId'] = "{$row["folderid"]}";
			$resultArr['exists'] = true;
		}
		
		return $resultArr;
	}

	// Returns true if the bookmark belongs to a user
	function b_belongs_to($bookID, $username, $path = "")
	{
		include($path . 'conn.php');
		$belongsTo = false;

		$Query = ("select id from " . TABLE_PREFIX . "favourites where id='$bookID' and name='$username'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$belongsTo = true;
		}
		
		return $belongsTo;
	}

	// Returns true if the folder belongs to a user
	function f_belongs_to($folderID, $username, $path = "")
	{
		include($path . 'conn.php');
		$belongsTo = false;

		$Query = ("select id from " . TABLE_PREFIX . "folders where id='$folderID' and name='$username'");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$belongsTo = true;
		}
		
		return $belongsTo;
	}

	function delete_folder($folderID, $username, $path = "")
	{
		include($path . 'conn.php');
		include($path . 'config.inc.php');
		$resultArr = array();
		$resultArr['message'] = "";
		$resultArr['success'] = false;
		if($folderID != null)
		{
			//delete the whole subtree

			//get all the folder ids in the subtree

			$folderids = array();
			$bookids = array();

			function countfolders($folderid, $uname, &$folderids, &$bookids, $path = "")
			{
				include($path . 'conn.php');
				$Query = ("select id from " . TABLE_PREFIX . "favourites where folderid='$folderid'");
				//echo($Query . "<br>\n");
				$dbResult = $dblink->query($Query);
				while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$rowid = "{$row["id"]}";
					$bookids[] = $rowid;
				}
				$Query = ("select id from " . TABLE_PREFIX . "folders where pid='$folderid'");
				//echo($Query . "<br>\n");
				$dbResult = $dblink->query($Query);
				$nb = 0;
				while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					$rowid = "{$row["id"]}";
					$folderids[] = $rowid;
					$nb++;
					$nb+=countfolders($rowid, $uname, $folderids, $bookids, $path);
				}
				return $nb;
				
			}

			$nbfolders = countfolders($folderID, $username, $folderids, $bookids, $path);

			//delete all the subtree
			foreach($folderids as $folderid)
			{
				$Query = "delete from " . TABLE_PREFIX . "folders where id='" .$folderid . "'";
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows != 1)
				{
					$resultArr['message'] = T_("There has been a problem when deleting a folder");
				}
			}
			foreach($bookids as $bookID)
			{
				if(TAGS)
				{
					require_once($path . 'includes/tags_functions.php');

					$public = checkIfPublic($bookID);

					if($public)
					{
						//Remove (unstore) all the tags attached to this bookmark in table tags_books
						unstoreTags($bookID);
					}
				}

				$Query = "delete from " . TABLE_PREFIX . "favourites where id='" . $bookID . "'";
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows != 1)
				{
					$resultArr['message'] = T_("There has been a problem when deleting a bookmark");
				}
			}

			//delete the folder itself when subtree deleted
			$Query = "delete from " . TABLE_PREFIX . "folders where id='" . $folderID . "'";
			//echo($Query . "<br>\n");
			$AffectedRows = $dblink->exec($Query);
			if($AffectedRows == 1)
			{
				$resultArr['message'] = T_("You have successfully deleted this folder");
				$resultArr['success'] = true;
			}
			else
				$resultArr['message'] = T_("There has been a problem when deleting the folder");
		}
		return $resultArr;
	}

	//Function to display a folder content
	function listFolderBookmarks($folderID, $username = "", $path = "", $edit = false, $target = "content")
	{
		global $style;
		$fields = array('id', 'title', 'url', 'description');
		$where = array('folderid'=>$folderID);
		if($folderID==MAIN_FID)
			$where['name'] = $username;
		$bookmarks = Prototype::queryData($fields, "favourites", $where, false, $path, false, "title");
		foreach($bookmarks as $key=>$bookmark)
		{
			echo("\t\t<li class=\"treeItem bookHover\" id=\"b" . $bookmark['id'] . "\" type=\"book\"><img src=\"" . $path . "images/style/$style/bookmark.GIF\" alt=\"Bookmark\" title=\"Bookmark\" /><a href=\"" . $path . "redirect.php?id=" . $bookmark['id'] . "\" target=\"_$target\" class=\"books_desc\" title=\"" . $bookmark['description'] . "\"> " . $bookmark['title'] . "</a>");
			if($edit)
			{
				echo(" <a href=\"modifyfav.php?id=" . $bookmark['id'] . "\" class=\"editB\"><img src=\"images/books/modifyGray.GIF\" width=\"16\" height=\"16\" alt=\"" . T_("Edit") . "\" title=\"" . T_("Edit") . "\" /></a>");
			?>
			<script type="text/javascript">
				document.write(" <a href=\"#\" onclick=\"deleteBookmark(this, <?php echo $bookmark['id'];?>); return false;\" class=\"deleteB\"><img src=\"images/books/deleteGray.GIF\" width=\"16\" height=\"16\" alt=\"<?php echo T_("Delete");?>\" title=\"<?php echo T_("Delete");?>\" /><\/a>");
			</script>
			<?php
			}
			echo("</li>\n");

		}
	}

	//Function to display a folder content
	function listFolderContent($folderID, $username = "", $path = "", $edit = false)
	{
		global $style;
		$fields = array('id', 'title', 'pid', 'description');
		$where = array('pid'=>$folderID);
		if($folderID==MAIN_FID)
			$where['name'] = $username;
		$folders = Prototype::queryData($fields, "folders", $where, false, $path, false, "title");

		foreach($folders as $key=>$current_folder)
		{
			$fields = array('count(*) as count');
			$where = array('folderid'=>$current_folder['id']);
			//$countFolderBookmarks = Prototype::queryData($fields, "favourites", $where, false, $path, false);

			echo ("\t<li class=\"treeItem folderHover\" id=\"f" . $current_folder['id'] . "\"><img src=\"" . $path . "images/style/$style/folder.GIF\" alt=\"Folder\" title=\"Folder\"/> <span class=\"textHolder\"><span class=\"folder_desc\" title=\"" . $current_folder['description'] . "\">" . $current_folder['title'] . "</span>");
			if($edit)
			{
				echo(" <a href=\"modifyfolder.php?id=" . $current_folder['id'] . "\" class=\"editF\"><img src=\"images/books/modifyGray.GIF\" width=\"16\" height=\"16\" alt=\"" . T_("Edit") . "\" title=\"" . T_("Edit") . "\" /></a>");
			?>
			<script type="text/javascript">
				document.write(" <a href=\"#\" onclick=\"deleteFolder(this, <?php echo $current_folder['id'];?>); return false;\" class=\"deleteF\"><img src=\"images/books/deleteGray.GIF\" width=\"16\" height=\"16\" alt=\"<?php echo T_("Delete");?>\" title=\"<?php echo T_("Delete");?>\" /><\/a>");
			</script>
			<?php
			}
			echo("</span>\n\t\t<ul id=\"f" . $current_folder['id'] . "\" style=\"display: none;\">\n");

			// Retrive sub-folders recusively
			listFolderContent($current_folder['id'], "", $path, $edit);

			// Retrieve folders bookmarks for now
			listFolderBookmarks($current_folder['id'], "", $path, $edit);

			echo("\t\t</ul>\n</li>\n");
		}
	}
?>