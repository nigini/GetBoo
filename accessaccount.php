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
	include('access.php');
	$access = checkAccess('a');
	if($access)
	{
		$uname = $_POST["name"];

		$user = new User();
		$username = $user->getUsername();

		if($uname != null && $user->isAdmin())
		{
			if($username == $uname && isset($_SESSION["oldname"]))
			{
				$adminName = $_SESSION["oldname"];
				session_unregister('oldname');
				$user->updateLastVisit(true);
				$user->changeUsername($adminName);
				
				include('header.php');
				echo("<h2>" . T_("Manage Users") . " -- " . T_("Access Accounts") . "</h2>");
				echo("<p class=\"success\">" . T_("You are now back into your account") . ".</p>");
				if($user->isLoggedIn())
					$user->updateLastVisit();
			}
			else
			{
				if($_SESSION["oldname"] == null)
					$_SESSION["oldname"] = $username;
				$user->changeUsername($uname);
				if($user->isLoggedIn())
					$user->updateLastVisit();
				$_SESSION['login_msg'] = sprintf(T_("You are now in %s's account.</p><p>Access this user again to get back into your admin account"),$uname);
				header('Location: books.php');
			}
		}
		else
		{
			include('header.php');
			echo("<h2>" . T_("Manage Users") . " -- " . T_("Access Accounts") . "</h2>");
			echo("<p class=\"error\">" . T_("Cannot access the user's account") . ".</p>");
		}
	}
?>
<?php include('footer.php'); ?>