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

	class curl
	{
		var $timeout;
		var $url;
		var $file_contents;

		function getFile($url)
		{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_VERBOSE, 1);

			curl_setopt($ch, CURLOPT_POST, 0);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

			$this->file_contents = curl_exec($ch);

			curl_close ($ch);

			return $this->file_contents;
		}
		function getFileOld($url,$timeout=0)
		{
			# use CURL library to fetch remote file
			$ch = curl_init();
			$this->url = $url;
			$this->timeout = $timeout;
			curl_setopt ($ch, CURLOPT_URL, $this->url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
			$this->file_contents = curl_exec($ch);
			/*
			if ( curl_getinfo($ch,CURLINFO_HTTP_CODE) !== 200 ) {
			return('Bad Data File '.$this->url);
			} else {
			*/
			return $this->file_contents;
			//}
			curl_close($ch);
		}
	}
?>