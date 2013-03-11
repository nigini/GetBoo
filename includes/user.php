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

class User
{
	private $usernameSession;
	private $privSession;
	private $styleSession;
	private $layoutSession;

	function User()
	{
		$this->usernameSession = "";
		$this->privSession = "";
		$this->styleSession = "";
		$this->layoutSession = "";
		$this->retrieveSessionUser();
	}

	//Retrieve the session data
	function retrieveSessionUser()
	{
		$this->usernameSession = $_SESSION['nameInSession'];
		$this->privSession = $_SESSION['privInSession'];
		$this->styleSession = $_SESSION['styleInSession'];
		$this->layoutSession = $_SESSION['layoutInSession'];
	}

	//Return the username of the user
	function getUsername()
	{
		return $this->usernameSession;
	}

	//Change the current username of the active session
	function changeUsername($newUsername)
	{
		$this->usernameSession = $newUsername;
		$_SESSION['nameInSession'] = $this->usernameSession;
	}

	//Return the priv of the user
	function getPriv()
	{
		return $this->privSession;
	}

	//Return the style of the user
	function getStyle()
	{
		return $this->styleSession;
	}

	//Return the bookmark's layout of the user
	function getLayout()
	{
		return $this->layoutSession;
	}

	//Set the bookmark's layout of the user
	function setLayout($layout)
	{
		$this->layoutSession = $layout;
		$_SESSION['layoutInSession'] = $this->layoutSession;
	}

	//Log the user without a password
	function forceLogin($username)
	{
		include('conn.php');
		require_once('protection.php');
		$Query = sprintf("select pass from " . TABLE_PREFIX . "session where name=%s",
								quote_smart($username));
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$passEncrypt = ("{$row["pass"]}");
		}
		

