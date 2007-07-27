<?php

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');

$this->includeTemplate($GLOBALS['top_include']);

if ( ! empty($updateResults) ) {
	echo "<p>$udpateResults</p>\n";
}

echo <<<HTML
<div style='margin-bottom: 1em;'>
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=users' style='color: #000000;';>users</a></span> |
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=bookmarks' style='color: #000000;'>bookmarks</a></span> |
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=tags' style='color: #000000;'>tags</a></span>
</div>

<hr />

<div style='font-size: large; margin-top: 1em; margin-bottom: 1em;'>Flagged & disabled bookmarks:</div>

<form action='$formaction' method='post' id='frmBookmarks' onsubmit='return validateModifyBookmarksForm("frmBookmarks");'>
	<table class='adminTable'>
		<tr>
			<th>[x]</th>
			<th>bId</th>
			<th>Flags</th>
			<th>Title</th>
			<th>Description</th>
			<th>Modified</th>
			<th>Registered</th>
			<th>Disabled</th>
			<th>Username</th>
			<th>IP</th>
			<th>Flagged By</th>
		</tr>

HTML;

$bookmarks = $bookmarkservice->getFlaggedBookmarks();

if ( ! empty($bookmarks) ) {
	foreach ( $bookmarks as $bookmark ) {

		# change the background color of the row depending on the bookmark's status
		# and also change bool status values to human readable ones
		if ( $bookmark['bFlagCount'] > 0 ) {
			# bookmark is flagged
			$bgColor = "bgYellow";
		}
		if ( $bookmark['bStatus'] == 1 ) {
			# bookmark is disabled
			$bgColor = "bgRed";
			$bookmark['bStatus'] = "Yes";
		} else {
			$bookmark['bStatus'] = "No";
		}

		# break apart the date and time so that we can display the short date in the 
		# crowded table, and yet still show the date and time with a popup title.
		list($dateRegistered, $timeRegistered) = explode(" ", $bookmark['bDatetime']);
		list($dateModified, $timeModified) = explode(" ", $bookmark['bModified']);

		# trim the description to something more reasonable
		$bDescription = substr($bookmark['bDescription'], 0, 75);	
		$bDescription = "$bDescription ...";

		echo <<<HTML
			<tr class='$bgColor'>
				<td><input type='checkbox' name='bookmarkList[]' value='{$bookmark['bId']}' /></td>
				<td>{$bookmark['bId']}</td>
				<td style='text-align: center;'>{$bookmark['bFlagCount']}</td>
				<td><a href='{$bookmark['bAddress']}' target='_blank'>{$bookmark['bTitle']}</a></td>
				<td>$bDescription</td>
				<td><span title='$dateModified $timeModified'>$dateModified</span></td>
				<td><span title='$dateRegistered $timeRegistered'>$dateRegistered</span></td>
				<td style='text-align: center;'>{$bookmark['bStatus']}</td>
				<td>{$bookmark['username']}</td>
				<td style='text-align: center;'>{$bookmark['bIp']}</td>
				<td style='text-align: center;'>{$bookmark['flaggedBy']}</td>
			</tr>

HTML;
	}
} else {
	echo "<tr><td colspan='11' style='text-align: center;'>No bookmarks to display</td></tr>\n";
}

echo <<<HTML
	</table>
	<div style='margin-top: 1em;'>
		<span>Perform the following action on the selected bookmarks:</span>
		<select name='bAction'>
			<option value='unflag'>Unflag</option>
			<option value='disable'>Disable</option>
			<option value='enable'>Enable</option>
			<option value='delete'>Delete</option>
		</select>
		<input type='hidden' name='mod' value='bookmarks' /> 
		<input type='submit' name='doModifyBookmarks' value='Submit' /> 
	</div>
</form>

<div style='margin-top: 1em;'>
	<div style='float: left;'>Color legend:&nbsp;</div>
	<div class='bgYellow' style='padding-left: .5ex; padding-right: .5ex; float: left;'>Flagged</div>
	<div class='bgRed' style='padding-left: .5ex; padding-right: .5ex; float: left;'>Disabled</div>
</div>
HTML;

$this->includeTemplate($GLOBALS['bottom_include']);

?>

