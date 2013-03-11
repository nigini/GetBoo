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

/**
 * Installation script
 * Started on April 19, 2007
 * TODO: 
 */
ini_set('include_path',ini_get('include_path'). PATH_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'pear' . DIRECTORY_SEPARATOR . PATH_SEPARATOR);
//echo get_include_path();
define("VERSION_NUMBER", "1.04");
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	            "http://www.w3.org/TR/html4/strict.dtd">
	<head>
	<link rel="shortcut icon" href="/favicon.ICO" type="image/x-icon">
	<link rel="icon" type="image/x-icon" href="/favicon.ICO">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Welcome to GetBoo Installation</title>
	<script type='text/javascript' src='../includes/jquery/jquery-1.2.3.pack.js'></script>
	<script type='text/javascript' src='../includes/jquery/jquery.form.pack.js'></script>
	<link rel="stylesheet" type="text/css" href="../style.css" media="screen, projection">
	<style type="text/css">
	.install
	{
		margin:0px auto;
		width: 80%;
	}

	.install table
	{
		text-align: center;
	}

	.install td
	{
		border: 2px solid #E9E9FF;
	}

	.install th
	{
		font-weight: bold;
	}

	.install-info
	{
		text-align: right;
		padding-right: 10px;
		font-size: small;
		color: #154C89;
		font-weight: bold;
		width: 50%;
	}

	.install-info-optional
	{
		text-align: right;
		padding-right: 10px;
		font-size: small;
		color: #154C89;
		width: 50%;
	}

	.install-content
	{
		text-align: left;
		padding-left: 10px;
		width: 50%;
	}
	</style>
	<script type='text/javascript'>
	// prepare the form when the DOM is ready 
	$(document).ready(function() { 
		$('#nojswarning').remove();
		$("button").click(function () {
     		$("div.help").toggle();
   		});	 
		
	    var options = { 
	        target:        '#messagesDiv',
	        beforeSubmit:  showRequest,
 			success:       showResponse
	    }; 
	 
	    // bind form using 'ajaxForm' 
	    $('#install_form').ajaxForm(options); 
	}); 
	
	// pre-submit callback 
	function showRequest(formData, jqForm, options) { 
		$('.error').remove();
		$('#messagesDiv').append("<p class='notice'>Processing...<\/p>");
		$("#installButton").attr("disabled","disabled");
		$("#upgradeButton").attr("disabled","disabled");
		return true; 
	} 

	// post-submit callback 
	function showResponse(responseText, statusText)  { 
	    $('.error').fadeTo("slow", 0.7);
	    $('.error').fadeTo("slow", 1);
	    if(responseText.indexOf("success") != -1 || responseText.indexOf("notice") != -1)
	    {
	    	$('.content').remove();
	    }
		else
		{
			$("#installButton").removeAttr("disabled");
			$("#upgradeButton").removeAttr("disabled");
		}
	}
	</script>
	</head>
	<body>
<?php

include('installFunctions.php');

