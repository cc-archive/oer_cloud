<?php
// Implements the del.icio.us API request for all a user's posts, optionally filtered by tag.

// del.icio.us behavior:
// - doesn't include the filtered tag as an attribute on the root element (we do)

// Force HTTP authentication first!
// require_once('httpauth.inc.php');
require_once('../header.inc.php');

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

// Force the user to log out so we can take advantage of scuttle default func.
$userservice->logout();

// Check to see if a tag was specified.
if (isset($_REQUEST['tag']) && (trim($_REQUEST['tag']) != ''))
    $tag = trim($_REQUEST['tag']);
else
    $tag = NULL;

// Get the posts relevant to the passed-in variables.
$bookmarks =& $bookmarkservice->getBookmarks(0, NULL, NULL, $tag);

// $currentuser = $userservice->getCurrentUser();
// $currentusername = $currentuser[$userservice->getFieldName('username')];

// Set up the XML file and output all the posts.
header('Content-Type: text/xml');
echo '<?xml version="1.0" ?'.">\r\n";
echo '<GoogleCustomizations>\r\n';
echo '<Annotations>\r\n';

// echo '<posts update="'. gmdate('Y-m-d\TH:i:s\Z') .'" user="'. htmlspecialchars($currentusername) .'"'. (is_null($tag) ? '' : ' tag="'. htmlspecialchars($tag) .'"') .">\r\n";

foreach($bookmarks['bookmarks'] as $row) {

    // XXX Get the bookmark URL and make it a wildcard`
    $bookmark_url = filter($row['bAddress'], 'xml');

    echo '<Annotation about="' . $bookmark_url . '" score="1" >\r\n';

    // XXX Add a "lable" to identify which coop this goes with
    echo '<Label name="_cse_XXX" />\r\n';

    /// Output the tags
    if (count($row['tags']) > 0) {
        foreach($row['tags'] as $tag)
            echo '<Label name="' . convertTag($tag) . '" />\r\n';
    }

    echo "</Annotation>\r\n";

    // echo "\t<post href=\"". filter($row['bAddress'], 'xml') .'" description="'. filter($row['bTitle'], 'xml') .'" '. $description .'hash="'. md5($row['bAddress']) .'" tag="'. filter($taglist, 'xml') .'" time="'. gmdate('Y-m-d\TH:i:s\Z', strtotime($row['bDatetime'])) ."\" />\r\n";
}

echo '</Annotations>\r\n</GoogleCustomizations>\r\n';
?>
