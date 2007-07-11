<?php
$userservice =& ServiceFactory::getServiceInstance('UserService');
$this->includeTemplate($GLOBALS['top_include']);
echo "<p>" . $commitresult . "</p>\n"; 
?>
<form action="<?php echo $formaction; ?>" method="post">
    <h2> -- <?php echo T_("User Options"); ?> -- <h2/>
    <h3> <?php echo T_("Administrators (allowed access to this console)"); ?> </h3> 

    <table border="1" name="administrator_table">
    <tr>
        <th><?php echo T_("Username"); ?> </th>
        <th><?php echo T_("Name"); ?> </th>
        <th><?php echo T_("Email"); ?> </th>
        <th><?php echo T_("IP Address"); ?> </th>
        <th><?php echo T_("Homepage"); ?> </th>
        <th><?php echo T_("uModified"); ?> </th>
        <th><?php echo T_("uDatetime"); ?> </th>
        <th><?php echo T_("Demote to Regular"); ?>  </th>
    </tr>
        
    <?php
    $ordered_users = $userservice->getUsersByCategory();
    $form_loop_count = 0;
    foreach($ordered_users['admins'] as $row)
    {
        echo "<tr><td>" . 
        $row['username'] . '</td><td>' .
        $row['name'] . '</td><td>' .
        $row['email'] . '</td><td>' .
        $row['uIp'] . '</td><td>' .
        $row['homepage'] . '</td><td>' .
        $row['uModified'] . '</td><td>' .
        $row['uDatetime'] . "</td><td>" .
        '<input type="radio" name="' . $row['username'] .
        '" value="revoke_admin_access"' .
        '" /></td></tr>' , "\n";
        $form_loop_count++;
    }
    ?>
    </table>

    <h3> <?php echo T_("Registered Users"); ?> </h3> 
    <table border="1" name="regular_table">
    <tr>
        <th><?php echo T_("Username"); ?> </th>
        <th><?php echo T_("Name"); ?> </th>
        <th><?php echo T_("Email"); ?> </th>
        <th><?php echo T_("IP Address"); ?> </th>
        <th><?php echo T_("Homepage"); ?> </th>
        <th><?php echo T_("uModified"); ?> </th>
        <th><?php echo T_("uDatetime"); ?> </th>
        <th><?php echo T_("Promote to Admin"); ?>  </th>
        <th><?php echo T_("Flag"); ?> </th>
    </tr>

    <?php
    $form_loop_count = 0;
    foreach($ordered_users['registered'] as $row)
    {
        echo "<tr>";
        echo "<td>" . $row['username'] . '</td><td>' .
        $row['name'] . '</td><td>' .
        $row['email'] . '</td><td>' .
        $row['uIp'] . '</td><td>' .
        $row['homepage'] . '</td><td>' .
        $row['uModified'] . '</td><td>' .
        $row['uDatetime'] . "</td><td>" .
        '<input type="radio" name="' . $row['username'] .
        '" value="promote_to_admin' . 
        '" /></td><td>' . 
        '<input type="radio" name="' . $row['username'] .
        '" value="flag' .
        "\"/></td></tr>\n";
        $form_loop_count++;
    }
    ?>
    </table>

    <h3> <?php echo T_("Unregistered Users"); ?> </h3> 
    <table border="1" name="regular_table">
    <tr>
        <th><?php echo T_("Username"); ?> </th>
        <th><?php echo T_("Name"); ?> </th>
        <th><?php echo T_("Email"); ?> </th>
        <th><?php echo T_("IP Address"); ?> </th>
        <th><?php echo T_("Homepage"); ?> </th>
        <th><?php echo T_("uModified"); ?> </th>
        <th><?php echo T_("uDatetime"); ?> </th>
        <th><?php echo T_("Approve Registration"); ?>  </th>
        <th><?php echo T_("Flag"); ?> </th>
    </tr>

    <?php
    $form_loop_count = 0;
    foreach($ordered_users['unregistered'] as $row)
    {
        echo "<tr>";
        echo "<td>" . $row['username'] . '</td><td>' .
        $row['name'] . '</td><td>' .
        $row['email'] . '</td><td>' .
        $row['uIp'] . '</td><td>' .
        $row['homepage'] . '</td><td>' .
        $row['uModified'] . '</td><td>' .
        $row['uDatetime'] . "</td><td>" .
        '<input type="radio" name="' . $row['username'] .
        '" value="approve_registration' . 
        '" /></td><td>' . 
        '<input type="radio" name="' . $row['username'] .
        '" value="flag' .
        "\"/></td></tr>\n";
        $form_loop_count++;
    }
    ?>
    </table>

    <h3><?php echo T_("Flagged Users (bookmarks not visible to other users)"); ?></h3> 
    <p><?php echo T_("Deleting users will remove their tags from the database.  While flagged, however, their tags will still be visible to everyone."); ?></p>
    <table border="1" name="flagged_table">
    <tr>
        <th><?php echo T_("Username"); ?> </th>
        <th><?php echo T_("Name"); ?> </th>
        <th><?php echo T_("Email"); ?> </th>
        <th><?php echo T_("IP Address"); ?> </th>
        <th><?php echo T_("Homepage"); ?> </th>
        <th><?php echo T_("uModified"); ?> </th>
        <th><?php echo T_("uDatetime"); ?> </th>
        <th><?php echo T_("Promote to Regular"); ?>  </th>
        <th><?php echo T_("Delete from Database"); ?>  </th>
    </tr>

    <?php
    $form_loop_count = 0;
    foreach($ordered_users['flagged'] as $row)
    {
        echo "<tr>";
        echo "<td>" . $row['username'] . '</td><td>' .
        $row['name'] . '</td><td>' .
        $row['email'] . '</td><td>' .
        $row['uIp'] . '</td><td>' .
        $row['homepage'] . '</td><td>' .
        $row['uModified'] . '</td><td>' .
        $row['uDatetime'] . "</td><td>" .
        '<input type="radio" name="' . $row['username'] .
        '" value="promote_to_regular' . 
        '" /></td><td>' . 
        '<input type="radio" name="' . $row['username'] .
        '" value="delete_from_db'  .
        "\"/></td></tr>\n";
        $form_loop_count++;
    }
    ?>
    </table>
<br/>
<input type="reset"value="<?php echo T_("Reset"); ?>"/> &nbsp;
<input type="submit" value="<?php echo T_("Apply Changes"); ?>" /> 
<br/>
<h2> -- <?php echo T_("Tag Options"); ?> -- </h2>
<?php echo T_("Change all tags with tag"); ?>&nbsp;
<input type="text" name="merge_tag_from">&nbsp;
<?php echo T_("to"); ?>&nbsp;
<input type="text" name="merge_tag_to">&nbsp;
<?php echo T_("and automatically convert when submitted"); ?>&nbsp;
<input type="checkbox" name="merge_trigger_enable">&nbsp;
<br/><br/>
<input type="reset"value="<?php echo T_("Reset"); ?>"/> &nbsp;
<input type="submit" value="<?php echo T_("Apply Changes"); ?>" /> 
</form>

<?php
$this->includeTemplate($GLOBALS['bottom_include']);
?>
