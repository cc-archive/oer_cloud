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

<form action='$formaction' method='post' id='frmUsers' onsubmit='return validateModifyUsersForm("frmUsers");'>
	<table class='adminTable'>
		<tr>
			<th>[x]</th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=username' title='Sort by username'>Username</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=name' title='Sort by name'>Name</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=email' title='Sort by email'>Email</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=homepage' title='Sort by homepage'>Homepage</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=uModified' title='Sort by modification date'>Modified</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=uDatetime' title='Sort by registration date'>Registered</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=isFlagged' title='Sort by flagged status'>Flg.</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=isAdmin' title='Sort by admin status'>Adm.</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=uStatus' title='Sort by activation status'>Act.</a></th>
			<th><a href='{$_SERVER['PHP_SELF']}?sort=activation_key' title='Sort by activation key'>Act.Key</a></th>
		</tr>

HTML;

if ( isset($_REQUEST['sort']) ) {
	$sort = $_REQUEST['sort'];
} else {
	$sort = "username";
}

$users = $userservice->getAllUsers($sort);

if ( ! empty($users) ) {
	foreach ( $users as $user ) {

		# change the background color of the row depending on the user's status
		# and also change bool status values to human readable ones
		if ( $user['uStatus'] ) {
			# user is activated
			$bgColor = "bgGreen";
			$user['uStatus'] = "Yes";
		} else {
			# user has registered but is not activated
			$bgColor = "bgYellow";
			$user['uStatus'] = "No";
		}
		if ( $user['isAdmin'] ) {
			# user is an admin
			$bgColor = "bgBlue";
			$user['isAdmin'] = "Yes";
		} else {
			$user['isAdmin'] = "No";
		}
		if ( $user['isFlagged'] ) {
			# user is flagged
			$bgColor = "bgRed";
			$user['isFlagged'] = "Yes";
		} else {
			$user['isFlagged'] = "No";
		}

		# break apart the date and time so that we can display the short date in the 
		# crowded table, and yet still show the date and time with a popup title.
		list($dateRegistered, $timeRegistered) = explode(" ", $user['uDatetime']);
		list($dateModified, $timeModified) = explode(" ", $user['uModified']);
	
		echo <<<HTML
			<tr class='$bgColor'>
				<td><input type='checkbox' name='userList[]' value='{$user['uId']}' /></td>
				<td>{$user['username']}</td>
				<td>{$user['name']}</td>
				<td>{$user['email']}</td>
				<td>{$user['homepage']}</td>
				<td><span title='$dateModified $timeModified'>$dateModified</span></td>
				<td><span title='$dateRegistered $timeRegistered'>$dateRegistered</span></td>
				<td style='text-align: center;'>{$user['isFlagged']}</td>
				<td style='text-align: center;'>{$user['isAdmin']}</td>
				<td style='text-align: center;'>{$user['uStatus']}</td>
				<td style='text-align: center;'>{$user['activation_key']}</td>
			</tr>
	
HTML;
	}
} else {
	echo "<tr><td colspan='11' style='text-align: center;'>No users to display</td></tr>\n";
}

echo <<<HTML
	</table>

	<div style='margin-top: 1em;'>
		<span>Perform the following action on the selected users:</span>
		<select name='uAction'>
			<option value='activate'>Activate</option>
			<option value='deactivate'>Deactivate</option>
			<option value='flag'>Flag</option>
			<option value='unflag'>Unflag</option>
			<option value='makeAdmin'>Make Admin</option>
			<option value='yankAdmin'>Yank Admin</option>
			<option value='delete'>Delete</option>
		</select>
		<input type='hidden' name='mod' value='users' /> 
		<input type='submit' name='doModifyUsers' value='Submit' /> 
	</div>
</form>

<div style='margin-top: 1em;'>
	<div style='float: left;'>Color legend:&nbsp;</div>
	<div class='bgBlue' style='padding-left: .5ex; padding-right: .5ex; float: left;'>Admin</div>
	<div class='bgGreen' style='padding-left: .5ex; padding-right: .5ex; float: left;'>Activated</div>
	<div class='bgYellow' style='padding-left: .5ex; padding-right: .5ex; float: left;'>Not activated</div>
	<div class='bgRed' style='padding-left: .5ex; padding-right: .5ex; float: left;'>Flagged</div>
</div>

HTML;

$this->includeTemplate($GLOBALS['bottom_include']);

?>

