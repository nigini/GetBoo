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
//TODO: Check with locale for long dates to be displayed in the correct language

 	function convert_date($date, $tz = false)
 	{
 		$date = strtotime($date); 
	 	if($date == 0)
	 		return T_("Never");
	 	
	 	// Make sure the timezone is not set in the format
	 	$posTZ = strpos(DATE_FORMAT, "e");
	 	$dateFormat = DATE_FORMAT;
 		if(!($posTZ === false))
 			$dateFormat = str_ireplace("e", "", DATE_FORMAT);
	 	if($tz) // display time zone
	 	{
	 		return date($dateFormat ." e", $date);
	 	}
	 	else
	 		return date($dateFormat, $date);
 	}

 	function convert_date_tags($date)
 	{
 		$date = strtotime($date); 
 		return date("Y-m-d", $date);
 	}

 	function convert_date_feed($date)
 	{
 		$date = strtotime($date); 
	 	return date("D, d M Y H:i:s O", $date);
 	}
 	
 	// adapted from http://codingforums.com/archive/index.php?t-118138.html
	function get_formatted_timediff($then, $timeout = 24, $now = false)
	{
		$then = strtotime($then); 
		define('INT_SECOND', 1);
		define('INT_MINUTE', 60);
		define('INT_HOUR', 3600);
		define('INT_DAY', 86400);
		define('INT_WEEK', 604800);
	    $now      = (!$now) ? time() : $now;
	    $timediff = ($now - $then);
	    $weeks    = (int) intval($timediff / INT_WEEK);
	    $timediff = (int) intval($timediff - (INT_WEEK * $weeks));
	    $days     = (int) intval($timediff / INT_DAY);
	    $timediff = (int) intval($timediff - (INT_DAY * $days));
	    $hours    = (int) intval($timediff / INT_HOUR);
	    $timediff = (int) intval($timediff - (INT_HOUR * $hours));
	    $mins     = (int) intval($timediff / INT_MINUTE);
	    $timediff = (int) intval($timediff - (INT_MINUTE * $mins));
	    $sec      = (int) intval($timediff / INT_SECOND);
	    $timediff = (int) intval($timediff - ($sec * INT_SECOND));
	    if($hours < $timeout && $days < 1 && $weeks < 1)
	    {
	
		    $str = '';
		    if ( $weeks )
		    {
		        $str .= intval($weeks);
		        $str .= ' ' . T_ngettext('week', 'weeks', $weeks);
		    }
		
		    if ( $days )
		    {
		        $str .= ($str) ? ', ' : '';
		        $str .= intval($days);
		        $str .= ' ' . T_ngettext('day', 'days', $days);
		    }
		
		    if ( $hours )
		    {
		        $str .= ($str) ? ', ' : '';
		        $str .= intval($hours);
		        $str .= ' ' . T_ngettext('hour', 'hours', $hours);
		    }
		
		    if ( $mins )
		    {
		        $str .= ($str) ? ', ' : '';
		        $str .= intval($mins);
		        $str .= ' ' . T_ngettext('minute', 'minutes', $mins);
		    }
		
		    if ( $sec )
		    {
		        $str .= ($str) ? ', ' : '';
		        $str .= intval($sec);
		        $str .= ' ' . T_ngettext('second', 'seconds', $sec);
		    }
		   
		    if ( !$weeks && !$days && !$hours && !$mins && !$sec )
		    {
		        $str .= T_('0 seconds ago');
		    }
		    else
		    {
		        $str = sprintf(T_('%s ago'), $str);
		    }
    	}	   
	    return $str;
	}
?>