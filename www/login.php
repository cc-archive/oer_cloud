<?php
/***************************************************************************
Copyright (C) 2004 - 2007 Scuttle project
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
$userservice =& ServiceFactory::getServiceInstance('UserService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');

$tplVars = array();

// Load bookmarks if already logged in
if ($userservice->isLoggedOn()) {
    $cUser      = $userservice->getCurrentUser();
    $cUsername  = utf8_strtolower($cUser[$userservice->getFieldName('username')]);
    if( $userservice->isAdminByUsername($cUsername))
        $tplVars['isAdmin'] = true;
    header('Location: '. createURL('bookmarks', $cUsername));
}

$login = false;
if (isset($_POST['submitted']) && isset($_POST['username']) && isset($_POST['password'])) {
    $posteduser = trim(utf8_strtolower($_POST['username']));
    $isUserFlagged = $userservice->isFlaggedByUsername($posteduser);
    if(!$isUserFlagged) {
        if($GLOBALS['recaptcha_private_key']) {
            require_once('recaptchalib.php');
            $privatekey = $GLOBALS['recaptcha_private_key'];
            $resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"],
                $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
            }
        if ($resp->is_valid) {
        $login          = $userservice->login($posteduser, $_POST['password'], ($_POST['keeppass'] == 'yes'), $path);
        switch ($login['message']) {
            case 'success':
            header('Location: '. createURL('bookmarks', $posteduser));
                break;
            case 'unverified':
                $tplVars['error'] = T_('You must verify your account before you can log in.');
                break;
            default:
        $tplVars['error'] = T_('The details you have entered are incorrect. Please try again.');
    }
        } else // failed CAPTCHA
            $tplVars['error'] = T_('The details you have entered are incorrect. Please try again.');
    }
    else // flagged user
        $tplVars['error'] = T_('Your account has been disabled.  Please contact the administrator to enable your account.');
}

$tplVars['loadjs']      = true;
$tplVars['subtitle']    = T_('Log In');
$tplVars['formaction']  = createURL('login');
$tplVars['querystring'] = filter($_SERVER['QUERY_STRING']);
$templateservice->loadTemplate('login.tpl', $tplVars);
?>
