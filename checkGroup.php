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
include('config.inc.php');
// TODO: Could make it ajax to avoid loading a popup
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">
<head>
<title><?php echo T_("Check Group Name");?></title>
<link rel="stylesheet" type="text/css" href="style.css" media="screen, projection" />
</head>
<body onload="closeWindow()">
<div style="text-align: center">
<br>
<?php
	//Put the javascript to automatically close the window
	echo ("<script type=\"text/javascript\"> function closeWindow() {setTimeout(\"window.close()\", 3000);} </script>");

	$groupToCheck = $_GET['groupToCheck'];
	include("includes/protection.php");
	if($groupToCheck!=null)
		remhtml($groupToCheck);

	if($groupToCheck != null && valid($groupToCheck, 20))
	{
		include('conn.php');
		$Query = ("select group_name from " . TABLE_PREFIX . "groups where (group_name='" . $groupToCheck . "')");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			echo("<b>" . T_("This group name is already taken") . "</b>");
		else
			echo("<b>" . T_("This group name is available") . "!</b>");
	}
	else
		echo("<b>" . T_("Incorrect group format") . "</b>");

?>
<br><br>
<a href="javascript:window.close()" style="font-size: small"><?php echo T_("Close Window");?></a>
</div>
</body>
</html>