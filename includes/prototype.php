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

/*
 * Created on 13-Apr-07
 *
 * Contains VERY useful functions that repeat themselves all the time..!
 * TODO: Develop more functions, such as inserts. Think about using some db abstration layers as in phpBB
 */
class Prototype
{
	public static function queryData($fields, $tableName, $wheres, $echo = false, $pathConnMod = "", $count = true, $sortQuery = "")
	{
		include($pathConnMod . "conn.php");
		// prepare select
		$selectFields = "";
		foreach($fields as $field)
			$selectFields .= "$field, ";
		$selectFields = substr($selectFields, 0, -2); // remove last comma
		//where clause
		if(is_array($wheres) && !empty($wheres))
		{
			$whereCondition = "where ";
			foreach($wheres as $whereClause => $whereValue)
				$whereCondition .= "$whereClause = '$whereValue' and ";
			$whereCondition = substr($whereCondition, 0, -5); // remove last and
		}
		else
			$whereCondition = "";

		if($sortQuery != "")
			$sortStr = "order by $sortQuery";

		$Query = "select $selectFields from " . TABLE_PREFIX . "$tableName $whereCondition $sortStr";
		if($echo)
			var_dump($Query);

		// execute query and get query count
		$dbResult = $dblink->query($Query);

		$result_data = array();
		$total_rows = $dbResult->numRows();
		if($count)
			$result_data['count'] = $total_rows;
		$count = 0;
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$result_data[$count++] = $row;
		}

		return $result_data;
	}
}
?>