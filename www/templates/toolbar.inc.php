<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
if ($userservice->isLoggedOn()) {
    $cUser = $userservice->getCurrentUser();
    $cUsername = $cUser[$userservice->getFieldName('username')];
    $isAdmin = $userservice->isAdminByUsername($cUsername);
?>

    <ul id="navigation">
        <li><a href="<?php echo createURL('bookmarks', $cUsername); ?>"><?php echo T_('My Bookmarks'); ?></a></li>
        <li><a href="<?php echo createURL('watchlist', $cUsername); ?>"><?php echo T_('Watchlist'); ?></a></li>
        <li><a href="<?php echo createURL('bookmarks', $cUsername . '?action=add'); ?>"><?php echo T_('Add a Bookmark'); ?></a></li>
<?php
	
if ( $isAdmin ) {
	echo "	<li>[<a href='{$GLOBALS['root']}admin/'>" . T_('Admin') . "</a>]</li>\n";
}

?>
        <li class="access">
          <ul>
            <li><strong><?= $cUsername ?>:</strong>&nbsp;</li>
            <li><a href="<?php echo $userservice->getProfileUrl($userid, $user); ?>"><?php echo T_('Profile'); ?></a></li>
            <li><a href="<?php echo $GLOBALS['root']; ?>?action=logout"><?php echo T_('Log Out'); ?></a></li>
          </ul>
        </li>
    </ul>

<?php
} else {
?>

    <ul id="navigation">
        <li><a href="<?php echo createURL('about'); ?>"><?php echo T_('About'); ?></a></li>
        <li class="access"><a href="<?php echo createURL('login'); ?>"><?php echo T_('Log In'); ?></a></li>
        <li class="access"><a href="<?php echo createURL('register'); ?>"><?php echo T_('Register'); ?></a></li>
    </ul>

<?php
}
?>
