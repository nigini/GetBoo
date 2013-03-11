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

	session_start();
	require_once('includes/user.php');
	require_once("config.inc.php");
	$user = new User();
	if($user->isLoggedIn()) // User logged in
	{
		//Redirect the user to his bookmarks page
		header('Location: books.php');
	}
	else
	{
		$success = false;
		// Get the user's input from the form
		$username = $_POST['name'];
		$pass = $_POST['pass'];
	   
		//Login user
		if ($_POST['submitted'])
		{
			$tokenError = "";
			// Retrieve token
			if(!(isset($_SESSION['security_token']) && $_SESSION['security_token'] != ""))
				$tokenError = T_("A session security token is missing");
			else if(!(isset($_POST["token"]) && $_POST["token"]!= ""))
				$tokenError = T_("A form security token is missing");
			else if($_POST["token"] != $_SESSION['security_token'])
				$tokenError = T_("The security token is invalid");
			//$_SESSION['security_token'] = null;
			if(!$tokenError)
			{
				$resultArr = $user->login($username, $pass);
	
				//Retrieve results
				$success = $resultArr['success'];
				$message = $resultArr['message'];
				$optmessage = $resultArr['optmessage'];
				$realname = $resultArr['username'];
	
				if($success) // User logged on
				{
					$redirectStr = "";
					//Check if using easybook
					if(isset($_SESSION['g_title']))
					{
						$redirectStr = "add.php";
					}
					else if(isset($_SESSION['pathStr']) && strpos($_SESSION['pathStr'], "index.php") === false && strpos($_SESSION['pathStr'], "logout.php") === false)
					{
						$strLocation = $_SESSION['pathStr'];
						if($_SESSION['queryStr'] != "")
							$strLocation .=  ("?" . $_SESSION['queryStr']);
						$redirectStr = $strLocation;
					}
					else
					{
						//Save logged in message in session
						$_SESSION['login_msg'] = $message;
						$redirectStr = "books.php";
					}
					if(isset($_POST['no_js']))
						header("Location: " . $redirectStr);
					else
					{
						$message = "<a href=\"$redirectStr\">" . T_("Redirecting") . "</a>...";
						echo("<script type='text/javascript'>location.href=\"$redirectStr\";</script>");
					}
					
				}
				if($message != null && !isset($_POST['no_js']))
				{
					if($success)
						$classMsg = "success";
					else
						$classMsg = "error";
					echo("<p class=\"$classMsg\">" . $message . "</p>\n");
					if($optmessage != null)
						echo("<p>" . $optmessage . "</p>\n");
				}
			}
			else
			{
				echo("<p class=\"error\">$tokenError</p>");
			}
		}
		if(isset($_POST['no_js']) || !$_POST['submitted'])
		{
			include('config.inc.php');
			$customTitle = T_("Log In");
			$jquery_script_form = true;
			include('header.php');
			// Generate security token
			$token = md5(uniqid(rand(), true)); 
			$_SESSION['security_token'] = $token;
?>
	<script type='text/javascript'>
	// prepare the form when the DOM is ready 
	$(document).ready(function() { 
		$('#nojswarning').remove();
		$('#no_js_tag').remove();
	    var options = { 
	        target:        '#messagesDiv',
 			resetForm: 	   true,
 			beforeSubmit:  showRequest,
 			success:       showResponse
	    }; 
	 
	    // bind form using 'ajaxForm' 
	    $('#login_form').ajaxForm(options); 
	}); 
	
	// pre-submit callback 
	function showRequest(formData, jqForm, options) { 
		$('.error').remove();
		$('#messagesDiv').append("<p class='notice'><?php echo T_("Processing") . "...";?><\/p>");
		return true; 
	} 
	
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
	    $('.error').fadeTo("slow", 0.7);
	    $('.error').fadeTo("slow", 1);
	    if( responseText.indexOf("success") != -1)
	    {
	    	$('#form_div').remove();
	    }
	} 
	 
	</script>
<?php 
			echo("<h2>" . T_("Log In") . "</h2>");
			if($message != null && isset($_POST['no_js']))
			{
				$messageDiv = ("<p class=\"error\">" . $message . "</p>\n");
			}
			else
				echo("<p class=\"notice\" id=\"nojswarning\">" . T_("Javascript should be enabled to access all functionality") . "</p>");
			echo("<div id=\"messagesDiv\">$messageDiv</div>");
?>

<div id="form_div">
<form method="post" id="login_form" action="login.php">
<input type="hidden" name="token" value="<?php echo $token; ?>">
<input type="hidden" id="no_js_tag" name="no_js">
<table>
	<tr>
			<td><span class="formsLabel"><label for="login_usrname"><?php echo T_("Username");?></label></span></td>
			<td><input type="text" name="name" size="20" maxlength="20" class="formtext" onfocus="this.select()" id="login_usrname"></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Password");?></span></td>
			<td><input type="password" name="pass" size="20" maxlength="50" class="formtext" onfocus="this.select()"></td>
	</tr>
	<tr>
			<td><span class="formsLabel"><?php echo T_("Remember me");?></span></td>
			<td><input type="checkbox" name="remember" class="formtext" onfocus="this.select()"></td>
	</tr>
	<tr>
			<td></td>
			<td>
				<input type="submit" name="submitted" value="<?php echo T_("Log In");?>" class="genericButton">
			</td>
	</tr>
</table>
</form>
<?php if(USE_DEMO) { echo "<p>" . sprintf(T_("Use the account <b>%s</b> for preview"),"demo/demo") . ".</p>"; }?>
<p><a href="newuser.php"><?php echo T_("New User");?>?</a> | <a href="forgotpass.php"><?php echo T_("Forgot password");?>?</a> | <a href="activate.php"><?php echo T_("Activate Account");?></a></p><br>

<?php
			if(IS_GETBOO) {
				$pAlign = "left";
				include('paypal.php');
			}
			echo("</div>\n");
			include('footer.php');
		}
	}
?>