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

	function checkAccess($access='n', $mute='f', $target = '', $load_header = false)
	{
		global $SETTINGS;
		
		$granted = false;
		if(empty($user))
		{
			require_once('includes/user.php');
			$user = new User();
		}


		if($access == 'a')
			$granted = ($user->isLoggedIn() && $user->isAdmin());
		else
			$granted = ($user->isLoggedIn());

		if(!$granted)
		{
			if($mute == 'f')
			{
				if($load_header)
					require_once("header.php");
				$loginLink = $SETTINGS['path_mod'] . "login.php";
				if($target != "")
					$loginLink .= "\" target=\"_$target";
				if($access=='n')
					echo("<h2>" . T_("Protected Area") . "</h2>\n<p class=\"error\">" . T_("You are not logged in, or your session has expired") . ".</p><p>"
					 . T_("Only registered users can access this page") . ".<br>\n"
					. sprintf(T_("Please <a href=\"%s\">login</a> into your account from the login page"), $loginLink) . ".</p>\n");
				else
					echo("<h2>" . T_("Protected Area") . "</h2>\n<p class=\"error\">" . T_("This is a protected area") .
					".</p><p>" . T_("Only admin users can access this page") . ".<br>\n"
					. sprintf(T_("Please <a href=\"%s\">login</a> into your account from the login page"), $loginLink) . ".</p>\n");
			}
		}
		return $granted;
	}
?>
