<?php

// Generate a Google Co-Op context file for the tags

require_once('../header.inc.php');

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$tagservice =& ServiceFactory::getServiceInstance('TagService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

// Force the user to log out so we can take advantage of scuttle default func.
$userservice->logout();

// Check to see if a tag was specified.
if (isset($_REQUEST['cse']) && (trim($_REQUEST['cse']) != ''))
    $cse = trim($_REQUEST['cse']);
else
    $cse = "_cse_we9jedjkeci";

// Get the list of tags
$tags = $tagservice->getAllTags();
$users = $userservice->getAllUsers();

// Set up the XML file and output all the posts.
header('Content-Type: text/xml');
echo <<<HEADER
<?xml version="1.0" encoding="UTF-8" ?>
<GoogleCustomizations version="1.0">
  <CustomSearchEngine keywords="oai" Title="OE Search" language="en">
    <Context refinementsTitle="Refine results for \$q:">
    <BackgroundLabels>
<Label name='_cse_cclearn_oe_search' mode="FILTER" />
    </BackgroundLabels>
	   <Facet>
HEADER;

// spit out the usernames as facets
foreach ($users as $user) {

	if ($user['username'] == 'admin') continue;

	echo <<<FACET
	<FacetItem title='{$user['username']}'>
		   <Label name='{$user['username']}' mode='FILTER' />
	</FacetItem>
FACET;
}


// spit out the facets to make labels show up
foreach ($tags as $tag) {
	# don't output system generated tags
	if ( substr($tag['tag'], 0, 7) != "system:" ) {
	   # convert special chars to character entities
	   $tag['tag'] = filter($tag['tag'], "xml");
	   echo <<<FACET
		<FacetItem title='{$tag['tag']}'>
			   <Label name='{$tag['tag']}' mode='FILTER' />
		</FacetItem>
FACET;
	}
}

echo <<<CLOSE_HEADER
	   </Facet>

    </Context>
    <LookAndFeel nonprofit="true" />

  </CustomSearchEngine>
CLOSE_HEADER;

// generate inclusion URLs for the bookmarks
$bookmarks =& $bookmarkservice->getAllBookmarks();
$page_count = (count($bookmarks) / 2500) + 1;

echo "<!-- include the OER Cloud annotations -->\n";

for ($i = 0; $i < $page_count; $i++) {

    echo " <Include type=\"Annotations\" href=\"http://oercloud.creativecommons.org/api/posts/coop?p=$i\" />";

}

echo <<<FOOTER
</GoogleCustomizations>
FOOTER;

?>
