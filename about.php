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
<h2><?php echo T_("About");?></h2>
<div class="about">
<?php if(IS_GETBOO) {?>
<p><b><?php echo T_("New");?></b> <a href="project.php"><?php echo T_("Learn more about the project");?></a> | <a href="donations.php"><?php echo T_("Donations");?></a></p>
<?php } ?>
<h3><?php echo T_("What is");?> <?php echo WEBSITE_NAME; ?>?</h3>

<p><b><?php echo WEBSITE_NAME; ?></b> <?php echo T_("is both a social and private bookmarking website which can be used to");?>:</p>
<ul>
<li><?php echo sprintf(T_("<b>Keep</b> links to your favorite news, blogs, music, games, and more on %s and access them from any computer on the web"),WEBSITE_NAME);?>.</li>
<li><?php echo T_("<b>Import/Export</b> these links from your browser's bookmarks and view them with the same hierarchy");?>.</li>
<li><?php echo T_("<b>Share</b> favorites with friends, family, and colleagues.");?></li>
<li><?php echo sprintf(T_("<b>Find</b> new things. Everything on %s is someone's favorite - they've already done the work of finding it. Explore and enjoy"),WEBSITE_NAME);?>.</li>
</ul>

<h3><?php echo T_("Why");?> <?php echo WEBSITE_NAME; ?>?</h3>

<ul>
<li><?php echo T_("You can import and export your bookmarks from your browser, and keep the same hierarchy of folders that you have in your browser");?>.</li>
<li><?php echo T_("You can create and join groups, either public or private, enabling you to share bookmarks for a school research/game strategy/etc");?>.</li>
<li><?php echo T_("And you get all the social bookmarking advantages, with public bookmarks and tags");?>!</li>
</ul>

<h3><?php echo T_("What to do?");?></h3>

<p><?php echo T_("Navigate and discover new urls that users found interesting!");?></p>
<p><?php echo sprintf(T_("Add your own urls and share them with the world by <a href=\"%s\">creating an account</a>!"),"newuser.php");?></p>

<h3><?php echo T_("Developers");?></h3>

<p><?php echo sprintf(T_("GetBoo is an open-source project (hosted on <a href=\"%s\">SourceForge.net</a>), licensed under the <a href=\"%s\">General Public Licence</a>."),"https://sourceforge.net/projects/getboo", "http://www.gnu.org/copyleft/gpl.html");?></p>
<p><?php echo sprintf(T_("Please visit <a href=\"%s\">GetBoo's wiki</a> for more information about the project."),"http://wiki.getboo.com/");?></p>

<!--Please modify this section to reflect your contact information, and remove the GetBoo check once done!-->
<?php if(IS_GETBOO) { // START CONTACT?>
<h3><?php echo T_("Contact");?></h3>
<p>Maxime Chartrand-Dumas<br><?php echo T_("Email");?>: maxime at getboo.com</p>
<div class="contact">
<ul>
<li><b><?php echo T_("Registration");?></b> : registration at getboo.com</li>
<li><b><?php echo T_("Support");?></b> : support at getboo.com</li>
<li><b><?php echo T_("Abuse");?></b> : abuse at getboo.com</li>
</div>
<?php } //END CONTACT?>
</div>
<?php include('footer.php'); ?>