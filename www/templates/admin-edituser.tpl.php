<?php

$userservice =& ServiceFactory::getServiceInstance('UserService');

$this->includeTemplate($GLOBALS['top_include']);

if ( ! empty($updateResults) ) {
	echo "<p>$udpateResults</p>\n";
}

echo <<<HTML
<div style='margin-bottom: 1em;'>
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=users' style='color: #000000;'>users</a></span> |
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=bookmarks' style='color: #000000;'>bookmarks</a></span> |
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=tags' style='color: #000000;'>tags</a></span>
</div>

<hr />

<div style='clear: left; padding-bottom: 1em; font-weight: bold;'>
	Edit user :
</div>

<form action='$formaction' method='post' id='frmEditUser' onsubmit='return validateEditUserForm("frmEditUser");'>
	<div><input type='text' name='username' size='40' value='{$user['username']}' /> Username</div>
	<div><input type='text' name='password' size='40' value='' /> Password (leave blank if you don't want to change the password)</div>
	<div><input type='text' name='name' size='40' value='{$user['name']}' /> Full name</div>
	<div><input type='text' name='email' size='40' value='{$user['email']}' /> Email</div>
	<div><input type='text' name='homepage' size='40' value='{$user['homepage']}' /> Homepage</div>
	<div style='margin-top: 1em;'><input type='submit' name='doEditUser' value='Edit' /></div>
	<input type='hidden' name='uAction' value='edit' />
	<input type='hidden' name='userid' value='{$user['uId']}' />
</form>

HTML;

$this->includeTemplate($GLOBALS['bottom_include']);

?>

