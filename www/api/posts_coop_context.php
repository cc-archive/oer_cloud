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
$tags =& $tagservice->getTags();

// Set up the XML file and output all the posts.
header('Content-Type: text/xml');
echo <<<HEADER
<?xml version="1.0" encoding="UTF-8" ?>
<GoogleCustomizations>
  <CustomSearchEngine version="1.0" volunteers="true" keywords="oai" 
  Title="OER Search" Description="Test Engine for OER import." language="en">
    <Context refinementsTitle="Refine results for $q:">
      <BackgroundLabels>
        <Label name="_cse_we9jedjkeci" mode="FILTER" />
        <Label name="_cse_exclude_we9jedjkeci" mode="ELIMINATE" />
      </BackgroundLabels>
HEADER;

foreach ($tags['tags'] as $tag) {

   echo $tag . "\n";

}


echo <<<FOOTER
    </Context>
    <LookAndFeel nonprofit="true" />

    <!-- include the OER Cloud annotations -->
    <Include type="Annotations" 
       href="http://oercloud.creativecommons.org/api/posts/google_coop" />

  </CustomSearchEngine>
</GoogleCustomizations>
FOOTER;

?>