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
  <CustomSearchEngine keywords="oai" language="en" top_refinements="5">
    <Title>Open Education Search</Title>
HEADER;

/*
 * For the moment we are goingt to comment out the Facet items.
 * Once issues are resolved with Google, we may put these back
 * in.
 */
echo <<<FACETS
    <Context>
    <BackgroundLabels>
	<Label name='_cse_cclearn_oe_search' mode="FILTER" />
    </BackgroundLabels>
FACETS;


// spit out the usernames as facets
foreach ($users as $user) {

	if ($user['username'] == 'admin') continue;

	$lc_username = strtolower($user['username']);

	echo <<<FACET
    <Facet>
      <FacetItem title='{$user['username']}'>
         <Label name='{$user['username']}' mode='FILTER'>
         <Rewrite>{$lc_username}</Rewrite>
	 </Label>
      </FacetItem>
    </Facet>
FACET;
}


// spit out the facets to make labels show up
foreach ($tags as $tag) {
	# don't output system generated tags
	if ( substr($tag['tag'], 0, 7) != "system:" ) {
	   # convert special chars to character entities
	   $tag['tag'] = filter($tag['tag'], "xml");
	   $lc_tag = strtolower($tag['tag']);

	   echo <<<FACET
    <Facet>
		<FacetItem title='{$tag['tag']}'>
			   <Label name='{$tag['tag']}' mode='FILTER'>
			   <Rewrite>{$lc_tag}</Rewrite>
			   </Label>
		</FacetItem>
    </Facet>
FACET;
	}
}
echo " </Context>";

echo <<<CLOSE_HEADER
		<LookAndFeel nonprofit="true" />
	</CustomSearchEngine>

CLOSE_HEADER;

// generate inclusion URLs for the bookmarks
$bookmarks =& $bookmarkservice->getAllBookmarks();
$page_count = (count($bookmarks) / 2500) + 1;

echo "	<!-- include the OER Cloud annotations -->\n";

for ($i = 0; $i < $page_count; $i++) {
	echo "	<Include type=\"Annotations\" href=\"http://oercloud.creativecommons.org/api/posts/coop?p=$i\" />\n";
}

echo "</GoogleCustomizations>\n"

?>
