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
$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
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

	if ( isset($_POST['doModifyUsers']) ) {
		$userservice->modifyUsers();
	} elseif ( isset($_POST['doModifyBookmarks']) ) {
		$bookmarkservice->modifyBookmarks();
	} elseif ( isset($_POST['doModifyTags']) ) {
		$bookmarkservice->modifyTags();
	}

    if($userservice->isAdminPassDefault($current_user['username'])) {
        $tplVars['error'] .= " PLEASE CHANGE THE ADMIN DEFAULT PASSWORD!";
    }
	$tplVars['loadjs'] = true;
    $tplVars['currentUsername'] = $current_user['username'];
    $tplVars['isLoggedOn'] = true;
    $tplVars['isAdmin'] = true;
    $tplVars['formaction'] = "admin.php";
    $tplVars['subtitle'] = T_("Administrative Console"); 

	if ( isset($_REQUEST['mod']) ) {
		switch ( $_REQUEST['mod'] ) {
			case "users":
    			$templateservice->loadTemplate('admin-users.tpl', $tplVars);
				break;
			case "bookmarks":
    			$templateservice->loadTemplate('admin-bookmarks.tpl', $tplVars);
				break;
			case "tags":
    			$templateservice->loadTemplate('admin-tags.tpl', $tplVars);
				break;
			default:
    			$templateservice->loadTemplate('admin-users.tpl', $tplVars);
		}
	} else {
    	$templateservice->loadTemplate('admin-users.tpl', $tplVars);
	}

}

?>
