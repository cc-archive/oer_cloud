<?php

$tagservice =& ServiceFactory::getServiceInstance('TagService');

$this->includeTemplate($GLOBALS['top_include']);

echo <<<HTML
<div style='margin-bottom: 1em;'>
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=users' style='color: #000000;'>users</a></span> |
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=bookmarks' style='color: #000000;'>bookmarks</a></span> |
	<span style='font-size: large;'><a href='{$GLOBALS['root']}admin/?mod=tags' style='color: #000000;'>tags</a></span>
</div>

<hr />

<form action='$formaction' method='post'>
	Change all tags with tag
	<input type='text' name='fromTag'> to <input type='text' name='toTag' />
	<input type='submit' name='doRenameTags' value='Submit' />
	<div style='margin-top: 1em;'>
		Make this a persistent tag mapping?
		<input type='hidden' name='mod' value='tags' />
		<input type='checkbox' name='persistentTagmap' />
	</div>
</form>

<hr />

<div style='font-size: large; margin-top: 1em; margin-bottom: 1em;'>Persistent tag mappings:</div>

<form action='$formaction' method='post' id='frmTagmaps' onsubmit='return validateModifyTagmapsForm("frmTagmaps");'>
	<table class='adminTable'>
		<tr>
			<th style='width: 2em;'>[x]</th>
			<th>id</th>
			<th>From</th>
			<th>To</th>
		</tr>

HTML;

$tagmaps = $tagservice->getTagmaps();

if ( ! empty($tagmaps) ) {
	foreach ( $tagmaps as $tagmap ) {
		$bgColor = ( $bgColor == "bgDark" ) ? "bgLight" : "bgDark";
		echo <<<HTML
			<tr class='$bgColor'>
				<td style='text-align: center;'><input type='checkbox' name='tagmapList[]' value='{$tagmap['id']}' /></td>
				<td style='text-align: center;'>{$tagmap['id']}</td>
				<td style='text-align: center;'>{$tagmap['fromTag']}</td>
				<td style='text-align: center;'>{$tagmap['toTag']}</td>
			</tr>

HTML;
	}
} else {
	echo "<tr><td colspan='4' style='text-align: center;'>No tag mappings to display</td></tr>\n";
}

echo <<<HTML
	</table>
	<div style='margin-top: 1em;'>
		<span>Perform the following action on the selected tag mappings:</span>
		<select name='tAction'>
			<option value='delete'>Delete</option>
		</select>
		<input type='hidden' name='mod' value='tags' /> 
		<input type='submit' name='doModifyTags' value='Submit' /> 
	</div>
</form>

HTML;

$this->includeTemplate($GLOBALS['bottom_include']);

?>

