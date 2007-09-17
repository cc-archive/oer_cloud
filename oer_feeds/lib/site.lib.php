<?php

/*
 * Copyright (c) 2007, Nathan Kinkade, Creative Commons
 * This software is licened under an MIT-style license.
 * For the full text of the license see the file LICENSE which should
 * have been provided with this software.  For more information:
 * http://www.opensource.org/licenses/mit-license.php
 */

	##------------------------------------------------------------------##

# this is a function for modifying and OER Cloud feed definition.  Nothing is
# passed to this function .. everything we need will be located in the variables
# submitted with the form
function modifyFeed () {

	global $db;

	# do not proceed if the user didn't select a feed to edit or if
	# for some reason it's not an integer.
	if ( empty($_GET['feed_id']) ) {
		$_SESSION['systemMsg'] = "<span style='color: red;'>You must select a feed to edit</span>";   
		return false;
	} else {
		$feed_id = $_GET['feed_id'];
	}

	# at least make sure that the URL isn't empty.  we might be able to establish
	# some better checks in the future.
	if ( "" == trim($_GET["url-$feed_id"]) ) {
		$_SESSION['systemMsg'] = "<span style='color: red;'>You must specify a URL</span>";   
		return false;
	}

	# do not continue if there is no user_id or one that obviously isn't valid
	if ( ! isset($_GET["user_id-$feed_id"]) ) {
		$_SESSION['systemMsg'] = "<span style='color: red;'>You must specify a user ID</span>";   
		return false;
	}

	$sql = sprintf ("
		UPDATE oer_feeds SET
			url = '%s',
			user_id = '%s',
			feed_type = '%s'
		WHERE id = '%s'
		",
		trim($_GET["url-$feed_id"]),
		$_GET["user_id-$feed_id"],
		$_GET["feed_type-$feed_id"],
		$_GET['feed_id']
	);
	$db->Modify($sql);
	if ( empty($db->_error) ) {
		$_SESSION['systemMsg'] = "<span style='color: green;'>The feed was modified</span>";   
	} else {
		$_SESSION['systemMsg'] = "<span style='color: red;'>There was an error</span>";   
		return false;
	}


	return true;

}

	##------------------------------------------------------------------##

# this is a function for deleting an existing feed from the OER Cloud database.
function deleteFeed($feed_id) {

	global $db;

	# do not proceed if the user didn't select a feed to edit or if
	# for some reason it's not an integer.
	if ( empty($feed_id) ) {
		$_SESSION['systemMsg'] = "<span style='color: red;'>You must select a feed to delete</span>";   
		return false;
	}

	$sql = sprintf ("
		DELETE FROM oer_feeds
		WHERE id = '%s'
		",
		$_GET['feed_id']
	);
	$db->Modify($sql);
	if ( $db->_affectedRows == 1 ) {
		$_SESSION['systemMsg'] = "<span style='color: green;'>The feed was deleted</span>";   
	} else {
		$_SESSION['systemMsg'] = "<span style='color: red;'>There was an error</span>";   
		return false;
	}

	return true;

}

	##------------------------------------------------------------------##

# this is a function for adding a new feed to the OER Cloud database.  Nothing is
# passed to this function .. everything we need will be located in the variables
# submitted with the form
function addFeed () {

	global $db;

	# at least make sure that the URL isn't empty.  we might be able to establish
	# some better checks in the future.
	if ( "" == trim($_POST["url"]) ) {
		$_SESSION['systemMsg'] = "<span style='color: red;'>You must specify a URL</span>";   
		return false;
	}

	# do not continue if there is no user_id or one that obviously isn't valid
	if ( ! isset($_POST['user_id']) ) {
		$_SESSION['systemMsg'] = "<span style='color: red;'>The user ID you specified is not valid</span>";   
		return false;
	}

	$sql = sprintf ("
		INSERT INTO oer_feeds(url, user_id, feed_type)
		VALUES ('%s', '%s', '%s')
		",
		trim($_POST['url']),
		$_POST['user_id'],
		$_POST['feed_type']
	);
	$db->Modify($sql);
	if ( $db->_affectedRows == 1 ) {
		$_SESSION['systemMsg'] = "<span style='color: green;'>The feed was added</span>";   
	} else {
		$_SESSION['systemMsg'] = "<span style='color: red;'>There was a database error</span>";   
		return false;
	}

	return true;

}

	##------------------------------------------------------------------##

# does this user have rights to enter?
function validateUser($user, $pass) {
	
	global $db;

	# clear out the session variables
	unset($_SESSION['authorized']);
	unset($_SESSION['ipaddress']);

	# trim the input fields
	$user = trim($user);
	$pass = trim($pass);

	# for the moment we only want to allow the admin user
	if ( $user != "admin" ) {
		return false;
	}

	# encrypt password
	$encPassword = sha1($pass);

	$sql = "
		SELECT * FROM sc_users
		WHERE username = '$user'
			AND password = '$encPassword'
	";
	$db->SelectOne($sql);
	if ( $db->_rowCount == 1 ) {
		$userRecord = $db->_row;

		# if one record was returned then a user matching the credentials they
		# supplied was found in the database.  give them access.
		$_SESSION['authorized'] = "access_granted";
		$_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];
		return true;
	} else {
		# not a valid user (not found in db)
		return false;
	}

	$db->Close();

}

	##------------------------------------------------------------------##

function requireLogin() {

	global $config;

	if ( 
		isset($_SESSION['authorized']) && 
		($_SESSION['authorized'] == "access_granted") &&
		isset($_SESSION['ipaddress']) && 
		($_SESSION['ipaddress'] == $_SERVER['REMOTE_ADDR'])
	) {
		return true;
	} else {
		header("Location: {$config->_rootUri}/login.php");
	}
	
}

	##------------------------------------------------------------------##

?>
