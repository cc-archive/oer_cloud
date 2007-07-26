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

<form action='$formaction' method='post'>
	Change all tags with tag <input type='text' name='merge_tag_from'> to <input type='text' name='merge_tag_to'>
	<input type='submit' name='doModifyTags' value='Apply Changes' /><br /><br />
	Automatically make this conversion for new tags? <input type='checkbox' name='merge_trigger_enable'>
</form>

HTML;

$this->includeTemplate($GLOBALS['bottom_include']);

?>