// First check if we have permissions to write in the parent folder
if (!is_writable('../')) {
	echo("<p class=\"error\">Please set the correct user (e.g. apache) for the root folder of your script (e.g. <code>chown www-data.www-data -R getboo/</code>).</p>");
}
else if(file_exists("../config.inc.php")) // Config file is already created
{
	echo("<p class=\"error\">Your config file is already created. You should delete this folder if GetBoo has been installed.</p><p>If you are upgrading, move your previous config file to another folder before running the installation script</p>");
}
else if(!file_exists_incpath('PEAR.php'))
{
	echo("<p class=\"error\">We couldn't detect PEAR package in your include path. If it is installed, make sure to include it in the PHP path.</p>");
	echo("<p>PEAR is required for GetBoo to work with your database. We are currently providing the necessary database packages (MDB2), but not PEAR.<br>For more information on PEAR, please visit this <a href=\"http://pear.php.net/\">website</a>, or contact your website administrator.</p>");
}
else
{
	// Determine a tentative root (from Scuttle root script)

    $pieces = explode('/', $_SERVER['SCRIPT_NAME']);
    $tentativeRoot = '/';
    foreach($pieces as $piece) {
        if ($piece != '' && !strstr($piece, '.php') && !strstr($piece, 'install')) {
            $tentativeRoot .= $piece .'/';
        }
    }
    if (($tentativeRoot != '/') && (substr($tentativeRoot, -1, 1) != '/')) {
        $tentativeRoot .= '/';
    }
    $tentativeRoot = 'http://'. $_SERVER['HTTP_HOST'] . $tentativeRoot;

	$locales = array('en_US', 'fr_FR', 'es_ES', 'cs_CZ', 'de_DE');
	//build select
	foreach($locales as $locale) {
		$selected = ($locale == $website_locale)?" selected=selected":"";
		$selectLocaleStr .= "<option value=\"$locale\"$selected>$locale</option>\n";
	}
	
	//get dabatase type dropdown
	$databaseTypesDropDown = dbms_select();

	// selected string
	$sel = " selected=selected";
	?>
	<p class="error" id="nojswarning"><?php echo("You must have Javascript enabled in order to use this installation script");?></p>
	<div class="content install">
	<p style="text-align: center;"><img src="../images/getboologo.png" alt="GetBoo Logo" title="GetBoo Logo" width="222" height="29"></p>
	<h2 style="text-align: center;">GetBoo Installation Script - Version <?php echo(VERSION_NUMBER);?></h2>
	<p style="width: 80%; margin:0px auto;">
	Welcome to the installation script, thank you for choosing GetBoo!<br>
	<b>Installation</b>: Fill the form to install the application on your server, and click the help button if you need more information for a field (or simply pause your mouse over the field).<br>
	<b>Upgrading</b>: If you saved your config file in another folder, upload it at the end of the form, otherwise fill only the Database Setup section and then press Upgrade.<br>
	<br>
	<button>Toggle Help</button>
	</p>
	<div class="help" style="float: none; width: 80%; display: none; margin:0px auto;">
  	<ul>
	  	<li>Database Setup
		  	<ul>
			    <li>The Database Type - <?php $desc['dbtype'] = "The database type you will be using (e.g. MySQL)"; echo $desc['dbtype'];?></li>  
			    <li>The Database Server Hostname or DSN - <?php $desc['dbhost'] = "The address of the database server"; echo $desc['dbtype'];?></li>
			    <li>The Database Server port - <?php $desc['dbport'] = "The port of the database server (leave empty for default)"; echo $desc['dbtype'];?></li>
			    <li>The Database name - <?php $desc['dbname'] = "The name of the database on the server"; echo $desc['dbtype'];?></li>
			    <li>Create database - <?php $desc['dbcreate'] = "If the user you specify has the privileges, it will create a new database to store the tables"; echo $desc['dbtype'];?></li>
			    <li>The Database username and Database password - <?php $desc['dbuser'] = "The login information to access the database"; echo $desc['dbtype'];?></li>
			    <li>Prefix for tables in database - <?php $desc['dbprefix'] = "Useful for sharing a database with other tables (e.g. 'gb_')"; echo $desc['dbtype'];?></li>
		    </ul>
	    </li>
	    <li>Admin configuration
		  	<ul>
			    <li>Admin Username - <?php $desc['adname'] = "The name of the first administrator account to manage your new installation"; echo $desc['adname'];?></li>  
			    <li>Admin Password - <?php $desc['adpass'] = "The password of this administrator"; echo $desc['adpass'];?></li>
			    <li>Admin Email - <?php $desc['ademail'] = "The email of this administrator"; echo $desc['ademail'];?></li>
		    </ul>
	    </li>
	    <li>Basic Configuration
		  	<ul>
			    <li>Default language - <?php $desc['bclang'] = "Which translation to use for the content"; echo $desc['bclang'];?></li>  
			    <li>Website Name - <?php $desc['bcwname'] = "The name of this GetBoo installation"; echo $desc['bcwname'];?></li>
			    <li>Website Root - <?php $desc['bcwroot'] = "The url of the installation (detected)"; echo $desc['bcwroot'];?></li>
			    <li>Caching - <?php $desc['bccaching'] = "Enables caching of the pages for improved performances"; echo $desc['bccaching'];?></li>
			    <li>cURL available - <?php $desc['bccurl'] = "Enables cURL file retrieval if the module is installed"; echo $desc['bccurl'];?> (<a href="http://www.php.net/curl">info</a>)</li>
			    <li>Anti-Spam - <?php $desc['bcspam'] = "Activates measures to prevent spam"; echo $desc['bcspam'];?></li>
			    <li>Demo account - <?php $desc['bcdemo'] = "Creates a demo account for guests to try the features before signing-up"; echo $desc['bcdemo'];?></li>
		    </ul>
	    </li>
	    <li>Upgrading
		  	<ul>
			    <li>config.inc.php file - <?php $desc['upconf'] = "The saved copy of the previous installation's configuration file"; echo $desc['upconf'];?></li>  
		    </ul>
	    </li>
    </ul>
	For a complete list of configuration settings with their default value, you can check this <a href="http://wiki.getboo.com/configs">wiki page</a>.
	<br><br>
	</div>
	
	<form action="install.inc.php" id="install_form" method="post" enctype="multipart/form-data">
	<table class="install">
		<tr>
			<th colspan="2">Database Setup</th>
		</tr>
		<tr title="<?php echo $desc['dbtype'];?>">
			<td class="install-info">Database Type</td><td class="install-content" title="<?php echo $desc['dbtype'];?>"><select name="dbtype" class="formtext"><?php echo $databaseTypesDropDown; ?></select></td>
		</tr>
		<tr title="<?php echo $desc['dbhost'];?>">
			<td class="install-info">Database Server Hostname / DSN</td><td class="install-content"><input type="text" name="dbhost" value="<?php echo $dbhost; ?>" size="40" maxlength="255" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['dbport'];?>">
			<td class="install-info-optional">Database Server Port</td><td class="install-content"><input type="text" name="dbport" value="<?php echo $dbport; ?>" size="20" maxlength="255" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['dbname'];?>">
			<td class="install-info">Database Name</td><td class="install-content"><input type="text" name="dbname" value="<?php echo $dbname; ?>" size="40" maxlength="255" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['dbcreate'];?>">
			<td class="install-info-optional">Create database</td><td class="install-content"><input type="checkbox" name="db_create" <?php echo (isset($db_create))?"checked=\"checked\"":""; ?>></td>
		</tr>
		<tr title="<?php echo $desc['dbuser'];?>">
			<td class="install-info">Database Username</td><td class="install-content"><input type="text" name="dbuname" value="<?php echo $dbuname; ?>" size="40" maxlength="255" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['dbuser'];?>">
			<td class="install-info-optional">Database Password</td><td class="install-content"><input type="password" name="dbpass" value="<?php echo $dbpass; ?>" size="40" maxlength="255" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['dbprefix'];?>">
			<td class="install-info-optional">Prefix for tables in database:</td><td class="install-content"><input type="text" name="table_prefix" value="<?php echo $table_prefix; ?>" size="40" maxlength="40" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr>
			<th colspan="2">Admin Configuration</th>
		</tr>
		<tr title="<?php echo $desc['adname'];?>">
			<td class="install-info">Admin Username</td><td class="install-content"><input type="text" name="admin_username" value="<?php echo $admin_username; ?>" size="20" maxlength="20" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['adpass'];?>">
			<td class="install-info">Admin Password</td><td class="install-content"><input type="password" name="admin_password" value="<?php echo $admin_password; ?>" size="20" maxlength="20" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['ademail'];?>">
			<td class="install-info">Admin Email</td><td class="install-content"><input type="text" name="admin_email" value="<?php echo $admin_email; ?>" size="20" maxlength="100" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr>
			<th colspan="2">Basic Configuration</th>
		</tr>
		<tr title="<?php echo $desc['bclang'];?>">
			<td class="install-info">Default language</td><td class="install-content"><select name="website_locale" class="formtext"><?php echo $selectLocaleStr; ?></select></td>
		</tr>
		<tr title="<?php echo $desc['bcwname'];?>">
			<td class="install-info">Website Name</td><td class="install-content"><input type="text" name="website_name" value="<?php echo $website_name; ?>" size="40" maxlength="100" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['bcwroot'];?>">
			<td class="install-info">Website Root</td><td class="install-content"><input type="text" name="website_root" value="<?php echo (isset($website_root))?$website_root:$tentativeRoot; ?>" size="40" maxlength="255" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr title="<?php echo $desc['bccaching'];?>">
			<td class="install-info">Caching</td><td class="install-content"><select name="usecache" class="formtext"><option value="1">True</option><option value="0"<?php echo($usecache == "0")?"$sel":"";?>>False</option></select></td>
		</tr>
		<tr title="<?php echo $desc['bccurl'];?>">
			<td class="install-info"><a href="http://www.php.net/curl">cURL</a> available:</td><td class="install-content"><select name="curl_available" class="formtext"><option value="1">True</option><option value="0"<?php echo($curl_available == "0")?"$sel":"";?>>False</option></select></td>
		</tr>
		<tr title="<?php echo $desc['bcspam'];?>">
			<td class="install-info">Anti-Spam:</td><td class="install-content"><select name="anti_spam" class="formtext"><option value="1">True</option><option value="0"<?php echo($anti_spam == "0")?"$sel":"";?>>False</option></select></td>
		</tr>
		<tr title="<?php echo $desc['bcdemo'];?>">
			<td class="install-info">Demo account:</td><td class="install-content"><select name="use_demo" class="formtext"><option value="1">True</option><option value="0"<?php echo($use_demo == "0")?"$sel":"";?>>False</option></select></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;"><input type="submit" name="installBtn" value="Install GetBoo!" class="genericButton" id="installButton"></td>
		</tr>
		<tr>
			<th colspan="2">Upgrading</th>
		</tr>
		<tr title="<?php echo $desc['upconf'];?>">
			<td class="install-info">config.inc.php file:</td><td class="install-content"><input type="file" name="config_file" size="30" class="formtext" onfocus="this.select()"></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;"><input type="submit" name="upgradeBtn" value="Upgrade GetBoo!" class="genericButton" id="upgradeButton"></td>
		</tr>
	</table>
	</form>
	</div>
	<div id="messagesDiv" style="margin-bottom: 2em;"></div>
	</body>
	</html>
<?php
}
?>