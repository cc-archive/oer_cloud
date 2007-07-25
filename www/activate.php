<?php

require_once('header.inc.php');
$userservice =& ServiceFactory::getServiceInstance('UserService');
$templateservice =& ServiceFactory::getServiceInstance('TemplateService');

if ( isset($_GET['key']) && ! empty($_GET['key']) ) {
	$sql = sprintf ("
		SELECT * FROM %susers
		WHERE  activation_key = '%s'
		",
		$GLOBALS['tableprefix'],
		$_GET['key']
	);
	$qid = $userservice->db->sql_query($sql);
	if ( $user = $userservice->db->sql_fetchrow($qid) ) {
		if ( "0" == $user['uStatus'] ) {
			$sql = sprintf ("
				UPDATE %susers SET uStatus = '1'
				WHERE uId = '%s'
				",
				$GLOBALS['tableprefix'],
				$user['uId']
			);
			if ( $qid = $userservice->db->sql_query($sql) ) {
				$tplVars['msg'] = "Thank you.  Your account has been activated.  You may now <a href='{$GLOBALS['root']}login/'>login</a>.";
			} else {
				$tplVars['error'] = "There was an error activating your account.  Please contact the site administrator.";
			}
		} else {
			$tplVars['error'] = "This account was already activated.  You may <a href='{$GLOBALS['root']}login/'>login</a>.";
		}
	} else {
		$tplVars['error'] = "The activation key specified was not found in the database.";
	}
} else {
	$tplVars['error'] = "You must provide an activation key.";
}

$tplVars['loadjs']      = true;
$tplVars['subtitle']    = T_('Activation');
$templateservice->loadTemplate('activate.tpl', $tplVars);

?>
