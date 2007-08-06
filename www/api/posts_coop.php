<?php

# Generate a Google Co-Op Annotation file for all posts.

require_once('../header.inc.php');

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');

# Check to see if a tag was specified.
if ( isset($_REQUEST['cse']) && (trim($_REQUEST['cse']) != "") ) {
	$cse = trim($_REQUEST['cse']);
} else {
	$cse = "_cse_we9jedjkeci";
}

# set a proper content-type header
header('Content-Type: text/xml');

echo <<<XML
<?xml version='1.0' ?>
	<GoogleCustomizations>
		<Annotations>

XML;

# Get the posts relevant to the passed-in variables.
$bookmarks = $bookmarkservice->getAllBookmarks();

foreach ( $bookmarks as $bookmark ) {

	# Get the bookmark URL and make it a wildcard
	$bookmark_url = filter($bookmark['bAddress'], 'xml');
	$url_info = parse_url($bookmark_url);
	if ( empty($url_info['query']) &&
		(substr($url_info['path'], -1) == "/" || empty($url_info['path']))
	) { 
		$bookmark_url .= ( empty($url_info['path']) ) ? "/*" : "*";
	}

	echo "		<Annotation about='$bookmark_url' score='1'>\n";

	# Add a "lable" to identify which coop this goes with
	echo "			<Label name='$cse' />\n";

    # Output the tags
	$bTags = explode(",", $bookmark['bTags']);
	if ( count($bTags) > 0 ) {
		foreach ( $bTags as $bTag ) {
			# ignore tags used by the system
			if ( substr($bTag, 0, 7) != "system:" ) {
				# if there are any single or double quotes in the tag, turn them into their html entities
				$bTag = htmlspecialchars($bTag, ENT_QUOTES);
				$bTag = convertTag($bTag);
				echo "			<Label name='$bTag' />\n";
			}
		}
	}

	echo "		</Annotation>\n";

}

echo "	</Annotations>\n";
echo "</GoogleCustomizations>\n";

?>
