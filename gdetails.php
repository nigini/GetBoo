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
include('header.php'); ?>
<?php
	include('access.php');
	$access = checkAccess();
	if($access)
	{
		include('gheader.php');

		if (isset($_POST['group_id']))
		{
			 $group_id = $_POST['group_id'];
		}

		$user = new User();
		$username = $user->getUsername();

		include('includes/groups_functions.php');
		include('conn.php');
		include('includes/gdetails_body.php');


		// Show the join button if the user is not subscribed to this group yet,
		// else display the button to view or unsubscribe from this group
		// Check if the user is already part of the group

		$partOf = (checkIfManager($group_id, $username) || checkIfMember($group_id, $username));
		$onlyMember = checkIfMember($group_id, $username);

		echo("<br><br>\n");
		if(!$partOf)
		{
?>
	<form action="gjoin.php" method="post">
	<input type="hidden" name="group_id" value="<?php echo("$rec_id");?>">
	<input type="submit" value="<?php echo T_("Join this group");?>" class="genericButton">
	</form>
<?php
		}
		elseif($onlyMember)
		{
?>
	<form action="gunsubs.php" method="post">
	<input type="hidden" name="group_id" value="<?php echo("$rec_id");?>">
	<input type="submit" value="<?php echo T_("Unsubscribe from this group");?>" class="genericButton">
	</form>
<?php
		}
		echo("</div>\n");
	}
?>
<?php include('footer.php'); ?>