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

ob_start();?>
<?php
	require_once("config.inc.php");
	$customTitle = T_("Add a bookmark");
	require_once('header.php'); ?>
<?php
	include('access.php');

	$access = checkAccess('n', 't');
	if(isset($_GET["g_title"]) && isset($_GET["g_url"]))
	{
		$title = $_GET["g_title"];
		$url = $_GET["g_url"];
		if(isset($_GET["g_desc"]))
			$description = $_GET["g_desc"];
		$title = stripslashes($title);
		$url = stripslashes($url);
		$description = stripslashes($description);
	}
	else if($_SESSION['g_title'] != null && $_SESSION['g_url'] != null)
	{
		$title = $_SESSION['g_title'];
		$url = $_SESSION['g_url'];
		$description = $_SESSION['g_desc'];
	}

	if(isset($_GET["popup"]))
		$popup = true;
	else if($_SESSION['popup'] != null)
		$popup = true;
	else
		$popup = false;

	$formEB = ($title != null) ? true : false;

	if($access)
	{
		echo("<h2>" . T_("Add a bookmark") . "</h2>");
		$user = new User();
		$username = $user->getUsername();
		include('includes/folders.php');

		$success = false;

		if ($_POST['submitted'])
		{
			$tokenError = "";
			// Retrieve token
			if(!(isset($_SESSION['security_token']) && $_SESSION['security_token'] != ""))
				$tokenError = T_("A session security token is missing");
			else if(!(isset($_POST["token"]) && $_POST["token"]!= ""))
				$tokenError = T_("A form security token is missing");
			else if($_POST["token"] != $_SESSION['security_token'])
				$tokenError = T_("The security token is invalid");
			$_SESSION['security_token'] = null;		
			
			if(!$tokenError)
			{
				if(isset($_POST["easybook"]) && $_POST["easybook"] == "1")
				{
					$easybook = true;
					$popup = $_POST["popup"];
				}
				else
					$easybook = false;
	
				//Retrieve vars from POST since the form has been submitted
				$title = $_POST["title"];
				$description = $_POST["description"];
				$url = $_POST["url"];
	
				if(TAGS)
				{
					if (!empty ($_POST['publicChk']))
					{
						$newPublic = true;
					}
					else
						$newPublic = false;
	
					$tags = $_POST["tags"];
				}
	
				$folderid = $_POST["folderid"];
	
				//New folder vars
				$newfoldertitle = $_POST["newfoldertitle"];
				$newfolderdesc = $_POST["newfolderdesc"];
	
				if($folderid != null && $title != null && $url != null)
				{
					//Make sure the url starts with http:// in the event the user's browser doesn't support javascript
					if (strpos($url, ':') === false)
					{
						$url = 'http://'. $url;
					}
					include("includes/bookmarks.php");
					$result = b_url_exist($url,$username);
					
					if(!$result['exists'])
					{
						include('conn.php');
						include('includes/protection.php');
	
						//Check if we need to add a folder and that the parent folder is not a group folder
						if($newfoldertitle != null && !isGroupFolder($folderid))
						{
							$newfoldertitle = filter($newfoldertitle);
							$newfolderdesc = filter($newfolderdesc);
							
							// Cut data to respect maximum length
							$newfoldertitle = substr($newfoldertitle, 0, 30);
							$newfolderdesc = substr($newfolderdesc, 0, 150);
							
							$Query = sprintf("INSERT INTO " . TABLE_PREFIX . "folders (Name , Title , Description , PID) " . "values('" . $username . "', %s, %s, " . $folderid . ") ", quote_smart($newfoldertitle), quote_smart($newfolderdesc));
							//echo($Query . "<br>\n");
							$AffectedRows = $dblink->exec($Query);
							$folderid = $dblink->lastInsertID(TABLE_PREFIX . "folders", 'ID');
						}
	
						if($title != null)
							$title = filter($title);
						if($description != null)
							$description = filter($description);
						if($url != null)
							$url = filter($url);
	
						//add bookmark
						$resultArr = add_bookmark($username, $title, $folderid, $url, $description, $tags, $newPublic);
						$success = $resultArr['success'];
						if($success)
						{
							if($easybook)
							{
								$Query = "
												INSERT INTO " . TABLE_PREFIX . "ebhints
												( Popup , Name , Time , IP )
												VALUES
												('$popup', '$username', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "')
												";
								$dbResult = $dblink->query($Query);
								
		
								//Clean up session data
								$_SESSION['g_title'] = null;
								$_SESSION['g_url'] = null;
								$_SESSION['g_desc'] = null;
								$_SESSION['popup'] = null;
		
								if($popup)
								{
									echo("<p class=\"success\">" . T_("The bookmark was added") . ".</p>");
									//Insert javascript to close the window
									echo ("<script language=\"javascript\"> setTimeout(\"window.close()\", 1500); </script>");
								}
								else
									header("Location: " . $url);
							}
							else
								header("Location: books.php");
						}
						else
							echo("<p class=\"error\">" . T_("There has been a problem when adding the new bookmark") . ".</p>");
					}
					else
					{
						echo("<p class=\"error\">" . T_("A bookmark with the same url has already been submitted") .
						".</p><div style=\"margin-left: 2em; padding-bottom: 2em;\"><form action=\"modifyfav.php\" method=\"post\">" .
						"<input type=\"hidden\" name=\"id\" value=\"" . $result['bId'] . "\" /><input type=\"hidden\" name=\"pid\" value=\"" . $result['folderId'] . "\" />" .
						"<input type=\"submit\" class=\"genericButton\" value=\"" . T_("Edit Original Bookmark") . "\" /></form></div>");
					}
				}
				else
				{
					echo("<p class=\"error\">" . T_("The form is incomplete") . "</p>");
				}
			}
			else
			{
				echo("<p class=\"error\">$tokenError</p>");
			}
		}

		if(!$success)
		{
			// Generate security token
			$token = md5(uniqid(rand(), true)); 
			$_SESSION['security_token'] = $token; 
?>
<script type="text/javascript">
window.onload = function() {
    document.getElementById("url").focus();
}
</script>
<form action="add.php" method="post">
<input type="hidden" name="easybook" value="<?php echo("$formEB"); ?>">
<input type="hidden" name="popup" value="<?php echo("$popup"); ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>" /> 
<table>
	<tr>
		<td><span class="formsLabel"><?php echo T_("Folder");?></span></td>
		<td><?php folders_dropdown($username, "folderid");?></td>
	</tr>
	<tr>
		<td colspan="2">
			<table style="background-color: white; border:1px #26a solid; margin: 0; padding: 0;">
				<tr>
					<td colspan="2"><?php echo T_("Optional - Add a new folder under the previous one");?></td>
				</tr>
				<tr>
					<td><span class="formsLabel"><?php echo T_("New Folder");?></span></td>
					<td><input type="text" name="newfoldertitle" size="30" maxlength="30" class="formtext" onfocus="this.select()" value="<?php echo("$newfoldertitle"); ?>" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('30 <?php echo T_("chars max, folder added under parent folder");?>');" onmouseout="return nd();">?</b></td>
				</tr>
				<tr>
					<td><span class="formsLabel"><?php echo T_("Description");?></span></td>
					<td><input type="text" name="newfolderdesc" size="75" maxlength="150" class="formtext" onfocus="this.select()" value="<?php echo("$newfolderdesc"); ?>" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo T_("chars max, description of the new folder");?>');" onmouseout="return nd();">?</b></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><span class="formsLabel"><?php echo T_("Url");?></span></td>
		<td><input type="text" name="url" id="url" size="75" class="formtext" onfocus="this.select()" value="<?php echo("$url"); ?>" <?php if(CURL_AVAILABLE) {?>onblur="checkurl(this)"<?php }?> />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('<?php echo T_("No chars limit");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
		<td><span class="formsLabel"><?php echo T_("Title");?></span></td>
		<td><input type="text" name="title" id="title" size="75" maxlength="100" class="formtext" onfocus="this.select()" value="<?php echo("$title"); ?>" onkeypress="this.style.backgroundImage = 'none';" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('100 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
		<td><span class="formsLabel"><?php echo T_("Description");?></span></td>
		<td><input type="text" name="description" size="75" maxlength="150" class="formtext" onfocus="this.select()" value="<?php echo("$description"); ?>" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
	</tr>
<?php
			if(TAGS)
			{
?>
	<tr>
		<td><span class="formsLabel"><?php echo T_("Public");?></span></td>
		<td><input type="checkbox" name="publicChk" id="tagCheck" onClick="activateTags(this);" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('<?php echo sprintf(T_("For new users, they will appear in recent bookmarks %s days after the date you registered"),PUBLIC_TIMEOUT);?>');" onmouseout="return nd();">?</b></td>
	</tr>
	<tr>
		<td><span class="formsLabel"><?php echo T_("Tags");?></span></td>
		<td><input type="text" name="tags" id="tags" size="75"  maxlength="150" value="<?php echo("$tags"); ?>" class="formtext" readonly="readonly" />&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo sprintf(T_("chars max, %s chars per tag max. Tags are keywords"),"30");?>.');" onmouseout="return nd();">?</b> <?php echo T_("space separated");?></td>
	</tr>
<?php
			}
?>
	<tr>
		<td></td>
		<td><input type="submit" name="submitted" value="<?php echo T_("Add Bookmark");?>" class="genericButton" /></td>
	</tr>
</table>
</form>
<?php
			if(TAGS)
			{
				include('includes/dynamicTags.php');
			}
		}
	}
	else
	{
		$_SESSION['g_title'] = $title;
		$_SESSION['g_url'] = $url;
		$_SESSION['g_desc'] = $description;
		$_SESSION['popup'] = $popup;
		session_write_close();
		header("Location: login.php");
	}
	ob_end_flush();
?>

<?php include('footer.php'); ?>