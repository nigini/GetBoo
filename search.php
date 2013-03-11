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
// TODO: Remaster the search interface to account for public bookmarks info (tags)
include('header.php'); ?>
<?php

//####################################################################
// Active PHP Bookmarks - lbstone.com/apb/
//
// Filename: search.php
// Authors:  L. Brandon Stone (lbstone.com)
//           Nathanial P. Hendler (retards.org)
//	Edited by: Maxime Chartrand-Dumas (getboo.com)
//	2005-04-26 01:06
//
// 2001-10-28 00:04     Starting on search for version 1.0 (NPH)
//
//####################################################################

$keywords = false;

include('access.php');
$access = checkAccess();
if($access)
{
	$name = $user->getUsername();

	include('conn.php');
	// Clean up the data that's been passed to us [LBS 20020211].

	if (isset($_POST['keywords']))
	{
		 $keywords = $_POST['keywords'];
	}

	if($keywords != null)
	{
		$keywords = trim($keywords);
		$keywords = preg_replace("/ +/", " ", $keywords);
		include('includes/protection.php');
		remhtml($keywords);
		$columns       = array('b.url', 'b.description', 'b.title');
		$group_columns = array('g.title', 'g.description');

		$words = split(" ", $keywords, 8);

	    $query = "
	        SELECT count(*) as total
	          FROM " . TABLE_PREFIX . "favourites b
	         WHERE b.name = '" . $name . "'";
	    //echo "<pre>$query</pre>";
		$result = $dblink->query($query);

		$row =& $result->fetchRow (DB_FETCHMODE_ASSOC);
		$total_bookmarks = $row['total'];

		foreach ($words as $search_string) {

			// This doesn't do anything helpful yet...
			if (preg_match("/^-/", $search_string))
			{
			  echo "<b>" . T_("Invalid") . "</b>: $search_string<br>\n";
			}

			//Store the keywords for the user in table searches
			$domain = $_SERVER['REMOTE_ADDR'];
			$Query = "INSERT INTO " . TABLE_PREFIX . "searches  (Name ,Keyword ,Time ,IP) values ('$name', '$search_string', now(), '$domain')";
			//echo("$Query<br>\n");
			$dbResult = $dblink->query($Query);
	
			 /******************************/
			 /* Look for Groups that Match */

			 foreach ($group_columns as $column) {
				  $query = "
						SELECT g.id, g.title
						  FROM " . TABLE_PREFIX . "folders g
						 WHERE ($column LIKE '%$search_string%')
							AND g.name = '" . $name . "'";

				  $result = $dblink->query($query);

				  $results = array ();
				  $total_rows = $result->numRows();
				  $keyword = false;
				  $group_results = array ();

				  while ($row =& $result->fetchRow (DB_FETCHMODE_ASSOC)) {
						$mod = 1;
						#$group_results[$row[group_id]] += (( 2 * ( 100 - (($total_rows/$total_groups) * 100) ) ) / $mod);
						if (empty ($group_results[$row['id']])) {
							 $group_results[$row['id']] = 0;
						}
						$group_results[$row['id']]++;
				  }
			 }

			 /*********************************/
			 /* Look for Bookmarks that Match */

			 foreach ($columns as $column) {
				  $Query = "
						SELECT b.id as bid, b.description, b.url, g.title
						  FROM " . TABLE_PREFIX . "favourites b
								 LEFT JOIN " . TABLE_PREFIX . "folders g ON (g.id = b.folderid)
						 WHERE ($column LIKE '%$search_string%')
							AND b.name = '" . $name . "'";

				  $result = $dblink->query($Query);

				  $total_rows = $result->numRows();

				while ($row =& $result->fetchRow (DB_FETCHMODE_ASSOC)) {
						if ($column == 'b.url') {
							 $mod = 1.5;
						} else {
							 $mod = 1;
						}
						if (empty ($results[$row['bid']])) {
							 $results[$row['bid']] = 0;
						}
						$results[$row['bid']] += (( 2 * ( 100 - (($total_rows/$total_bookmarks) * 100) ) ) / $mod);
				  }
			 }
		}

		$number_of_results = count($results) + count($group_results);
	}
	include('bheader.php');
	if($keywords != null)
	{
		echo("<br><b>" . T_("Search") . " -- " . T_("Results") . "</b>");
		if ($number_of_results == 0) { $number_of_results = "" . T_("No") . ""; }
		$resultsStr = T_ngettext('result', 'results', $number_of_results);

		// Added all the entities stripslashes stuff to the search results. [LBS 20020211]
		echo "<p><b>$number_of_results</b> $resultsStr " . T_("for") . " \"<b>".htmlentities(stripslashes($keywords))."</b>\"";
	}
	else
	{
		echo("<br><b>" . T_("Search in your own bookmarks") . "</b>");
	}
	?>
		<!-- Search Box -->

		<p>
		<form method="post">
		<input type='hidden' name='action' value='search' />
		<input name='keywords' value="" size="30" value="<?php echo htmlentities(stripslashes($keywords)) ?>" class="formtext" onfocus="this.select()" />
		<input type='submit' name='Submit' class='genericButton' value='<?php echo T_("Search");?>' />
		</form>
	<?php
	if($keywords != null)
	{
		include('includes/folders.php');
		include('includes/bookmarks.php');

		//echo "<p><table align='left' cellpadding='0' cellspacing='0' border='0'><tr><td>\n";

		if ($group_results)
		{

			echo "<p><b>" . T_("Folder Matches") . "</b></p>\n\n";

			while(list($id, $score) = each ($group_results))
			{
				$path = get_group_path($id, $name);
				echo($path);
			}

		}

		if ($results)
		{
			arsort($results);
			reset($results);

			echo "<p><b>" . T_("Bookmark Matches") . "</b></p>\n\n";

			echo "<ul>";
			while(list($id, $score) = each ($results))
			{
				//$b = apb_bookmark($id);
				//$g = apb_group($b->group_id());
				$folderid = get_bfolderid($id, $name);
				$path = get_group_path($folderid, $name, false);
				if($path == "")
					$path = "Main";
				echo "<li>";
				#echo "<tt>[$score]</tt> ";
				$btitle = get_btitle($id, $name);
				echo "<a href=\"redirect.php?id=$id\" target=\"blank\">$btitle</a>" . " <div>" . $path  . "</div></li> ";
				$desc = get_bdescription($id, $name);
				if ($desc) {
					echo " - ". $desc;
				}
				echo "\n";
			}
			echo "</ul>";
		}
	}
}
?>
<?php include('footer.php'); ?>
