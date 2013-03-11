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
	$user = new User();

	// If connected and not admin, redirect
	if($user->isLoggedIn() && !$user->isAdmin())
		header("Location: books.php");
	else
	{
		include('config.inc.php');
		//Add user
		if ($_POST['submitted'])
		{
			$aname = $_POST["aname"];
			$pass = $_POST["pass"];
			$pass2 = $_POST["pass2"];
			$email = $_POST["email"];
			$emailrobot = $_POST["email2"];
			$hint = $_POST["hint"];
			$captcha = $_POST["captcha"];
			$success = false;
			include("includes/protection.php");
			if($aname!=null)
				remhtml($aname);
			if($email!=null)
				remhtml($email);
			if($hint!=null)
				remhtml($hint);
			if($pass!=null)
				remhtml($pass);
			if($pass2!=null)
				remhtml($pass2);
			if($captcha!=null)
				remhtml($captcha);
		
			$successMsg = ("<p class=\"success\">" . T_("Account created") . "!</p><p>" . sprintf(T_("You have been successfully added to %s"),WEBSITE_NAME) 
			. "!<br>" . sprintf(T_("You need to check your email and activate your account with the given url (or code in the <a href=\"%s\">activation</a> page)"),"activate.php") 
			. "<br>\n" . T_("The email is already sent, but with some free email providers it might take a few hours to receive it") . ".</p>");
		
			if($emailrobot != null) //Robot that is filling the form (he filled out the hidden email field)
			{
				// Fake success so the robot thinks he registered an account
				$success = true;
				echo $successMsg;
			}
			else if($aname != null && $pass != null && $pass2 != null && $email != null)
			{
				if(!(valid($aname, 20) && valid($pass, 20) && check_email_address($email)))
				{
					echo("<p class=\"error\">" . T_("Check for invalid characters or length, or wrong email address format") . ".</p>");
				}
				else
				{
					if($pass != $pass2)
					{
						echo("<p class=\"error\">" . T_("Both passwords have to match") . ".</p>");
					}
					else
					{
						include('conn.php');
						$Query = ("select name from " . TABLE_PREFIX . "session where (name='$aname')");
						//echo($Query . "<br>\n");
						$dbResult = $dblink->query($Query);
						if(!$row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
						{
							if(!CAPTCHA || $captcha != null || $user->isAdmin())
							{
								require_once('includes/php-captcha.inc.php');
								if (PhpCaptcha::Validate($captcha) || $user->isAdmin() || !CAPTCHA)
								{
									//Anti-Spam Protection: check if an account has been created in the last "delay" with the same IP
									$domain = $_SERVER['REMOTE_ADDR'];
		
									//Compute delay
									$delay = SAME_IP_NEW_ACCONT_DELAY * 3600;
		
									$Query = ("select a.name, s.DateJoin from " . TABLE_PREFIX . "activation a, " . TABLE_PREFIX . "session s where (a.name = s.name and ip='" . $domain . "' and " . DATE_DIFF_SQL . " datejoin) < " . $delay . ")");
									//echo($Query . "<br>\n");
									$dbResult = $dblink->query($Query);
									if((!$row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC)) || $user->isAdmin() || !CAPTCHA)
									{
										//encrypt password
										$passencrypt = $user->encryptPassword($pass);
										$status = ($user->isAdmin())?"normal":"disabled";
										$Query = "insert into " . TABLE_PREFIX . "session (Name, Pass, Email, PassHint, LastLog, DateJoin, Status, Style) " .
													"values('" . $aname . "','" . $passencrypt . "','" . $email . "','" . $hint . "', now(), now(), '$status', 'Auto')";
										//echo($Query . "<br>\n");
										$AffectedRows = $dblink->exec($Query);
										if($AffectedRows == 1)
										{
											//generate id activation
											$id = md5(uniqid(rand()));
											if($user->isAdmin())
												$Query = "INSERT INTO " . TABLE_PREFIX . "activation values ('$aname', '$id', 'Y', NULL, '$email')";
											else
												$Query = "INSERT INTO " . TABLE_PREFIX . "activation values ('$aname', '$id', 'N', '" . $_SERVER['REMOTE_ADDR'] . "', '$email')";
											//echo("$Query<br>\n");
											$AffectedRows = $dblink->exec($Query);
											if($AffectedRows != 1)
											{
												echo("<p class=\"error\">" . T_("An error occured while creating your activation code") . ".</p>");
											}
											else
											{
												if($user->isAdmin())
												{
													$success = true;
													echo("<p class=\"success\" id=\"adminAdd\">" . sprintf(T_("New user %s was successfully added"), "<b>$aname</b>") . "!</p>\n");
												}
												else
												{
													$user->sendActivationEmail($email, $aname, $pass, $id);
													$success = true;
													echo $successMsg;
													require_once('includes/browser.php');
													$browser = new Browser;
													//Check if the browser is Firefox, so we show the extension
													if($browser->Name == "Firefox")
													{
														echo("<br>");
														include("includes/ff_extension.php");
													}
												}
											}
										}
										else
											echo("<p class=\"error\">" . T_("An error occured when creating your account information") . ".</p>");
									}
									else
										echo("<p class=\"error\">" . sprintf(T_("An account has been created with the same IP address in the last %s hours"),SAME_IP_NEW_ACCONT_DELAY) . ".</p>");
								}
								else
								{
									echo("<p class=\"error\">" . T_("Your letters did not match the ones appearing in the image below") . ".</p>");
									$sessCaptcha = $_SESSION[CAPTCHA_SESSION_ID];
									if(!empty($sessCaptcha)) //Sometime it loses the captcha, so don't record that..
									{
										$Query = "INSERT INTO " . TABLE_PREFIX . "captchahits (Code ,Entered ,Username ,Email ,IP ,Source ,Time) values ('$sessCaptcha', '$captcha', '$aname', '$email', '" . $_SERVER['REMOTE_ADDR'] . "', 'Registration', NOW())";
										//echo("$Query<br>\n");
										$dbResult = $dblink->query($Query);
									}
								}
							}
							else
							{
								echo("<p class=\"error\">" . T_("Please enter the letters appearing in the image below") . ".</p>");
							}
						}
						else
							echo("<p class=\"error\">" . T_("This username is already taken") . ".</p>");
						
					}
				}
			}
			else
			{
				echo("<p class=\"error\">" . T_("The form is incomplete") . ".</p>");
			}
		
		}
		if(isset($_POST['no_js']) || !$_POST['submitted'])
		{
		
			$customTitle = T_("Registration");
			$jquery_script_form = true;
			require_once('header.php');
			if($user->isAdmin())
				echo("<h2>" . T_("Settings") . " -- " . T_("Add a new user") . "</h2>");
			else
				echo("<h2>" . T_("New User Registration") . "</h2>");
				?>
		
		<script type="text/javascript">
		<!--
		var checkAvailabilityWindow;
		function checkUsername()
		{
			var usernameToCheck = document.forms[0].aname.value;
			var topost = "checkUsername.php?usernameToCheck=" + usernameToCheck;
			if (usernameToCheck != "")
			{
				checkAvailabilityWindow = window.open(topost,'checkAvailabilityWindow','height=100, width=250, toolbar=no, directories=no, status=no, location=no, menubar=no, scrollbars=no, resizable=no, left=300, top=300, screenX=300, screenY=300');
			}
			else
				alert('<?php echo T_("Enter the username in the field");?>');
		}
		-->
		</script>
		<script type='text/javascript'>
		// prepare the form when the DOM is ready 
		$(document).ready(function() { 
			$('#nojswarning').remove();
		    var options = { 
		        target:        '#messagesDiv',
		        beforeSubmit:  showRequest,
				success:       showResponse
		    }; 
		 
		    // bind form using 'ajaxForm' 
		    $('#registration_form').ajaxForm(options); 
		}); 
		
		//pre-submit callback 
		function showRequest(formData, jqForm, options) { 
			$('.error').remove();
			$('#adminAdd').remove();
			$('#messagesDiv').append("<p class='notice'><?php echo T_("Processing") . "...";?><\/p>");
			return true; 
		} 
		// post-submit callback 
		function showResponse(responseText, statusText)  { 
		    $('.error').fadeTo("slow", 0.7);
		    $('.error').fadeTo("slow", 1);
		    if(responseText.indexOf("adminAdd") == -1 && ( responseText.indexOf("success") != -1 || responseText.indexOf("notice") != -1))
		    {
		    	$('#form_div').remove();
		    }
		    else if(responseText.indexOf("adminAdd") != -1)
		    {
		    	$('#adminAdd').fadeTo("slow", 0.7);
		    	$('#adminAdd').fadeTo("slow", 1);
		    	$('#registration_form').clearForm();
		    }
		} 
		</script>
		<div id="messagesDiv"></div>
		<p class="notice" id="nojswarning"><?php echo(T_("Javascript should be enabled to access all functionality"));?></p>
		<div id="form_div">
		<?php if(!$user->isAdmin()) { echo '<p>' . sprintf(T_("Sign up here to create a free account %s"),WEBSITE_NAME);?>! <a href="about.php"><?php echo sprintf(T_("Learn more</a> about %s"),WEBSITE_NAME) . '.</p>'; } ?>
		<form action="newuser.php" id="registration_form" method="post">
		<table>
			<tr>
					<td><span class="formsLabelRequired"><?php echo T_("Username");?></span></td>
					<td><input type="text" name="aname"  size="20" maxlength="20" value="<?php echo $aname; ?>" class="formtext" onfocus="this.select()">&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b>&nbsp;<span class="formsLabelSmall"><a href="javascript:checkUsername();"><?php echo T_("Check availability");?></a></span></td>
			</tr>
			<tr>
					<td><span class="formsLabelRequired"><?php echo T_("Password");?></span></td>
					<td><input type="password" name="pass"  size="20" maxlength="20" value="<?php echo $pass; ?>" class="formtext" onfocus="this.select()">&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
			</tr>
			<tr>
					<td><span class="formsLabelRequired"><?php echo T_("Retype password");?></span></td>
					<td><input type="password" name="pass2"  size="20" maxlength="20" value="<?php echo $pass2; ?>" class="formtext" onfocus="this.select()">&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('20 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
			</tr>
			<tr>
					<td><span class="formsLabelRequired"><?php echo T_("Email address");?></span></td>
					<td><input type="text" name="email" size="40" maxlength="100" value="<?php echo $email; ?>" class="formtext" onfocus="this.select()">&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('100 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
			</tr>
			<tr>
					<td><span class="formsLabelOpt"><?php echo T_("Password hint");?></span></td>
					<td><input type="text" name="hint" size="40" maxlength="150" value="<?php echo $hint; ?>" class="formtext" onfocus="this.select()">&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('150 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></td>
			</tr>
			<?php if(!$user->isAdmin() && CAPTCHA) { ?>
			<tr>
					<td></td>
					<td><img src="includes/visual-captcha.php" width="200" height="60" alt="<?php echo T_("Visual CAPTCHA");?>"></td>
			</tr>
			<tr>
					<td><span class="formsLabelRequired"><?php echo T_("Type image letters");?></span></td>
					<td><input type="text" name="captcha" size="20" maxlength="20" value="" class="formtext" onfocus="this.select()">&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('<?php echo T_("Please enter the letters appearing in the image below");?>');" onmouseout="return nd();">?</b></td>
			</tr>
			<?php } ?>
			<tr>
					<td><div style="display: none"><span class="formsLabelRequired"><?php echo T_("Email");?></span><input type="text" name="email2" size="40" maxlength="100" value="" class="formtext" onfocus="this.select()">&nbsp;<b style="text-decoration:underline; cursor:pointer;" onmouseover="return overlib('100 <?php echo T_("chars max");?>');" onmouseout="return nd();">?</b></div></td>
					<td>
						<input type="submit" name="submitted" value="<?php echo T_("Register");?>" class="genericButton">
					</td>
			</tr>
		</table>
		</form>
		<?php if(!$user->isAdmin()) { echo '<p>' . T_("All fields in bold are mandatory, and they can only contain letters, digits, '-' and '_' (except for the email)") . '.<br>';?>
		<?php echo T_("They must also respect the size limits (move the mouse over the ? at the end of each field)"). '.</p>'; } 
		else { echo("<p><a href=\"manageusers.php\"><< " . T_("Back") . "</a></p>"); }?>
		</div>
		<?php
		}
	}
	include('footer.php'); ?>