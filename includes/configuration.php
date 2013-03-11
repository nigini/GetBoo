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
class Configuration
{
  	public static function LoadConfig($strConfig = "all", $path = "", $dblink = null)
  	{
  		if($dblink == null) {
  			if(!empty($path))
  				include($path . "conn.php");
  			else
  				include(ABSPATH . "conn.php");
		} 

		if($strConfig != "all")
			$arrConfig = explode(",",$strConfig);
		else if(defined("WEBSITE_NAME"))
			return null;
		$query = "SELECT config_name, config_value FROM " . TABLE_PREFIX . "configs";
		if($strConfig != "all")
		{
			$query .= " WHERE";
			foreach ($arrConfig as $intKey => $ConfigName)
				$query .= " config_name = '$ConfigName' OR";
			$query = substr($query,0,-3);
		}
		//echo($query . "<br>\n");
		$result = $dblink->query($query);

		$result_data = array();
		
		if (PEAR::isError($result)) {
			header("Location: error.php");
		}

   		while ($row =& $result->fetchRow (DB_FETCHMODE_ASSOC)) {
     			//printf ("%s, %s\n", $row['config_name'], $row['config_value']);
			$result_data[$row['config_name']] = $row['config_value'];
		}

		return $result_data;
  	}

  	public static function SetConfig($strConfig, $strValue, $path = "")
  	{
  		include($path . "conn.php");
  		require_once("protection.php");
  		$Query = sprintf("UPDATE " . TABLE_PREFIX . "configs SET config_value = %s WHERE config_name = '$strConfig'", quote_smart($strValue));
  		//echo($Query . "<br>\n");
  		$AffectedRows = $dblink->exec($Query);
		return ($AffectedRows == 1 || $AffectedRows == 0);
  	}
}
?>
