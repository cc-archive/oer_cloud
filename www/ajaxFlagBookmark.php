<?php

# this script is triggered when a logged-in user wants to flag a bookmark
header('Content-Type: text/xml; charset=UTF-8');
header("Last-Modified: ". gmdate("D, d M Y H:i:s") ." GMT");
header("Cache-Control: no-cache, must-revalidate");

require_once('header.inc.php');

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');

$bFlagCount = $bookmarkservice->flagBookmark($_GET['bId']);
$status = "{$_GET['bId']}:$bFlagCount";

echo "<?xml version='1.0' encoding='utf-8'?>";
?>
<response>
  <method>flagBookmark</method>
  <result><?php echo $status; ?></result>
</response>
