<?php

/*
 * Copyright (c) 2007, Nathan Kinkade, Creative Commons
 * This software is licened under an MIT-style license.
 * For the full text of the license see the file LICENSE which should
 * have been provided with this software.  For more information:
 * http://www.opensource.org/licenses/mit-license.php
 */

# this script perhaps doesn't have the most apt name, but it made sense
# to me at the time I first named it.
# the purpose of this file is to allow adding custom <head>
# items based on the current script/page.  there is a common set of
# headers that will be the same for all pages and these are defined
# in the variable $commonHeaders.  for example, some pages will need
# some special javascript, but we may not want to add the overhead
# of loading the javascript into pages that don't require it.  this
# can be handled here.  we may also be able to add page-specific
# <title>'s.  at the stage that this script is included we should
# have access to all the $config variables and the database, as well
# as any user submitted data: $_POST, $_GET, etc.

# headers common to every page
$commonHeaders = <<<HEADERS
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	<meta name='keywords' content='oer, open, education, open education resources, creative commons' />
	<meta name='description' content='OER Feeds' />
	<link rel='stylesheet' media='all' type='text/css' href='{$config->_cssUri}/site.css' />
	<script type='text/javascript' src='{$config->_jsUri}/site.js'></script>
	<script type='text/javascript' src='{$config->_jsUri}/standard.js'></script>

HEADERS;

switch ( $config->_thisScript ) {

	case "index.php":
		$myHeaders = <<<HEADERS

	<title>OER Search - Home</title>
$commonHeaders

HEADERS;
		break;

	default:
		$myHeaders = <<<HEADERS

	<title>OER Search</title>
$commonHeaders

HEADERS;

}

?>
