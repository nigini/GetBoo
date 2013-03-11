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
//TODO: Filter the data, even though it is an admin we might want to save him the trouble of making a mistake and disabling his website!
//TODO: Add more support for custom values, right now we only display custom values if the current value doesn't match the choices for select (drop down)
	session_start();
	include('access.php');
	$access = checkAccess('a', 'f', '', true);
	if($access)
	{
		require_once('includes/configuration.php');
						
		$configs = load_configs();
		$configs_post = array();
		
		if ($_POST['submitted'])
		{
			foreach($_POST as $key=>$currentPOST)
			{
				if(trim($currentPOST) != "" && $key !=  "no_js" && $key !=  "token" && $key !=  "submitted")
				{
					$configs_post[$key] = $currentPOST;
				}
			}
			
			$success = true;
			
			foreach($configs as $key=>$currentConfig)
			{
				if(!isset($configs_post[$key]))
					$configs_post[$key] = "0";
				$result = Configuration::SetConfig($key, $configs_post[$key]);
				if(!$result)
				{
					$message = ("<p class=\"error\">" . T_("Error when assigning the config variables") . "</p>");
					$success = false;
					break;
				}
			}
			
			if($success)
				$message = T_("Configuration has been saved");
				
						
			if($message != null && !isset($_POST['no_js']))
			{
				if($success)
				{
					$classMsg = "success";
					$widthDiv = " style=\"width: 200px; text-align: center; margin:0px auto;\"";
				}
				else
					$classMsg = "error";
				echo("<p class=\"$classMsg\"$widthDiv>" . $message . "</p>\n");
			}
		}
		if(isset($_POST['no_js']) || !$_POST['submitted'])
		{
			$config_css = true;
			$jquery_script_form = true;
			include('header.php');
			include('includes/config.class.php');
			
?>
	<script type='text/javascript'>
	// prepare the form when the DOM is ready 
	$(document).ready(function() { 
		$('#no_js_tag').remove();
	    var options = { 
	        target:        '#messagesDiv',
 			resetForm: 	   false,
 			success:       showResponse
	    }; 
	 
	    // bind form using 'ajaxForm' 
	    $('#config_form').ajaxForm(options); 
	}); 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
	    $('.error').fadeTo("slow", 0.7);
	    $('.error').fadeTo("slow", 1);
	    $('.success').fadeTo("slow", 0.7);
	    $('.success').fadeTo("slow", 1);
	} 
	 
	</script>
<?php
			
			$configManager = new ConfigurationManager();
			
			
			echo("<h2>" . T_("Settings") . " -- " . T_("Manage Configuration") . "</h2>\n");
			echo("<div class=\"content\">\n");
	
			$prev_group_id = null;
			
			$token = md5(uniqid(rand(), true)); 
			$_SESSION['security_token'] = $token;
			if($message != null && isset($_POST['no_js']))
			{
				if($success)
					$classMsg = "success";
				else
					$classMsg = "error";
				echo("<div id=\"messagesDiv\"><p class=\"$classMsg\">" . $message . "</p></div>");
			}
	?>
	<div id="config_manager">
	<form method="post" id="config_form" action="manageconfig.php">
	<input type="hidden" name="token" value="<?php echo $token; ?>">
	<input type="hidden" id="no_js_tag" name="no_js">
	<?php
			if(isset($_POST['no_js']))
				$configs = load_configs();
				
			foreach($configs as $key => $config)
			{
				if($config['id'] != $prev_group_id) // we are changing group
				{
					if($prev_group_id != null)
						echo("</table></fieldset>");
					$prev_group_id = $config['id'];
					echo("<fieldset id=\"" . $config['title'] . "\">");
		  			echo("<legend>" . $config['title'] . " " . T_("Settings") . "</legend>");
		  			echo($config['description'] . "<table class=\"inline\">");
				}
	
				if($config['config_type'] == "choices")
					$options = $configManager->html_select($config['config_value'], $config['config_choices'], $config['config_name']);
				else if($config['config_type'] == "boolean")
					$options = $configManager->html_checkbox($config['config_value'], $config['config_name']);
				else if($config['config_type'] == "string" || $config['config_type'] == "integer")
					$options = $configManager->html_textfield($config['config_value'], $config['config_name'], $config['config_type']);
				else
					$options = $config['config_value'];
				echo("<tr class=\"default\"><td class=\"title\"><span title=\"" . $config['config_name'] . "\"><label for=\"config___title\">" . $config['config_description'] . "</label></span></td><td class=\"value\">$options</td></tr>");
			}
			echo("</table></fieldset>");
		if(!isset($_POST['no_js']))
		{
			echo("<div id=\"messagesDiv\"></div>");
		}
?>
	<p style="text-align: center"><input type="submit" name="submitted" value="<?php echo T_("Save Changes");?>" /></p>
	</form>
	<?php echo("<p><a href=\"controlpanel.php\"><< " . T_("Back to") . " " . T_("Settings") . "</a></p>"); ?>
	</div>
	</div>
<?php
		}
	}
	
	function load_configs()
	{
		include('conn.php');
						
		$configs_post = array();
		
		$Query = ("select config_name, config_value, config_description, config_type, config_group, config_choices, cg.id, cg.title, cg.description
					from " . TABLE_PREFIX . "configs, " . TABLE_PREFIX . "configs_groups cg
					where config_group=cg.id and cg.id != 0
					order by cg.id");
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);

	  	$configs = array();
		while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$key = ("{$row["config_name"]}");
			$configs[$key] = $row;
		}
		return $configs;
	}
include('footer.php'); ?>