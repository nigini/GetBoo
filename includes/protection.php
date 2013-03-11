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
// Inspired from examples in the PHP manual to protect from malicious users

	// Quote variable to make safe
	function quote_smart($value)
	{
		global $SETTINGS;
		include($SETTINGS['path_mod'] . 'conn.php');
		// Stripslashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		// Quote if not integer
		if (!is_numeric($value)) {
			$valueEscaped = $dblink->escape($value);
			if (PEAR::isError($valueEscaped)) {
				$value = "'" . $value . "'" ;
			}
			else
				$value = "'" . $valueEscaped . "'" ;
		}
		return $value;
		
	}

	function valid($str,$length) {

		$validlength=$length;
		$validmask="abcdefghijklmnopqrstuvwxyz0123456789_-";

		$str=strtolower($str);
		if (strspn($str, $validmask) == strlen($str) && $validlength >= strlen($str)) {
			 return true;
		} else {
			 return false;
		}
	}

	function validNumber($str,$length) {

		$validlength=$length;
		$validmask="0123456789";

		$str=strtolower($str);
		if (strspn($str, $validmask) == strlen($str) && $validlength >= strlen($str)) {
			 return true;
		} else {
			 return false;
		}
	}

	// $document should contain an HTML document.
	// This will remove HTML tags, javascript sections
	// and white space. It will also convert some
	// common HTML entities to their text equivalent.
	function remhtml(&$document)
	{
		$search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
							  '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
							  '@([\r\n])[\s]+@',                // Strip out white space
							  '@&(quot|#34);@i',                // Replace HTML entities
							  '@&(amp|#38);@i',
							  '@&(lt|#60);@i',
							  '@&(gt|#62);@i',
							  '@&(nbsp|#160);@i',
							  '@&(iexcl|#161);@i',
							  '@&(cent|#162);@i',
							  '@&(pound|#163);@i',
							  '@&(copy|#169);@i',
							  '@&#(\d+);@e');                    // evaluate as php

		$replace = array ('',
							  '',
							  '\1',
							  '"',
							  '&',
							  '<',
							  '>',
							  ' ',
							  chr(161),
							  chr(162),
							  chr(163),
							  chr(169),
							  'chr(\1)');

		$text = preg_replace($search, $replace, $document);
		$document = html_entity_decode(addslashes($text));
	}


// Email validator from http://www.ilovejackdaniels.com/php/email-address-validation/
	function check_email_address($email)
	{
	  // First, we check that there's one @ symbol, and that the lengths are right
	  if (!ereg("[^@]{1,64}@[^@]{1,255}", $email)) {
		 // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		 return false;
	  }
	  // Split it into sections to make life easier
	  $email_array = explode("@", $email);
	  $local_array = explode(".", $email_array[0]);
	  for ($i = 0; $i < sizeof($local_array); $i++) {
		  if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
			return false;
		 }
	  }
	  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
		 $domain_array = explode(".", $email_array[1]);
		 if (sizeof($domain_array) < 2) {
			  return false; // Not enough parts to domain
		 }
		 for ($i = 0; $i < sizeof($domain_array); $i++) {
			if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
			  return false;
			}
		 }
	  }
	  return true;
	}

	// Adapted from Scuttle
	function filter($string, $type = NULL)
	{
		$string = trim($string);
		$string = stripslashes($string);

	    switch ($type) {
	        case 'url':
	            $string = rawurlencode($string);
	            break;
	        default:
	            $string = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	    }

		return $string;
	}
?>