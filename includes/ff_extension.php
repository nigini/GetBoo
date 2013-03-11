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
?>
<div class="extension">
<p><?php echo sprintf(T_("In order to use your %s account, you need to install two buttons into your browser.<br>" .
 "We recommend that all Firefox users install this simple extension:"), WEBSITE_NAME); ?></p>
 <p><img src="images/extension/toolbarintro.png" alt="Toolbar buttons" width="362" height="202" /></p><br>
 <h4><a href="http://www.getboo.com/extension/getboo.xpi"><?php echo T_("Install GetBoo Firefox Extension");?></a></h4><br>
 <p><br><img src="images/extension/ffprotect.png" alt="Firefox block pop-up" width="660" height="30" /></p>
 <p><?php echo T_("If you see this message at the top of the page, click Allow or follow the steps (for earlier versions)");?>.</p>
 <p><?php echo T_("Click <b>Install</b> to install the extension in your browser, and <b>Restart</b> your browser once it is done");?>.</p>
 <p><?php echo T_("In order to have the buttons installed on your toolbar, rick-click anywhere on the toolbar, click <code>Customize...</code> 
 and then in the <code>Customize Window</code>, drag any of the buttons onto your toolbar. Click <code>Done</code> when you are finished");?>.<br><br>
 <?php echo sprintf(T_("Please refer to the Firefox extension %swiki page</a> for more information on how to use and configure the extension.<br>" .
 "Alternatively, you can download it directly from the %sFirefox add-ons website</a>"), "<a href=\"http://wiki.getboo.com/help/ffextension\">", "<a href=\"https://addons.mozilla.org/en-US/firefox/addon/2382\">"); ?>.</p>
 <p><br><?php echo sprintf(T_("If you don't want to install the extension, or you are not using Firefox, you might want to have a look at 
 bookmarklets called EasyBook, which can be found in the wiki under
  %sHelp</a> / Bookmarklets (EasyBook)"), "<a href=\"http://wiki.getboo.com/help/helpindex#bookmarklets_easybook\">");?></p>
</div>