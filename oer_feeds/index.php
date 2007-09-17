<?php

/*
 * Copyright (c) 2007, Nathan Kinkade, Creative Commons
 * This software is licened under an MIT-style license.
 * For the full text of the license see the file LICENSE which should
 * have been provided with this software.  For more information:
 * http://www.opensource.org/licenses/mit-license.php
 */

# include the main site config where various global variables
# and libraries are included
include("config.php");

# you must login to view this page
requireLogin();

# the user has selected to modify an existing feed - either modify or delete
if ( isset($_GET['doModifyFeed']) ) {
	switch ( $_GET['feedAction'] ) {
		case "modify":
			if ( modifyFeed() ) {
				header("Location: $config->_rootUri");
				exit;
			}
			break;
		case "delete":
			# do not proceed if the user didn't select a feed to edit or if
			# for some reason it's not an integer.
			if ( empty($_GET['feed_id']) ) {
				$_SESSION['systemMsg'] = "<span style='color: red;'>You must select a feed to delete</span>";   
			} else {
				if ( deleteFeed($_GET['feed_id']) ) {
					header("Location: $config->_rootUri");
					exit;
				}
			}
			break;
		default:
			$_SESSION['systemMsg'] = "<span style='color: red;'>There action you specified isn't recognized</span>";   
	}
}

# the user selected to add a new feed.
if ( isset($_POST['doAddFeed']) ) {
	if ( addFeed() ) {
		header("Location: $config->_rootUri");
		exit;
	}
}

# an array of feed types that feedparser (http://feedparser.org) supports
$smarty->assign( "feedTypes",
	array(
		"atom10" => "Atom 1.0",
		"rss090" => "RSS 0.90",
		"rss091n" => "Netscape RSS 0.91",
		"rss091u" => "Userland RSS 0.91",
		"rss10" => "RSS 1.0",
		"rss092" => "RSS 0.92",
		"rss093" => "RSS 0.93",
		"rss094" => "RSS 0.94",
		"rss20" => "RSS 2.0",
		"rss" => "RSS (unknown version)",
		"atom01" => "Atom 0.1",
		"atom02" => "Atom 0.2",
		"atom03" => "Atom 0.3",
		"atom" => "Atom (unknown version)",
		"cdf" => "CDF",
		"hotrss" => "Host RSS"
	)
);

# get a list of possible users so that we know can present the user with a list
# of the possible users that the feed can be attributed to
$sql = "
	SELECT uId, name
	FROM sc_users
	ORDER BY name
";
$db->Select($sql);
$smarty->assign("users", $db->_rows);

# grab all the feeds in the database
$sql = "
	SELECT * FROM oer_feeds
	ORDER BY url
";
$db->Select($sql);

if ( $db->_rowCount > 0 ) {
	# loop through the results so that we can change the Unix timestamp
	# of the last_import to a human-readable date
	foreach ( $db->_rows AS $key => $feed ) {
		if ( $db->_rows[$key]['last_import'] ) {
			$db->_rows[$key]['last_import'] = date("D, M jS, Y", $feed['last_import']);
		} else {
			$db->_rows[$key]['last_import'] = "Never";
		}
	}
	# assign it to the smarty template
	$smarty->assign("feeds", $db->_rows);
}

# if anything generated a system message, then add it to the template here
if ( ! empty($systemMsg) ) {
	$smarty->assign("systemMsg", $systemMsg);
};

# grab the various parts.  these sections are not printed to the screen
# but rather dumped into smarty variables that will simply be printed
# in the template, so the order doesn't matter here at the moment
include("header.php");
include("footer.php");

$smarty->display("index.tpl");

?>
