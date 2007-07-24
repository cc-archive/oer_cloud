<?php

$userservice =& ServiceFactory::getServiceInstance('UserService');

$this->includeTemplate($GLOBALS['top_include']);

if ( ! empty($updateResults) ) {
	echo "<p>$udpateResults</p>\n";
}

echo <<<HTML
<script type='text/javascript'>

function getElement(elemid) {
	/* the former for Firefox and crew, the latter for IE */
	return (document.getElementById) ? document.getElementById(elemid) : document.all[elemid];
}

function showUsers(userDiv,tagDiv) {
	var divAdminUsers = getElement(userDiv);
	var divAdminTags = getElement(tagDiv);
	divAdminUsers.style.display = "";
	divAdminTags.style.display = "none";
	return true;
}

function showTags(tagDiv,userDiv) {
	var divAdminTags = getElement(tagDiv);
	var divAdminUsers = getElement(userDiv);
	divAdminTags.style.display = "";
	divAdminUsers.style.display = "none";
	return true;
}

function validateModifyUsersForm(formId) {
	var usersForm = getElement(formId);
	if ( usersForm.modifyUsersAction.options[usersForm.modifyUsersAction.selectedIndex].value == "delete" ) {
		var msg = "Are you sure that you want to permanently delete the selected users and all of their bookmarks and tags?";
		if ( confirm(msg) ) {
			var doModifyUsers = document.createElement('input');
			doModifyUsers.setAttribute('type','hidden');
			doModifyUsers.setAttribute('name','doModifyUsers');
			usersForm.appendChild(doModifyUsers); 
			usersForm.submit;
			return true;
		} else {
			return false;
		}
	}
	return true;
}

</script>

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
				<th><a href='{$_SERVER['PHP_SELF']}?sort=uStatus'>Act.</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=isFlagged'>Flg.</a></th>
				<th><a href='{$_SERVER['PHP_SELF']}?sort=isAdmin'>Adm.</a></th>
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

	echo <<<HTML
			<tr class='$bgColor'>
				<td><input type='checkbox' name='userList[]' value='{$user['uId']}' /></td>
				<td>{$user['username']}</td>
				<td>{$user['name']}</td>
				<td>{$user['email']}</td>
				<td>{$user['homepage']}</td>
				<td>{$user['uModified']}</td>
				<td>{$user['uDatetime']}</td>
				<td style='text-align: center;'>{$user['uStatus']}</td>
				<td style='text-align: center;'>{$user['isFlagged']}</td>
				<td style='text-align: center;'>{$user['isAdmin']}</td>
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

