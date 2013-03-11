<?php include('header.php'); ?>
<?php
	/* Page used for the tags, to display all of them
	 *	Started on 26.01.06
	 * TODO
	 * Consider caching this page, since it is very slow to load when having lots of tags!
	 */

	include('conn.php');
	include('includes/tags_functions.php');

	$sortOrder = "";
	if (isset($_GET['sortOrder']))
	{
	    $sortOrder = $_GET['sortOrder'];
	}
	include('includes/protection.php');
	remhtml($sortOrder);

	$userName = "";
	if (isset($_GET['uname']))
	{
	    $userName = $_GET['uname'];
	}
	remhtml($userName);
	if($userName != "")
	{
		$userStr = "&amp;uname=" . $userName;
		$current_page = "userb.php?uname=" . $userName . "&amp;tag=";
	}
	
	$user = new User();
	$username = $user->getUsername();
		
	if (USECACHE) {
		require_once('includes/cache.php');
		$cache =& Cache::getInstance(CACHE_DIR);
		// Generate hash for caching on
		$hashtext = $_SERVER['REQUEST_URI'];
		if ($user->isLoggedIn()) {
			$hashtext .= $user->getUsername();
		}
		$hash = md5($hashtext);
	
		// Cache for 180 minutes
		$cache->Start($hash, 7200);
	}

	//Display all the tags
	echo("<h2>" . T_("All tags") . "");
	if($userName)
		echo(" -- " . $userName);
	echo("</h2>");

	if($current_page != "")
		$strPopular = displayPopularTagsCloud(tagCloud(getPopularTags(-1, $userName), 5, 90, 225, $sortOrder), $current_page);
	else
		$strPopular = displayPopularTagsCloud(tagCloud(getPopularTags(-1, $userName), 5, 90, 225, $sortOrder));

	if($strPopular != "")
		echo("<p class=\"tags\">" . $strPopular . "</p>");
?>
<p id="sort">
 <?php echo T_("Sort by");?>:    <a href="?sortOrder=alphabet<?php echo $userStr;?>"><?php echo T_("Alphabet");?></a><span> / </span>

 <a href="?sort=popularity<?php echo $userStr;?>"><?php echo T_("Popularity");?></a>
</p>
<?php 
	include('publicfooter.php'); 
	if (USECACHE) {
	    // Cache output if existing copy has expired
	    $cache->End($hash);
	}
?>