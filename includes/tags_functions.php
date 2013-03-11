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

	/* Functions used for the tags
	 *	Started on 19.01.06
	 */

	//Return true if the bookmark is public (containing tags)
	function checkIfPublic ($b_id, $path = "")
	{
		include($path . 'conn.php');
		$public = false;
		$Query = "select b_id from " . TABLE_PREFIX . "tags_books where b_id='" . $b_id . "'";
		$dbResult = $dblink->query($Query); 
		return count($dbResult->fetchRow()) == 1;
	}

	//Add all the new tags from a string, separated by spaces
	function addTags ($strTags)
	{
		$nbChars = 0;
		$strTags = trim($strTags);
		$tags = split(" ", $strTags); // Get each single tag

		foreach ($tags as $current_tag)
		{
			// Check max length (30) and if 150 chars have been added
			$nbChars += strlen($current_tag);
			if($nbChars <= 150 && strlen(trim($current_tag)) <= 30)
				$result = addTag($current_tag);
		}
	}

	//Add the new tag into the table Tags
	function addTag ($strTag)
	{
		//Trim the tag
		$strTag = trim($strTag);

		//Check if tag exists before adding
		$exists = tagExists($strTag);

		if(!$exists && $strTag != null)
		{
			include('conn.php');
			//Lower case
			$strTag    = utf8_decode($strTag);
		    $strTag    = strtolower($strTag);
		    $strTag    = utf8_encode($strTag);
			$Query = "INSERT INTO " . TABLE_PREFIX . "tags ( Title , Date_Added ) VALUES ('" . $strTag . "', NOW())";
			$AffectedRows = $dblink->exec($Query);
			
			return $AffectedRows;
		}
		else
			return 0;
	}

	//Return true if the tag already exists
	function tagExists ($strTag, $path = "")
	{
		return returnTagID($strTag, $path) != null;
	}

	//Return the ID of a tag
	function returnTagID ($strTag, $path = "")
	{
		include($path . 'conn.php');
		require_once('protection.php');
		$Query = sprintf("select id from " . TABLE_PREFIX . "tags where title=%s",quote_smart($strTag));
		$dbResult = $dblink->query($Query);
		$row = $dbResult->fetchRow();
		if(count($row) != 1)
			$rec_id = null;
		else
			$rec_id = $row['id'];
						
		return $rec_id;
	}

	//Store all the tags from a string, separated by spaces, to a user's bookmark
	function storeTags ($b_id, $strTags)
	{
		$strTags = trim($strTags);
		$tags = split(" ", $strTags); // Get each single tag

		foreach ($tags as $current_tag)
		{
			$result = storeTag($b_id, $current_tag);
		}
		//Store the date the bookmark was made public
		include('conn.php');
		$Query = "INSERT INTO " . TABLE_PREFIX . "tags_added ( B_ID , Date_Added ) VALUES ('" . $b_id . "', NOW())";
		$AffectedRows = $dblink->exec($Query);
	}

	//Add the new tag into the table tags_books
	function storeTag ($b_id, $strTag)
	{
		//Check if tag exists before storing
		$exists = tagExists($strTag);

		if($exists)
		{
			$tagID = returnTagID($strTag);
			include('conn.php');
			$Query = "INSERT INTO " . TABLE_PREFIX . "tags_books ( B_ID , T_ID , Date_Added ) VALUES ('" . $b_id . "', '" . $tagID . "', NOW())";
			$AffectedRows = $dblink->exec($Query);
			
			return $AffectedRows;
		}
		else
			return 0;
	}

	//Return all the tags of a bookmark in a string, separated by spaces
	function returnAllTags ($b_id, $path = "")
	{
		include($path . 'conn.php');
		$Query = "select title from " . TABLE_PREFIX . "tags t, " . TABLE_PREFIX . "tags_books tb where (t.id=tb.t_id and tb.b_id = '" . $b_id . "')";
		$dbResult = $dblink->query($Query);
		$strTags = "";
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$strTags .= "{$row["title"]}";
			$strTags .= " ";
		}
		
		return $strTags;
	}

	//Return all the tags of a bookmark in an array
	function returnAllTagsArray ($b_id, $path)
	{
		$strTags = returnAllTags($b_id, $path);
		$strTagsArr = explode(' ', trim($strTags));
		return $strTagsArr;
	}

	//Return all the tags of a bookmark in a string, separated by spaces with links
	function returnAllTagsLinks ($b_id, $current_page = "tags.php?tag=", $path = "")
	{
		include($path . 'conn.php');
		$Query = "select title from " . TABLE_PREFIX . "tags t, " . TABLE_PREFIX . "tags_books tb where (t.id=tb.t_id and tb.b_id = '" . $b_id . "')";
		$dbResult = $dblink->query($Query);
		$strTags = "";
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$strTitle = "{$row["title"]}";
			$strTags .= ("<a href=\"" . $current_page . $strTitle . "\">" . $strTitle . "</a> ");
		}
		
		return $strTags;
	}

	//Unstore all the tags for a user's bookmark
	function unstoreTags ($b_id, $path = "")
	{
		include($path . 'conn.php');
		$Query = "delete from " . TABLE_PREFIX . "tags_books where b_id = '" . $b_id . "'";
		$AffectedRows = $dblink->exec($Query);
		$Query = "delete from " . TABLE_PREFIX . "tags_added where b_id = '" . $b_id . "'";
		$dbResult = $dblink->exec($Query);
		$AffectedRows2 = $dbResult;
		
		return ($AffectedRows && $AffectedRows2);
	}

	//Remove the link to a tag in table tags_books for a user's bookmark
	function unstoreTag ($b_id, $strTag)
	{
		//Check if tag exists before unstoring
		$exists = tagExists($strTag);

		if($exists)
		{
			$tagID = returnTagID($strTag);
			include('conn.php');
			$Query = "delete from " . TABLE_PREFIX . "tags_books where b_id = '" . $b_id . "' and t_id = '" . $tagID . "'";
			$AffectedRows = $dblink->exec($Query);
			
			return $AffectedRows;
		}
		else
			return 0;
	}

	//Update all the tags from a string, separated by spaces, to a user's bookmark
	function updateTags ($b_id, $strTags)
	{
		$strTags = trim($strTags);
		$tags = split(" ", $strTags); // Get each single tag

		//Remove tags if not there anymore
		//Get old tags
		$strOldTags = returnAllTags($b_id);
		$oldTags = split(" ", $strOldTags); // Get each single tag
		foreach ($oldTags as $old_tag)
		{
			$found = false;
			foreach ($tags as $new_tag)
			{
				if($old_tag == $new_tag)
				{
					$found = true;
					break;
				}
			}
			if(!$found) // Not there anymore, remove the link
				$result = unstoreTag($b_id, $old_tag);
		}

		$nbChars = 0;
		//Add new tags if any
		foreach ($tags as $current_tag)
		{
			if(tagExists($current_tag))
			{
				$tagID = returnTagID($current_tag);
				if(!returnTagLinked($b_id, $tagID))
					$result = storeTag($b_id, $current_tag);
			}
			else
			{
				// Check max length (30) and if 150 chars have been added
				$nbChars += strlen($current_tag);
				if($nbChars <= 150 && strlen(trim($current_tag)) <= 30)
				{
					$result = addTag($current_tag);
					$tagID = returnTagID($current_tag);
					$result2 = storeTag($b_id, $current_tag);
				}
			}
		}
	}

	//Check if a tag is linked to a bookmark
	function returnTagLinked ($b_id, $t_id)
	{
		include('conn.php');
		$Query = "select * from " . TABLE_PREFIX . "tags_books where (b_id='" . $b_id . "' and t_id='" . $t_id . "')";
		$dbResult = $dblink->query($Query);
		return count($dbResult->fetchRow()) == 1;
	}

	//Return the number of public bookmarks
	function numberOfPublicBookmarks()
	{
		include('conn.php');
		$countB = 0;
		$Query = ("select count(b_id) as total from " . TABLE_PREFIX . "tags_added");
		//echo($Query);
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$countB = "{$row["total"]}";
		}
		
		return $countB;
	}

	//Return the number of public bookmarks for a given tag id
	function numberOfPublicBookmarksTag($tagID)
	{
		include('conn.php');
		$countB = 0;
		$Query = ("select count(b_id) as total from " . TABLE_PREFIX . "tags_books where t_id = " . $tagID);
		//echo($Query);
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$countB = "{$row["total"]}";
		}
		
		return $countB;
	}

	//select count(id) from favourites b, tags_added ta where (b.id = ta.b_id and b.name = 'Maxime')
	//Return the number of public bookmarks for a given tag id
	function numberOfPublicBookmarksUser($username)
	{
		include('conn.php');
		$countB = 0;
		$Query = ("select count(id) as total from " . TABLE_PREFIX . "favourites b, " . TABLE_PREFIX . "tags_added ta where (b.id = ta.b_id and b.name = '" . $username . "')");
		//echo($Query);
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$countB = "{$row["total"]}";
		}
		
		return $countB;
	}

	// Following functions are based on Scuttle code

	function getPopularTags ($size = 30, $userName = "")
	{
		include('conn.php');
		//$tags = array();
		$countMaxTagsDisplay = $size;
		if($userName != "")
			$Query = ("SELECT t.title, count(tb.t_id) as amount from " . TABLE_PREFIX . "tags_books tb, " . TABLE_PREFIX . "tags t, " . TABLE_PREFIX . "favourites f where t.id = tb.t_id and tb.b_id = f.id and f.name = '" . $userName . "' group by tb.t_id, t.title order by amount desc");
		else
			$Query = ("SELECT title, count(tb.t_id) as amount from " . TABLE_PREFIX . "tags_books tb, " . TABLE_PREFIX . "tags t where t.id = tb.t_id group by tb.t_id, t.title order by amount desc");
		if($countMaxTagsDisplay != -1)
			$dblink->setLimit($countMaxTagsDisplay, 0);
			//$Query .= (" limit 0, " . $countMaxTagsDisplay);
		$dbResult = $dblink->query($Query);
		//echo($Query . "<br>");
		$tags = array();
		$count = 0;
		while(($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC)))
		{
			$tags[$count++] = $row;
		}

		return $tags;
	}

	function displayPopularTagsCloud ($output, $current_page = "tags.php?tag=")
	{
		$strResult = "";
		if($output != null)
		{
			foreach($output as $current_row)
			{
				$resultsStr = T_ngettext('bookmark', 'bookmarks', $current_row['amount']);
				$strResult .= "<a href=\"" . $current_page . $current_row['title'] . "\" title=\"" . $current_row['amount'] . " $resultsStr\" style=\"font-size:" . $current_row['size'] . "\">". $current_row['title'] . "</a> \n";
			}
		}
		return $strResult;
	}

	function displayPopularTagsCloudSelect ($output)
	{
		$strResult = "";
		if($output != null)
		{
			foreach($output as $current_row)
			{
				$resultsStr = T_ngettext('bookmark', 'bookmarks', $current_row['amount']);
				$strResult .= "<span title=\"" . $current_row['amount'] . " $resultsStr\" style=\"cursor: pointer; font-size:" . $current_row['size'] . "\" onclick=\"addTag(this)\">". $current_row['title'] . "</span> ";
			}
		}

		return $strResult;
	}

	function tagCloud($tags = NULL, $steps = 5, $sizemin = 90, $sizemax = 225, $sortOrder = NULL)
	{
		if (is_null($tags))
			return false;

		$min = $tags[count($tags) - 1]['amount'];
		$max = $tags[0]['amount'];

		for ($i = 1; $i <= $steps; $i++)
		{
		   $delta = ($max - $min) / (2 * $steps - $i);
		   $limit[$i] = $i * $delta + $min;
		}
		$sizestep = ($sizemax - $sizemin) / $steps;
		foreach ($tags as $row)
		{
		   $next = false;
		   for ($i = 1; $i <= $steps; $i++) {
		       if (!$next && $row['amount'] <= $limit[$i]) {
		           $size = $sizestep * ($i - 1) + $sizemin;
		           $next = true;
		       }
		   }
		   $tempArray = array('size' => $size .'%');
		   $row = array_merge($row, $tempArray);
		   $output[] = $row;
		}

		if ($sortOrder == 'alphabet' && $output != null)
		   usort($output, create_function('$a,$b','return strcasecmp($a["title"], $b["title"]);'));

		return $output;
	}

	function displayRelatedTagsList ($output, $current_tags, $current_page = "tags.php?tag=")
	{
		$strResult = "";
		if($output != null)
		{
			$strResult = "<table class=\"related_tags\">";
			foreach($output as $current_row)
			{
				$resultsStr = T_ngettext('bookmark', 'bookmarks', $current_row['amount']);
				$strResult .= "<tr><td style=\"width: 1em; text-align: center;\"><a href=\"" . $current_page . $current_tags . "+" . $current_row['title'] . "\" title=\"" . T_("Add tag") . "\" alt=\"" . T_("Add tag") . "\">+</a></td><td style=\"margin-left: 0.5em;\"><a href=\"" . $current_page . $current_row['title'] . "\" title=\"" . $current_row['amount'] . " $resultsStr\">". $current_row['title'] . "</a></td></tr>\n";
			}
			$strResult .= "</table>";
		}
		return $strResult;
	}

	function displayRelatedTagsListMinus ($tagName, $current_tags, $current_page = "tags.php?tag=")
	{
		$strResult = "";
		if($current_tags != null)
		{
			$strResult = "<table class=\"related_tags\">";
			$tagName = $tagName . " "; //Fix for the loop after
			foreach($current_tags as $current_row)
			{
				$QueryMinus = "";
				$QueryMinus = str_replace($current_row . " ", "", $tagName);
				$strResult .= "<tr><td style=\"width: 1em; text-align: center;\"><a href=\"" . $current_page . $QueryMinus . "\" title=\"" . T_("Remove tag") . "\" alt=\"" . T_("Remove tag") . "\">-</a></td><td style=\"margin-left: 0.5em;\"><a href=\"" . $current_page . $current_row . "\">". $current_row . "</a></td></tr>\n";
			}
			$strResult .= "</table>";
		}
		return $strResult;
	}

	function getRelatedTags ($tags, $size = 10, $userName = "")
	{
		include('conn.php');
		//$tags = array();
		$countMaxTagsDisplay = $size;

		$tagcount = count($tags);

		$Query1 = ("");
		$Query2 = ("");
		$Query3 = ("");
		$Query4 = ("");

		$Query1 = ("select T0.title, count(b.id) as amount "); //select the fields
		$Query2 = ("from " . TABLE_PREFIX . "favourites as b, " . TABLE_PREFIX . "tags as T0, " . TABLE_PREFIX . "tags_books TB0"); // from fields
		$Query3 = ("where (TB0.b_id = b.id and TB0.t_id = T0.id ");
		$Query4 = (") group by T0.title order by amount desc, T0.title");

		for ($i = 1; $i <= $tagcount; $i ++)
		{
			$Query2 .= (", " . TABLE_PREFIX . "tags as T" . $i . ", " . TABLE_PREFIX . "tags_books as TB" . $i . " ");
			$Query3 .= ("and TB" . $i . ".b_id = b.id and TB" . $i . ".t_id = T" . $i . ".id and T" . $i . ".title = '" . $tags[$i-1] . "' and T0.title <> '" . $tags[$i-1] . "' ");
		}

		if($userName != "")
			$Query3 .= ("and name = '" . $userName . "'"); // Only for a specific user

		$Query = $Query1 . $Query2 . $Query3 . $Query4;

		if($countMaxTagsDisplay != -1)
			$dblink->setLimit($countMaxTagsDisplay, 0);
			//$Query .= (" limit 0, " . $countMaxTagsDisplay);

		$dbResult = $dblink->query($Query);
		//echo($Query . "<br>");
		$related_tags = $dbResult->fetchAll();
		return $related_tags;
	}

	function getRecentTags($minTagsNb, $maxTagsNb, $path = "")
	{
		include($path . 'conn.php');
		
		if(ANTI_SPAM)
		{
			$antiSPAM = " and s.status!='disabled' and s.name!='demo' and ( " . DATE_DIFF_SQL . " s.datejoin)/" . DAY_SECONDS . " >= " . PUBLIC_TIMEOUT . " or s.status='admin'";
			if(IS_GETBOO)
				$antiSPAM .= " or s.donor = 1";
			$antiSPAM .= ")";
		}

		$Query = ("select distinct b.ADD_DATE AS formatted_time, id, title, url, description, b.name from " . TABLE_PREFIX . "favourites b, " . TABLE_PREFIX . "tags_books tb, " . TABLE_PREFIX . "tags_added ta, " . TABLE_PREFIX . "session s where (id = tb.b_id and tb.b_id = ta.b_id and b.name = s.name$antiSPAM) order by b.ADD_DATE desc");// limit " . $minTagsNb . ", " . $maxTagsNb);
		$dblink->setLimit($maxTagsNb, $minTagsNb);
		$dbResult = $dblink->query($Query);
		//var_dump($Query);

		$recent_tags = $dbResult->fetchAll();
		return $recent_tags;
	}

	function getUserBookmarks($username, $minTagsNb, $maxTagsNb, $path = "", $datestart = "", $dateend = "", $url = "")
	{
		include($path . 'conn.php');
		if($url)
			$urlStr = " and b.url = '$url'";
		// Start and end dates
        if ($datestart) {
            $datesStr .= ' and b.add_date > "'. $datestart .'"';
        }
        if ($dateend) {
            $datesStr .= ' and b.add_date < "'. $dateend .'"';
        }	
		
		$Query = ("select b.ADD_DATE AS formatted_time, b.ADD_DATE, id, title, url, description from " . TABLE_PREFIX . "favourites b, " . TABLE_PREFIX . "tags_added ta where (b.id = ta.b_id and b.name = '" . $username . "'$urlStr$datesStr) order by b.ADD_DATE desc");// limit " . $minTagsNb . ", " . $maxTagsNb);
		$dblink->setLimit($maxTagsNb, $minTagsNb);
		$dbResult = $dblink->query($Query);

		$user_books = $dbResult->fetchAll();
		return $user_books;
	}

	function getTagsBookmarks($tagNames, $minTagsNb, $maxTagsNb, $userName = "", $path = "", $datestart = "", $dateend = "", $url = "")
	{
		include($path . 'conn.php');
		$tagcount = count($tagNames);
		
		$doQuery = true; //in case one tag doesn't exists, no need to run the query!

		$Query1 = ("");
		$Query2 = ("");
		$Query3 = ("");
		$Query4 = ("");

		$Query1 = ("select b.ADD_DATE AS formatted_time, b.ADD_DATE, id, title, url, description, name "); //select the fields
		$Query2 = ("from " . TABLE_PREFIX . "favourites as b"); // from fields
		$Query3 = ("where (");
		$Query4 = (") order by b.ADD_DATE desc");// limit " . $minTagsNb . ", " . $maxTagsNb);
		
		for ($i = 0; $i < $tagcount; $i ++)
		{
			$tagID = returnTagID($tagNames[$i], $path);
			if($tagID == null) // tag exists
			{
				$doQuery = false;
				break;
			}
			else
			{
				$Query2 .= (", " . TABLE_PREFIX . "tags_books as tb" . $i . ", " . TABLE_PREFIX . "tags_added as ta" . $i . " ");
				
				if($i > 0)
					$Query3 .= "and ";
				$Query3 .= ("b.id = tb" . $i . ".b_id and tb" . $i . ".b_id = ta" . $i . ".b_id and tb" . $i . ".t_id = '" . $tagID . "' ");
			}
		}
		if($doQuery)
			{
			if($userName != "")
				$Query3 .= ("and name = '" . $userName . "'"); // Only for a specific user
				
			if($url)
				$Query3 .= " and b.url = '$url'";
			// Start and end dates
	        if ($datestart) {
	            $Query3 .= ' and b.add_date > "'. $datestart .'"';
	        }
	        if ($dateend) {
	            $Query3 .= ' and b.add_date < "'. $dateend .'"';
	        }
		
			$Query = $Query1 . $Query2 . $Query3 . $Query4;
			
			$dblink->setLimit($maxTagsNb, $minTagsNb);
			$dbResult = $dblink->query($Query);
			
			$tags_books = $dbResult->fetchAll();
		}
		else
			$tags_books = null;
		return $tags_books;
	}

	function getSearchBookmarks($keywords, $minTagsNb, $maxTagsNb)
	{
		include('conn.php');
		$word = explode(' ', $keywords);
	    $word = array_map('trim', $word);
		$tagcount = count($word);

		$Query1 = ("");
		$Query2 = ("");
		$Query3 = ("");
		$Query4 = ("");

		$Query1 = ("select distinct b.ADD_DATE AS formatted_time, id, title, url, description, name "); //select the fields
		$Query2 = ("from " . TABLE_PREFIX . "favourites as b "); // from fields
		$Query3 = ("where (");
		$Query4 = (") order by b.ADD_DATE desc");// limit " . $minTagsNb . ", " . $maxTagsNb);
			
		$countTable = 0;
		for ($i = 0; $i < $tagcount; $i++)
		{
			if($i > 0)
				$Query3 .= "and (";
			else
				$Query3 .= " (";

			$Query3 .= "(b.title LIKE '%". $word[$i] . "%' ";
			$Query3 .= "OR b.description LIKE '%". $word[$i] . "%') ";

			$tagID = returnTagID($word[$i]);
			if($tagID != "")
			{
				//left join tags_books as tb0 on b.id = tb0.b_id
				$Query2 .= ("left join " . TABLE_PREFIX . "tags_books as tb" . $countTable . " on b.id = tb" . $countTable . ".b_id left join " . TABLE_PREFIX . "tags_added as ta" . $countTable . " on tb" . $countTable . ".b_id = ta" . $countTable . ".b_id ");
				$Query3 .= (" or (tb" . $countTable . ".t_id = '" . $tagID . "')) ");
				$countTable++;
			}
			else
				$Query3 .= ") ";
		}
		//If no tags, then add the table tags_added for the dates
		if($Query2 == "from " . TABLE_PREFIX . "favourites as b ")
			$Query2 .= "left join " . TABLE_PREFIX . "tags_added ta0 on b.id = ta0.b_id ";

		//Make sure its a public tag
		$Query3 .= "and ta0.Date_Added IS NOT NULL";

		$Query = $Query1 . $Query2 . $Query3 . $Query4;
		$dblink->setLimit($maxTagsNb, $minTagsNb);
		$dbResult = $dblink->query($Query);

		$tags_books = array();
		$count = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$tags_books[$count++] = $row;
		}

		return $tags_books;
	}

	function getSingleBookmark($bookmarkID)
	{
		include('conn.php');
		$Query = ("select b.ADD_DATE AS formatted_time, id, title, url, description, name from " . TABLE_PREFIX . "favourites b, " . TABLE_PREFIX . "tags_added ta where (b.id = ta.b_id and b.id = '" . $bookmarkID . "')");
		//echo($Query);
		$dbResult = $dblink->query($Query);

		$user_books = array();
		$count = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$user_books[$count++] = $row;
		}

		

		return $user_books;
	}
	
    function renameTag($username, $old, $new) {
	    //TODO: Complete the function for the API tags_rename
        if (is_null($userid) || is_null($old) || is_null($new))
            return false;

        // Find bookmarks with old tag
        $bookmarksInfo =& $bookmarkservice->getBookmarks(0, NULL, $userid, $old);
        $bookmarks =& $bookmarksInfo['bookmarks'];

        // Delete old tag
        $this->deleteTag($old);

        // Attach new tags
        foreach(array_keys($bookmarks) as $key) {
            $row =& $bookmarks[$key];
            //Add the tags
			addTags($tags);
			//Store the tags with the bookmark
			storeTags($rec_id, $tags);
            $this->attachTags($row['bId'], $new, $fromApi, NULL, false);
        }

        return true;
    }
?>