<?php
/***************************************************************************
Copyright (C) 2004 - 2006 Scuttle project
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
$current_user = $userservice->getCurrentUser();

if(!$userservice->isLoggedOn()) {
    // someone that's not logged in tries to access the admin page...
    // redirect him to the login page
    
    $tplVars['subtitle']    = T_('Log In');
    $tplVars['formaction']  = createURL('login');
    $tplVars['querystring'] = filter($_SERVER['QUERY_STRING']);

    $templateservice->loadTemplate('login.tpl', $tplVars);
}
else if(!$userservice->isAdminByUsername($current_user['username'])) {
    // regular user tries to access admin page: redirect to bookmarks page

    $posteduser = trim(utf8_strtolower($current_user['username']));
    header('Location: '. createURL('bookmarks', $posteduser));
    $tplVars['currentUsername'] = $current_user['username'];
    $tplVars['isLoggedOn'] = true;
    $templateservice->loadTemplate($templatename, $tplVars);
}
else { 
    // legitimate administrator accessing the page
    if(0 < count($_POST)) { 
        // at least one variable was POSTed
        $paaResult = $userservice->performAdminActions($_POST);
        switch($paaResult[0]) {
            case 0: 
                // keeping the T_ in case 0 (and not just the error msg)
                // for translation purposes
                $tplVars['error'] = T_('An error occurred with the requested actions: ') 
                    . $paaResult[1]; 
                break;
            case 1:
                $tplVars['error'] = T_('Your changes were sucessfully made'); 
                break;
            case 2:
                $tplVars['error'] = T_('No action was taken');
                break;
        }
    }
    if($userservice->isAdminPassDefault($current_user['username'])) {
        $tplVars['error'] .= " PLEASE CHANGE THE ADMIN DEFAULT PASSWORD!";
    }
    $tplVars['currentUsername'] = $current_user['username'];
    $tplVars['isLoggedOn'] = true;
    $tplVars['isAdmin'] = true;
    $tplVars['formaction'] = "admin.php";
    $tplVars['subtitle'] = T_("Administrative Console"); 
    $templateservice->loadTemplate('admin.tpl', $tplVars);
}

?>
