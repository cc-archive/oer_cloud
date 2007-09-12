<?php

/*
 * Copyright (c) 2007, Nathan Kinkade, Creative Commons
 * This software is licened under an MIT-style license.
 * For the full text of the license see the file LICENSE which should
 * have been provided with this software.  For more information:
 * http://www.opensource.org/licenses/mit-license.php
 */

# establish an error reporting level suitable for the site
#error_reporting (0); # don't show any errors
error_reporting(E_ALL); # show all errors
#error_reporting(E_ALL ^ E_NOTICE); # show all errors except notices

# set various site-wide variables
#
# site constants that don't need to be interpolated in strings and/or
# are more sensitive
define("DBHOST", "localhost");          # database host
define("DBNAME", "oer");       # database name
define("DBUSER", "root");            # database user
define("DBPASS", "");       # database password
define("ADODBDIR", "/usr/share/php/adodb"); # adodb db abastractions libs - adodb.sourceforge.net
define("SMARTYDIR", "/usr/share/php/smarty/libs"); # Smarty templates dir - smarty.php.net

# if true then any db error will cause the error to be printed
# # and the script will be halted.  this should probably only be
# # set to true while in development
define("DBDEBUG", "true");

# generic empty class for some config variables
class siteConfig {};
$config = new siteConfig;

# variables that need to be interpolated inside strings and HEREDOCs
# will go in the siteConfig object
$config->_rootDir			= "/var/www/cc/oercloud/oer_feeds"; # root directory
$config->_rootUri			= "http://localhost/cc/oercloud/oer_feeds"; # root uri 
$config->_imgUri			= "{$config->_rootUri}/images"; # where images live
$config->_cssUri			= "{$config->_rootUri}/css"; # where css files live
$config->_jsUri				= "{$config->_rootUri}/js"; # where javascript files live
$config->_smarty			= "{$config->_rootDir}/smarty"; # where smarty files live
$config->_recordsPerPage	= "30"; # number of results per page on food_search.php
$config->_thisScript		= basename($_SERVER['PHP_SELF']);

# in some cases we might want to send the user back to the page
# they came from, but HTTP_REFERER won't always be set for
# various reasons, so if it isn't then just set this to the
# root URI.
if ( isset($_SERVER['HTTP_REFERER']) ) {
	$config->_previousUri = $_SERVER['HTTP_REFERER'];
} else {
	$config->_previousUri = "{$config->_rootUri}/";
}

# include the necessary libraries and classes
require("lib/site.lib.php"); # functions specific to this site
require("lib/standard.lib.php"); # standard functions useful for any site
require("lib/database.class.php"); # database class
require(SMARTYDIR . "/Smarty.class.php"); # Smarty templates

# start a session.  we may not need it but start it anyway
session_start();

# instantiate the database object
$db = new Database();

# instantiate and configure Smarty
$smarty = new Smarty();
$smarty->template_dir	= "{$config->_rootDir}/templates";
$smarty->compile_dir	= "{$config->_smarty}/templates_c";
$smarty->cache_dir		= "{$config->_smarty}/cache";
$smarty->config_dir		= "{$config->_smarty}/configs";
#$smarty->caching		= true; # turn on smarty template caching

# go ahead and register our $config object with smarty
$smarty->assign("config", $config);

?>
