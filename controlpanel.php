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
	require_once("config.inc.php");
	$customTitle = T_("My Settings");
	require_once('header.php'); ?>
<?php
	include('access.php');
	$access = checkAccess();
	if($access)
	{
		$user = new User();
		$username = $user->getUsername();
?>
<h2><?php echo T_("Settings");?></h2>
<div class="content">
<?php echo sprintf(T_("Hello <b>%s</b>. You are in your control panel area where you can change your settings"),$username);?><br><br>
<b><?php echo T_("Options");?>:</b><br>
<ul>
<?php if($username != "demo") {?>
<li><a href="changepass.php"><?php echo T_("Change Password");?></a><br><br></li>
<?php } ?>
<li><a href="umodifyaccount.php"><?php echo T_("Modify account information");?></a><br><br></li>
<?php
	if($user->isAdmin())
	{
		?>
</ul>
<b><?php echo T_("Admin Options");?>:</b><br>
<ul>
<li><a href="manageusers.php"><?php echo T_("Manage Users");?></a><br><br></li>
<li><a href="manageconfig.php"><?php echo T_("Manage Configuration");?></a><br><br></li>
<?php if(ANTI_SPAM) {?>
<li><a href="spamcenter.php"><?php echo T_("Spam Center");?></a><br><br></li>
<?php } 
	  if(NEWS) {?>
<li><a href="managenews.php"><?php echo T_("Manage News");?></a><br><br></li>
<?php } ?>
<li><a href="onlineusers.php"><?php echo T_("Online Users");?></a><br><br></li>
</ul>
<br>
<?php
	//Check for the latest version (inspired by phpBB) if not GetBoo

	$localhost = strpos($pathStr, "/gb/");
	if(!IS_GETBOO)
	{
		echo "<b>" . T_("Version Check") . "</b><br>";
		$current_version = explode('.', VERSION);
		$minor_revision = (int) $current_version[1];

		$errno = 0;
		$errstr = $version_info = '';

		if ($fsock = @fsockopen('www.getboo.com', 80, $errno, $errstr, 10))
		{
			@fputs($fsock, "GET /latest-version.txt HTTP/1.1\r\n");
			@fputs($fsock, "HOST: www.getboo.com\r\n");
			@fputs($fsock, "Connection: close\r\n\r\n");

			$get_info = false;
			while (!@feof($fsock))
			{
				if ($get_info)
				{
					$version_info .= @fread($fsock, 1024);
				}
				else
				{
					if (@fgets($fsock, 1024) == "\r\n")
					{
						$get_info = true;
					}
				}
			}
			@fclose($fsock);

			$version_info = explode("\n", $version_info);
			$latest_head_revision = (int) $version_info[0];
			$latest_minor_revision = (int) $version_info[1];
			$latest_version = (int) $version_info[0] . '.0.' . (int) $version_info[1];
			
			$version = $latest_head_revision.".0.".$minor_revision;

			if ($latest_head_revision == 1 && $minor_revision == $latest_minor_revision)
			{
				$version_info = '<p style="color:green">' . T_("Your installation is up to date") . '!</p>';
			}
			else
			{
				$version_info = '<p style="color:red">' .  sprintf(T_("Your installation does not seem to be up to date (you currently use version %s)"), $version) . '.';
				$version_info .= '<br>' . sprintf(T_("You can visit <a href=\"%s\">SourceForge.net project's page</a> to download and upgrade to the latest version (%s)"),"https://sourceforge.net/projects/getboo", $latest_version) . '.</p>';
			}
		}
		else
		{
			if ($errstr)
			{
				$version_info = '<p style="color:red">Connection socket error</p>';
			}
			else
			{
				$version_info = '<p>Sockets functions disabled</p>';
			}
		}
		echo($version_info);
	}
	// End of version check

	}
	else if($username != "demo")
	{?>
<li><a href="deleteaccount.php"><?php echo T_("Delete Account");?></a><br><br></li>
</div>
</ul>
<?php
	}
echo("</div>\n");
} ?>
<?php include('footer.php'); ?>