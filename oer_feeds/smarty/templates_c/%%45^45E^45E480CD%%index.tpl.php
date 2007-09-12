<?php /* Smarty version 2.6.14, created on 2007-09-12 14:47:23
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'index.tpl', 15, false),)), $this); ?>
<?php echo $this->_tpl_vars['header']; ?>


<div>
	<form action='' method='get' id='feedListForm' onsubmit='return confirmDelete("feedListForm");'>

		<div>
			<div class='fieldHeader' style='width: 2%; text-align: center;'>X</div>
			<div class='fieldHeader' style='width: 40%;'>URL</div>
			<div class='fieldHeader' style='width: 20%;'>User</div>
			<div class='fieldHeader' style='width: 15%;'>Feed type</div>
			<div class='fieldHeader' style='width: 15%;'>Last import</div>
		</div>
<?php if ($this->_tpl_vars['feeds']): ?>
	<?php $_from = $this->_tpl_vars['feeds']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feed']):
?>
		<div style='clear: left;' class='<?php echo smarty_function_cycle(array('values' => "bgDark,bgLight"), $this);?>
'>
			<div class='fieldData' style='border-color: #ffffff; width: 2%;'><input type='radio' name='feed_id' value='<?php echo $this->_tpl_vars['feed']['id']; ?>
' /></div>
			<div class='fieldData' style='width: 40%;'><input type='text' style='width: 95%;' name='url-<?php echo $this->_tpl_vars['feed']['id']; ?>
' value='<?php echo $this->_tpl_vars['feed']['url']; ?>
' /></div>
			<div class='fieldData' style='width: 20%;'>
				<select name='user_id-<?php echo $this->_tpl_vars['feed']['id']; ?>
'>
		<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
			<?php if ($this->_tpl_vars['user']['uId'] == $this->_tpl_vars['feed']['user_id']): ?>
					<option value='<?php echo $this->_tpl_vars['user']['uId']; ?>
' selected='selected'><?php echo $this->_tpl_vars['user']['name']; ?>
</option>
			<?php else: ?>
					<option value='<?php echo $this->_tpl_vars['user']['uId']; ?>
'><?php echo $this->_tpl_vars['user']['name']; ?>
</option>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
				</select>
			</div>
			<div class='fieldData' style='width: 15%;'>
				<select name='feed_type-<?php echo $this->_tpl_vars['feed']['id']; ?>
'>
		<?php $_from = $this->_tpl_vars['feedTypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feedKey'] => $this->_tpl_vars['feedType']):
?>
			<?php if ($this->_tpl_vars['feedKey'] == $this->_tpl_vars['feed']['feed_type']): ?>
					<option value='<?php echo $this->_tpl_vars['feedKey']; ?>
' selected='selected'><?php echo $this->_tpl_vars['feedType']; ?>
</option>
			<?php else: ?>
					<option value='<?php echo $this->_tpl_vars['feedKey']; ?>
'><?php echo $this->_tpl_vars['feedType']; ?>
</option>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
				</select>
			</div>
			</div>
			<div class='fieldData' style='width: 15%; font-size: x-small;'><?php echo $this->_tpl_vars['feed']['last_import']; ?>
</div>
		</div>
	<?php endforeach; endif; unset($_from);  else: ?>
		<div style='clear: left;'>
			<strong>There are no feeds to list</strong>
		</div>
<?php endif; ?>
		<div style='clear: left; margin-top: 3em;'>
			<select name='feedAction'>
				<option value='modify'>Modify</option>
				<option value='delete'>Delete</option>
			</select>
			the selected feed
			<input type='submit' name='doModifyFeed' value='Modify Feed' />
		</div>
	</form>
</div>
<div style='clear: left; margin-top: 1em;'>
	<form action='' method='post' id='addFeedForm' />
		<div>
			<div><strong>Add a new feed</strong></div>
			<div class='fieldData' style='width: 2%;'></div>
			<div class='fieldData' style='width: 40%'><input type='text' style='width: 95%;' name='url' /></div>
			<div class='fieldData' style='width: 20%;'>
				<select name='user_id'>
		<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
					<option value='<?php echo $this->_tpl_vars['user']['uId']; ?>
'><?php echo $this->_tpl_vars['user']['name']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
				</select>
			</div>
			<div class='fieldData' style='width: 15%;'>
				<select name='feed_type'>
		<?php $_from = $this->_tpl_vars['feedTypes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feedKey'] => $this->_tpl_vars['feedType']):
?>
					<option value='<?php echo $this->_tpl_vars['feedKey']; ?>
'><?php echo $this->_tpl_vars['feedType']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
				</select>
			</div>
		</div>
		<div>
			<input type='submit' name='doAddFeed' value='Add Feed' />
		</div>
	</form>
</div>

<?php echo $this->_tpl_vars['footer']; ?>
