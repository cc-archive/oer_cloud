<?php
// Generate a Google Co-Op Annotation file for all posts.

require_once('../header.inc.php');

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

// Force the user to log out so we can take advantage of scuttle default func.
$userservice->logout();

// Check to see if a tag was specified.
if (isset($_REQUEST['cse']) && (trim($_REQUEST['cse']) != ''))
    $cse = trim($_REQUEST['cse']);
else
    $cse = "_cse_we9jedjkeci";

// Get the posts relevant to the passed-in variables.
$bookmarks =& $bookmarkservice->getBookmarks(0, NULL, NULL, $tag);

// Set up the XML file and output all the posts.
header('Content-Type: text/xml');
echo '<?xml version="1.0" ?'.">";
echo '<GoogleCustomizations>';
echo '<Annotations>';

foreach($bookmarks['bookmarks'] as $row) {

    // Get the bookmark URL and make it a wildcard`
    $bookmark_url = filter($row['bAddress'], 'xml');
    $url_info = parse_url($bookmark_url);
    if ( ((substr($url_info['path'], -1) == '/') && ($url_info['query'] == ''))
        || ($url_info['path'] == '' && $url_info['query'] == '') ) {

	if (substr($bookmark_url, -1) != '/') 
	    $bookmark_url = $bookmark_url . "/";

        $bookmark_url = $bookmark_url . "*";
    }

    echo '<Annotation about="' . $bookmark_url . '" score="1" >';

    // Add a "lable" to identify which coop this goes with
    echo '<Label name="' . $cse . '" />';

    /// Output the tags
    if (count($row['tags']) > 0) {
        foreach($row['tags'] as $tag)
            echo '<Label name="' . convertTag($tag) . '" />';
    }

    echo "</Annotation>";

}

echo '</Annotations>';
echo '</GoogleCustomizations>';
?>
