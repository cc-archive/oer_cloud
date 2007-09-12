<?php /* Smarty version 2.6.11, created on 2007-04-30 14:32:38
         compiled from edit_account.tpl */ ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData' style='margin-left: 5ex; margin-right: 5ex;'>

			<h3 style='text-align: center;'>Edit Profile</h3>
			<div class='standardMargins' style='text-align: justify;'>
				Use this form to edit your account profile, or delete the account entirely.
				<strong>NOTE</strong>: if you choose to delete the account, then be aware that
				any and all data (foods, meals, diaries, etc.) associated with the account will
				also be permanently and irrevocably deleted.  This isn't a problem, but something
				that you should be aware of.  For this reason, you will be prompted twice as to
				whether you really want to delete the account.
			</div>
			<div>
				<form action='<?php echo $_SERVER['PHP_SELF']; ?>
' method='post' id='formEditUser' onsubmit='validateEditUser("formEditUser"); return false;'>
					<div class='standardMargins'>
						<input type='text' name='username' size='25' value='<?php echo $_SESSION['user']['username']; ?>
' /> <strong>Login name</strong>
						(min. 5 chars.)
					</div>
					<div class='standardMargins'>
						<input type='password' name='password' size='25' /> <strong>New password</strong>
						(min. 5 chars.)
					</div>
					<div class='standardMargins'>
						<input type='password' name='password2' size='25' /> <strong>Confirm password</strong>
						(min. 5 chars.)
					</div>
					<div class='standardMargins'>
						<input type='text' name='age' size='5' value='<?php echo $_SESSION['user']['age']; ?>
' /> <strong>Age</strong>
					</div>
					<div class='standardMargins'>
						<select name='gender'>
<?php $_from = $this->_tpl_vars['genders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['gender']):
?>
	<?php if ($this->_tpl_vars['gender'] == $_SESSION['user']['gender']): ?>
							<option value='<?php echo $this->_tpl_vars['gender']; ?>
' selected='selected'><?php echo $this->_tpl_vars['gender']; ?>
</option>
	<?php else: ?>
							<option value='<?php echo $this->_tpl_vars['gender']; ?>
'><?php echo $this->_tpl_vars['gender']; ?>
</option>
	<?php endif;  endforeach; endif; unset($_from); ?>
						</select> <strong>Gender</strong>
					</div>
					<div class='standardMargins'>
						<input type='hidden' name='action' value='' />
						<input type='submit' name='doEdit' value='Modify' onclick='getElement("formEditUser").action.value = "editUser";' />
						<input type='submit' name='doDelete' value='Delete' onclick='return verifyDeleteUser();' />
					</div>
				</form>
			</div>
	
		</div>
	</div>

	<div id='leftColumn'>
		<div id='leftData'>
			<?php echo $this->_tpl_vars['sidebar_left']; ?>

		</div>
	</div>

	<div id='rightColumn'>
		<div id='rightData'>
			<?php echo $this->_tpl_vars['sidebar_right']; ?>

		</div>
	</div>

</div>
<?php echo $this->_tpl_vars['footer']; ?>
