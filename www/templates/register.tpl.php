<?php
require_once('recaptchalib.php');
$this->includeTemplate($GLOBALS['top_include']);
?>

<script type="text/javascript">
window.onload = function() {
    document.getElementById("username").focus();
}
</script>

<p><?php echo sprintf(T_('Sign up here to create a free %s account. All the information requested below is required'), $GLOBALS['sitename']); ?>.</p>

<form action="<?php echo $formaction; ?>" method="post">
<table>
<tr>
    <th align="left"><label for="username"><?php echo T_('Username'); ?></label></th>
    <td><input type="text" id="username" name="username" size="20" class="required" onkeyup="isAvailable(this, '')" /></td>
    <td id="availability"></td>
</tr>
<tr>
    <th align="left"><label for="name"><?php echo T_('Full name / Organization'); ?></label></th>
    <td><input type="text" id="name" name="name" size="50" class="required" /></td>
    <td></td>
</tr>
<tr>
    <th align="left"><label for="homepage"><?php echo T_('Homepage'); ?></label></th>
    <td><input type="text" id="homepage" name="homepage" size="50" /></td>
    <td></td>
</tr>
<tr>
    <th align="left"><label for="password"><?php echo T_('Password'); ?></label></th>
    <td><input type="password" id="password" name="password" size="20" class="required" /></td>
    <td></td>
</tr>
<tr>
    <th align="left"><label for="email"><?php echo T_('E-mail'); ?></label></th>
    <td><input type="text" id="email" name="email" size="40" class="required" /></td>
    <td></td>
</tr>

  <?php if ($GLOBALS['use_recaptcha']) {  ?>

<tr>
    <th align="left"><?php echo T_('Read books'); ?></th>
    <td>
    <?php echo recaptcha_get_html($GLOBALS['recaptcha_public_key']); ?>
</td>
    <td></td>
</tr>
					
  <?php } ?>

<tr>
    <td></td>
    <td><input type="submit" name="submitted" value="<?php echo T_('Register'); ?>" /></td>
    <td></td>
</tr>
</table>
</form>

<?php
$this->includeTemplate($GLOBALS['bottom_include']);
?>
