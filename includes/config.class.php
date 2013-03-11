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

class ConfigurationManager
{
	function html_select($value, $choices, $name)
	{
		// added: custom support
		$custom = true;
		$choicesArray = explode(',', trim($choices));
		// disable if no other choices
        if (count($choicesArray) <= 1) {
        	return "No choice";
        }
        $html_result = "<select name=\"$name\" class=\"edit\">";
		foreach($choicesArray as $choice)
		{
			if($choice == $value)
			{
				$selected = ' selected="selected"';
				$custom = false;
			}
			else
				$selected = '';
			//$selected = ($choice == $value); $custom = false; ? ' selected="selected"' : '';
			$html_result .= "<option name=\"$choice\"$selected>$choice</option>";
		}
		//if no match found, we have a custom value
		if($custom)
		{
			$html_result .= "<option name=\"$value\" selected=\"selected\">$value</option>";
		}
		$html_result .= "</select>";
		return $html_result;
	}
	
	function html_checkbox($value, $name)
	{
		$checked = ($value) ? ' checked="checked"' : '';
		$html_result = "<input name=\"$name\" type=\"checkbox\" value=\"1\" ".$checked.">";
		return $html_result;
	}
	
	function html_textfield($value, $name, $type)
	{
		$html_result = "<input name=\"$name\" type=\"text\" value=\"$value\" class=\"$type\">";
		return $html_result;
	}
	//TODO: numeric times in seconds, convert to a more readable format.
}
?>