		//Log in the user
		$this->login($username, $passEncrypt, true);
	}

	//Log the user
	function login($username, $pass, $passEncrypted = false)
	{
		global $SETTINGS;
		/*
			$resultArr['message'] : the message displaying either the error or success, used with p class ("error"/"success")
			$resultArr['optmessage'] : the message explaining more in details the error
			$resultArr['success'] : the bool saying whether or not the login was successfull
			$resultArr['username'] : the "real" username of the user
		*/
		$resultArr = array();
		$resultArr['success'] = false;
   
   		include($SETTINGS['path_mod'] . 'conn.php');
		if($username != null && $pass != null)
		{
			require_once($SETTINGS['path_mod'] . 'includes/protection.php');
			//Check if the name and pass strings are valid (for protection)
			if(!(valid($username, 20) && valid($pass, 50)))
			{
				$resultArr['message'] = T_("Please check your username and password and make sure that they are correctly spelled.");
			}
			else
			{
				//Check if the name exists on the database

				$Query = sprintf("select name from " . TABLE_PREFIX . "session where name=%s",
								quote_smart($username));
				//echo($Query . "<br>\n");
				$dbResult = $dblink->query($Query);

				if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
				{
					//Check if user is demo, to avoid blocking the account
					$democheck = strtoupper($username);
					$logtries = 0;
					if($democheck != "DEMO")
					{
						//Check if user is connecting more than 3 times
						$Query = sprintf("select success from " . TABLE_PREFIX . "loginhits where name=%s and " . DATE_DIFF_SQL . " time) < " . WAITTIME . " order by time", quote_smart($username));
						//echo($Query . "<br>\n");
						$dbResult = $dblink->query($Query);

						$success = 'N';
						while($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
						{
							$success = ("{$row["success"]}");
							if($success == 'N')
								$logtries++;
						}
						if($success == 'Y')
							$logtries = 0;
					}

					//Check if user tried more than 3 times
					if($logtries < 3)
					{
						//Encrypt password if not already
						if(!$passEncrypted)
							$passencrypt = $this->encryptPassword($pass);
						else
							$passencrypt = $pass;

						// Make a safe query
						$Query = sprintf("select name, status from " . TABLE_PREFIX . "session where name=%s and pass=%s",
								quote_smart($username),
								quote_smart($passencrypt));
						//echo($Query);

						$dbResult = $dblink->query($Query);
						if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
						{
							$status = ("{$row["status"]}");
							$realname = ("{$row["name"]}");

							//Check if user is activated, if not an admin
							if($status != "admin" && $democheck != "DEMO")
							{
								$Query = "select name, activated from " . TABLE_PREFIX . "activation where (name='" . $username . "')";
								//echo($Query . "<br>\n");
								$dbResult = $dblink->query($Query);
								$yesno = '';
								if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
								{
									$yesno = ("{$row["activated"]}");
								}
							}

							if($yesno=='Y' || ($status == "admin" || $democheck == "DEMO"))
							{
								$resultArr['success'] = true;

								//Set the session variabless and return vars
								$_SESSION['privInSession'] = "normal";
								$_SESSION['nameInSession'] = $realname;
								$this->usernameSession = $realname;
								$resultArr['username'] = $realname;
								
								//check if the user wants to be remember: set cookie
								if(isset($_POST['remember']))
								{
									setcookie("gbname", $realname, time()+60*60*24*100, "/");
									setcookie("gbpass", $passencrypt, time()+60*60*24*100, "/");
								}

								$resultArr['message'] = (T_("Welcome back") . " <b>" . $realname ."</b>!");

								$Query = "SELECT lastlog AS formatted_time, style from " . TABLE_PREFIX . "session where name='" . $username ."'";
								//echo($Query . "<br>\n");
								$dbResult = $dblink->query($Query);
								if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
								{
									$date1 = ("{$row["formatted_time"]}");
									require_once('convert_date.php');
									$date2 = convert_date($date1, true);
									$resultArr['message'] .= (" " . T_("Your last login was on") . " " . $date2);
									$style = ("{$row["style"]}");
								}

								//Check user's browser and user's preference (to do)
								include('browser.php');
								$br = new Browser;
								$_SESSION['browser'] = $br->Name;
								$browser = $_SESSION['browser'];
								if($browser == "MSIE")
									$browser = "Internet Explorer";

								$_SESSION['layoutInSession'] = "no_js";
								$_SESSION['styleInSession'] = "";
								if($style == "Auto")
								{
									if($browser == "Internet Explorer")
										$_SESSION['styleInSession'] = "IE";
									else if($browser == "Opera")
										$_SESSION['styleInSession'] = "Opera";
									else
										$_SESSION['styleInSession'] = "Firefox";
								}
								else
									$_SESSION['styleInSession'] = $style;

								$this->styleSession = $_SESSION['styleInSession'];

								//Update last logging time
								//$time = date('Y-m-d H:i:s');
								$Query = "update " . TABLE_PREFIX . "session set LastLog = now(), LastActivity = now() where name='" . $username . "'";
								$dbResult = $dblink->query($Query);

								//Check for user's privileges
								$Query = "select status from " . TABLE_PREFIX . "session where name='" . $username . "'";
								$dbResult = $dblink->query($Query);
								//echo($Query . "<br>\n");
								$found = false;
								$priv ="";
								if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
								{
									$priv = ("{$row["status"]}");
									$found = true;
								}

								// TODO: Please change the following emails below

								$loginSuccess = 'Y';
								if($found && $priv=="admin")
									$_SESSION['privInSession'] = "admin";
								else if($found && $priv=="disabled")
								{
									$_SESSION['privInSession'] = "disabled";
									$resultArr['success'] = false;
									$_SESSION['nameInSession'] = null;
									$this->usernameSession = null;
									$resultArr['username'] = null;
									$resultArr['message'] = (T_("Your account has been disabled") . ".");
									$loginSuccess = 'D';
									$resultArr['optmessage'] = (sprintf(T_("This has been done by our anti-spam system. If you think it is a mistake, please email <a href=\"%s\">%s</a>.<br>\n<i>Expect 1-2 days of response by email</i>"),"mailto:support@getboo.com", "support@getboo.com"));
								}
								else
									$_SESSION['privInSession'] = "normal";

								$this->privSession = $_SESSION['privInSession'];

								// Update loginhits table
								$Query = "
											INSERT INTO " . TABLE_PREFIX . "loginhits
											(Name, Time, IP, Success)
											VALUES
											('$realname', now(), '" . $_SERVER['REMOTE_ADDR'] . "', '$loginSuccess')
											";

								$dbResult = $dblink->query($Query) or die("Couldn't update the database");
							}
							else
							{
								$resultArr['message'] = (T_("Your account is not activated") . ".");
								$resultArr['optmessage'] = ("" . sprintf(T_("You must first <a href=\"%s\">activate</a> your account with the code emaild to you when you registered"),"activate.php") . ".<br>\n " . sprintf(T_("Please check your email in order to get your activation code<br>\n or email <a href=\"%s\">%s</a> if you don't receive any email from us after 24h"),"mailto:registration@getboo.com", "registration@getboo.com") . ".<br>\n<i>" . T_("Expect 1-2 days of response by email") . "</i>");
							}
						}
						else
						{
							$resultArr['message'] = (T_("The password is invalid") . ".");
							if($democheck != "DEMO")
							{
								//$time = date('Y-m-d H:i:s');
								$Query = "
										INSERT INTO " . TABLE_PREFIX . "loginhits
										(Name, Time, IP, Success)
										VALUES
										('$username', now(), '" . $_SERVER['REMOTE_ADDR'] . "', 'N')
										";
							}

							$dbResult = $dblink->query($Query);

						}
					}
					else
					{
						$resultArr['message'] = (T_("Too many login tries") . ".");
						$resultArr['optmessage'] = (sprintf(T_("You have tried to log in more than 3 times unsuccessfully for this account in the last %s minutes"),(WAITTIME / 60)) . ".<br><br>" . sprintf(T_("Please try again in %s minutes"),(WAITTIME / 60)) . ".");
					}
				}
				else
				{
					$resultArr['message'] = (T_("The user does not exist") . ".");
				}
				
			}
		}
		else
		{
			$resultArr['message'] = (T_("Username or Password is missing") . "");
		}
		return $resultArr;
	}

	//Encrypt the password
	function encryptPassword($password)
	{
		return sha1(trim($password));
	}

	//Return true if the user is logged in
	function isLoggedIn($fromLogOut = false)
	{
		if(strlen($this->usernameSession) == 0 && !$fromLogOut)
		{
			return $this->validateRememberMeCookie();
		}
		return ($this->usernameSession != "");
	}
	
	function validateRememberMeCookie()
	{
		// Check if user has been remembered (with cookies)
	   if(isset($_COOKIE['gbname']) && isset($_COOKIE['gbpass'])){
	      $username = $_COOKIE['gbname'];
	      $pass = $_COOKIE['gbpass'];
	      $resultArr = $this->login($username, $pass, true);
	      return $resultArr['success'];
	   }
	   return false;	  
   }

	//Return true if the user has admin power
	function isAdmin()
	{
		return ($this->privSession == "admin");
	}

	//Change user's password
	function changePassword($actpass, $newpass, $renewpass)
	{
		$resultArr = array();
		$resultArr['success'] = false;
		$resultArr['message'] = "";

		if($actpass != null && $newpass != null && $renewpass != null && valid($actpass, 50) && valid($newpass, 20) && valid($renewpass, 20))
		{
			//Encrypt password
			$passencrypt = $this->encryptPassword($actpass);

			include('conn.php');

			// Make a safe query
			$Query = sprintf("select name, pass from " . TABLE_PREFIX . "session where name=%s and pass=%s",
					quote_smart($this->usernameSession),
					quote_smart($passencrypt));

			$dbResult = $dblink->query($Query);
			$count = 0;
			if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			{
				$count++;
			}

			if($count==0)
			{
				$resultArr['message'] = T_("The actual password is incorrect");
			}
			else if($newpass != $renewpass)
			{
				$resultArr['message'] = T_("The new password does not match in both fields");
			}
			else
			{
				//Encrypt password
				$newpassencrypt = $this->encryptPassword($newpass);

				$Query = "update " . TABLE_PREFIX . "session set pass='" . $newpassencrypt . "' where name='" . $this->usernameSession . "'";
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows == 1)
				{
					$resultArr['message'] = T_("You have successfully changed your password");
					$resultArr['success'] = true;
				}
				else
					$resultArr['message'] = T_("There has been a problem while updating your password. Don't type the same password.");
			}

			
		}
		else
		{
			$resultArr['message'] = T_("Missing values or invalid length");
		}
		return $resultArr;
	}

	//Change user's account information
	function changeAccountInfo($email, $passhint, $style, $donor = 0, $realname = "", $displayemail = 0, $website = "", $information = "")
	{
		$resultArr = array();
		$resultArr['success'] = false;
		$resultArr['message'] = "";
		$resultArr['optmessage'] = "";

		require_once('includes/protection.php');

		if($email!=null && $style!=null)
		{
			if(check_email_address($email))
			{
				include('conn.php');

				if($donor && IS_GETBOO)
					$donorQuery = ", realname='" . $realname . "', displayemail='" . $displayemail . "', website='" . $website . "', information='" . $information . "'";

				$Query = "update " . TABLE_PREFIX . "session set email='" . $email . "', passhint='" . $passhint . "', style='" . $style . "'$donorQuery where name='" . $this->usernameSession . "'";
				//echo($Query . "<br>\n");
				$AffectedRows = $dblink->exec($Query);
				if($AffectedRows >= 0)
				{
					$resultArr['success'] = true;
					$resultArr['message'] = T_("You have successfully updated your account");
					$resultArr['optmessage'] = "";

					if($style == "Auto")
					{
						$resultArr['optmessage'] = T_("Note: If you just changed the style to Auto, it will take effect the next time you will login.");
					}
					else
					{
						$_SESSION['styleInSession'] = $style;
					}
				}
				
			}
			else
				$resultArr['message'] = T_("The email address is invalid.");
		}
		else
		{
			$resultArr['message'] = T_("The form is incomplete");
		}
		return $resultArr;
	}

	//Update the last activity variable which accounts for user's activity
	function updateLastVisit($clear = false, $path = "")
	{
		include($path . "conn.php");

		//Check if its an admin accessing another account, so that we don't update the wrong account!
		if($this->isAdmin() && isset($_SESSION["oldname"]))
			$usernameToUpdate = $_SESSION["oldname"];
		else
			$usernameToUpdate = $this->usernameSession;

		if($clear)
			$Query = "update " . TABLE_PREFIX . "session set LastActivity = '0000-00-00 00:00:00' where name='" . $usernameToUpdate . "'";
		else
			$Query = "update " . TABLE_PREFIX . "session set LastActivity = now() where name='" . $usernameToUpdate . "'";
		$dbResult = $dblink->query($Query);
		
	}

	//Check if the the user's session has expired
	function checkTimeOut($path = "")
	{
		include($path . "conn.php");

		//Check if its an admin accessing another account, so that we don't log him off!
		if($this->isAdmin() && isset($_SESSION["oldname"]))
			$usernameToCheck = $_SESSION["oldname"];
		else
			$usernameToCheck = $this->usernameSession;

		$Query = "select * from " . TABLE_PREFIX . "session where " . DATE_DIFF_SQL . " LastActivity) < " . USER_TIMEOUT . " and name='" . $usernameToCheck . "'";
		$dbResult = $dblink->query($Query);
		if(!$row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			$this->logout();

		//Log out the user after a maximum period of time since he logged in : SPAM to avoid bot logged in for too long
		$Query = "select * from " . TABLE_PREFIX . "session where " . DATE_DIFF_SQL . " LastLog) < " . USER_MAX_TIMEOUT . " and name='" . $usernameToCheck . "'";
		$dbResult = $dblink->query($Query);
		if(!$row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
			$this->logout();

		
	}

	//End the user's session
	function logout()
	{
		global $SETTINGS;
		$this->updateLastVisit(true, $SETTINGS['path_mod']);
		$_SESSION = array();
		@session_destroy();
		$this->usernameSession = "";
		$this->privSession = "";
		$this->styleSession = "";
		$this->layoutSession = "";
		$this->retrieveSessionUser();
		//"delete cookies if any
		if(isset($_COOKIE['gbname']) && isset($_COOKIE['gbpass'])){
			setcookie("gbname", "", time()-60*60*24*100, "/");
			setcookie("gbpass", "", time()-60*60*24*100, "/");
		}
	}

	//Please modify with your emails
	//Send the activation email
	function sendActivationEmail($email, $username, $pass, $id)
	{
		//Prepare the email information
		$domain = $_SERVER['REMOTE_ADDR'];
		$mailheaders = "From: " . sprintf(T_("%s Registration"),WEBSITE_NAME) . " <> \r\n";
		$mailheaders .= "Reply-To: registration@getboo.com\r\n";
		$emailmsg = sprintf(T_("Thank you for registering at %s!\n\nYour account information is"),WEBSITE_NAME) . ":\n\n----------------\n" . T_("Username") . ": $username\n" . T_("Password") . ": $pass\n----------------\n\n" . T_("Please keep this information, but in the event your forget your password you can recover it using your username and email.\nTo confirm your registration, please use this address") . ":\n" . WEBSITE_ROOT . "activate.php?aname=$username&id=$id\n" . T_("You can also go to the Activation page (from Login / Activate Account)\nand enter the activation code") . ": $id\n\n" . sprintf(T_("Sincerely,\n%s"),WEBSITE_NAME) . "\n\n" . sprintf(T_("If you didn't register for a new account, please forward this message to: %s"),"abuse@getboo.com") . "\n" . T_("IP address of the user registering for a new account") . ": $domain";

		//Message in case the php mail function doesn't work
		$dieMessage = str_replace("\n", "<br>", $emailmsg);

		//Send the email
		@mail($email, sprintf(T_("Activate your %s account"),WEBSITE_NAME) . "!", $emailmsg, $mailheaders) or die("<p class=\"notice\">" . T_("Could not send the email: Here is a copy of the email") . ":</p><p>$dieMessage</p>");
	}

	// Check if a user is a donor
	public static function isDonor($username)
	{
		include('conn.php');
		$Query = "SELECT donor from " . TABLE_PREFIX . "session where name='" . $username ."'";
		//echo($Query . "<br>\n");
		$dbResult = $dblink->query($Query);
		if($row =& $dbResult->fetchRow (DB_FETCHMODE_ASSOC))
		{
			$donor = ("{$row["donor"]}");
		}
		
		return $donor;
	}
}
?>