<?php
/***************************************************************************
Copyright (C) 2004 - 2006 Marcus Campbell
http://sourceforge.net/projects/scuttle/
http://scuttle.org/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
***************************************************************************/

require_once('header.inc.php');
require_once('recaptchalib.php');
$userservice =& ServiceFactory::getServiceInstance('UserService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');

$tplVars = array();
if ($_POST['submitted']) {
    $posteduser = trim(utf8_strtolower($_POST['username']));
    $captcha_invalid = false;

    // validate recaptcha
    if ($GLOBALS['use_recaptcha']) {
      $recaptcha_result = 
	recaptcha_check_answer($GLOBALS['recaptcha_private_key'],
			       $_SERVER['REMOTE_ADDR'],
			       $_POST['recaptcha_challenge_field'],
			       $_POST['recaptcha_response_field']);
      
      if ($recaptcha_result->is_valid) {
	// great!
      }
      else {
	$captcha_invalid = true;
      }
    }
      
      // Check if form is incomplete
    if (!($posteduser) || !($_POST['password']) || !($_POST['email'])) {
        $tplVars['error'] = T_('You <em>must</em> enter a username, password and e-mail address.');
    

    // Check if the user failed the captcha
    } elseif ($captcha_invalid) {
      $tplVars['error'] = T_('You did not correctly prove you are a human.  Go back and try reading the book again.');

    // Check if username is reserved
    } elseif ($userservice->isReserved($posteduser)) {
        $tplVars['error'] = T_('This username has been reserved, please make another choice.');

    // Check if username already exists
    } elseif ($userservice->getUserByUsername($posteduser)) {
        $tplVars['error'] = T_('This username already exists, please make another choice.');
    
	// Check if password is at least 6 chars
	} elseif ( strlen($_POST['password']) < 6 ) {
        $tplVars['error'] = T_('Password must be at least 6 characters long.');

    // Check if e-mail address is valid
    } elseif (!$userservice->isValidEmail($_POST['email'])) {
        $tplVars['error'] = T_('E-mail address is not valid. Please try again.');

    // Register details
    } elseif ($userservice->addUser($posteduser, $_POST['password'], $_POST['email'])) {
		/* Send the user an activation email */

		# user the codes existing function for creating random password to create a 
		# hopefully random activation key and then insert it into the users record
		$activationKey = $userservice->_randompassword();
		$activationUrl = "http://{$_SERVER['SERVER_NAME']}{$root}activate/?key=$activationKey";
		$sql = sprintf ("
			UPDATE %susers SET activation_key = '$activationKey'
			WHERE username = '%s'
			",
			$GLOBALS['tableprefix'],
			$posteduser
		);
		$userservice->db->sql_query($sql);
		$rcptTo = $_POST['email'];
		$subject = "Activate your new account at ccLearn's OER Cloud";
		$message = <<<MSG
You have received this email because someone registered at http://oercloud.creativecommons.org using
this email address.  If this was not you, then please disregard this message, otherwise visit the
following link to activate your account. You can also paste this address into your browser's
address bar if your email program does not allow you to click on links.

$activationUrl

Thank you for your interest in ccLearn,

The ccLearn Team.
MSG;

		mail($rcptTo,$subject,$message);
        $tplVars['msg'] = "You were successfully registered, but you still need to activate the account.  Please check your email for a confirmation.";
    } else {
        $tplVars['error'] = T_('Registration failed. Please try again.');
    }
 }

$tplVars['loadjs']      = true;
$tplVars['subtitle']    = T_('Register');
$tplVars['formaction']  = createURL('register');
$templateservice->loadTemplate('register.tpl', $tplVars);

?>
