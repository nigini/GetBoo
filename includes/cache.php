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

class Cache {
   var $basedir;
   var $fileextension = '.cache';

   function &getInstance() {
      static $instance;
      if (!isset($instance)) {
         $instance =& new Cache();
      }
      return $instance;
   }

   function Cache() {
      $this->basedir = CACHE_DIR;
   }

   function Start($hash, $time = 300) {
      $cachefile = $this->basedir .'/'. $hash . $this->fileextension;
      if (file_exists($cachefile) && time() < filemtime($cachefile) + $time) {
         @readfile($cachefile);
         echo "\n<!-- Cached: ". date('r', filemtime($cachefile)) ." -->\n";
         unset($cachefile);
         exit;
      }
     //ob_start("ob_gzhandler");
     ob_start();
   }

   function End($hash) {
      $cachefile = $this->basedir .'/'. $hash . $this->fileextension;
      $handle = fopen($cachefile, 'w');
      fwrite($handle, ob_get_contents());
      fclose($handle);
      ob_flush();
   }
}
?>