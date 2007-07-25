<?php

$userservice =& ServiceFactory::getServiceInstance('UserService');

$this->includeTemplate($GLOBALS['top_include']);

if ( ! empty($updateResults) ) {
	echo "<p>$udpateResults</p>\n";
}

echo <<<HTML
<div style='margin-bottom: 1em;'>
	<span style='font-size: large;'><a href='#' style='color: #000000;' onclick='showUsers("adminUsers", "adminTags");'>users</a></span> |
	<span style='font-size: large;'><a href='#' style='color: #000000;' onclick='showTags("adminTags", "adminUsers");'>tags</a></span>
</div>

<hr />

<div id='adminUsers'>
	<form action='$formaction' method='post' id='frmUsers' onsubmit='return validateModifyUsersForm("frmUsers");'>
		<table class='usersTable'>
			<tr>
				<th>[x]</th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=username'>Login</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=name'>Name</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=email'>Email</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=homepage'>Homepage</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=uModified'>Modified</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=uDatetime'>Registered</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=isFlagged'>Flg.</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=isAdmin'>Adm.</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=uStatus'>Act.</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=activation_key'>Act.Key</a></th>
			</tr>

HTML;

if ( isset($_REQUEST['sort']) ) {
	$sort = $_REQUEST['sort'];
} else {
	$sort = "username";
}

$users = $userservice->getAllUsers($sort);
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

echo <<<HTML
		</table>
		<div style='margin-top: 1em;'>
			<span>Perform the following action on the selected users:</span>
			<select name='modifyUsersAction'>
				<option value='activate'>Activate</option>
				<option value='deactivate'>Deactivate</option>
				<option value='flag'>Flag</option>
				<option value='unflag'>Unflag</option>
				<option value='makeAdmin'>Make Admin</option>
				<option value='yankAdmin'>Yank Admin</option>
				<option value='delete'>Delete</option>
			</select>
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
</div>


<div id='adminTags' style='display: none;'>
	<form action='$formaction' method='post'>
		Change all tags with tag <input type='text' name='merge_tag_from'> to <input type='text' name='merge_tag_to'>
		<input type='submit' name='doModifyTags' value='Apply Changes' /><br /><br />
		Automatically make this conversion for new tags? <input type='checkbox' name='merge_trigger_enable'>
	</form>
</div>

HTML;

$this->includeTemplate($GLOBALS['bottom_include']);

?>

