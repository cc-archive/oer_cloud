<?php

/*
 * Copyright (c) 2007, Nathan Kinkade, Creative Commons
 * This software is licened under an MIT-style license.
 * For the full text of the license see the file LICENSE which should
 * have been provided with this software.  For more information:
 * http://www.opensource.org/licenses/mit-license.php
 */

# this file allows us to change any info inside the
# <head> tags for any given file, while still using
# a common header file
include("meta.php");
$smarty->assign("myHeaders", $myHeaders);

# assign any system message that may exist to the template and then clear the variable
if ( ! empty($_SESSION['systemMsg']) ) {
	$smarty->assign("systemMsg", $_SESSION['systemMsg']);
	unset($_SESSION['systemMsg']);
}

# grab the header
$smarty->assign("header", $smarty->fetch("header.tpl"));

?>
