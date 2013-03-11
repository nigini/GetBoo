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

require_once($SETTINGS['path_mod'] . "includes/user.php");
$user = new User();

if($user->isLoggedIn())
{
	$user->logout();

	if(!$user->isLoggedIn(true))
	{
		//Save logged out message in session
		session_start();
		$_SESSION['logout_msg'] = T_("You have been successfully logged out") . "!";
		header('Location: index.php');
	}
	else
	{
		include('header.php');
		echo "<h2>Log Out</h2>";
		echo "<p class=\"error\">" . T_("The session is still active") . ".</p>";
	}
}
else
{
	header('Location:index.php');
	/*echo "<h2>" . T_("Log Out") . "</h2>";
	echo "<p class=\"error\">" . T_("You must first log in in order to be able to log out") . ".</p>";*/
}
include('footer.php');
?>