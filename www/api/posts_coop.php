<?php

# Generate a Google Co-Op Annotation file for all posts.

require_once('../header.inc.php');

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');

# set a proper content-type header
header('Content-Type: text/xml');

echo <<<XML
<?xml version='1.0' ?>
	<GoogleCustomizations>
		<Annotations>

XML;

# Get the posts relevant to the passed-in variables.
$page = $_GET['p'];
$bookmarks = $bookmarkservice->getBookmarks($start=$page * 5000, $perpage=5000);
//getAllBookmarks();

foreach ( $bookmarks['bookmarks'] as $bookmark ) {

	# Get the bookmark URL and make it a wildcard
	$bookmark_url = filter($bookmark['bAddress'], 'xml');
	$url_info = parse_url($bookmark_url);
	if ( empty($url_info['query']) &&
		(substr($url_info['path'], -1) == "/" || empty($url_info['path']))
	) { 
		$bookmark_url .= ( empty($url_info['path']) ) ? "/*" : "*";
	}

	echo "		<Annotation about='$bookmark_url' score='1'>\n";
	echo "		<Label name='_cse_cclearn_oe_search' />\n";

    # Output the tags
	if ( count($bookmark['tags']) > 0 ) {
		foreach ( $bookmark['tags'] as $bTag ) {
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
