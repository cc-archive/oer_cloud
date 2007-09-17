<?php /* Smarty version 2.6.11, created on 2007-04-25 18:05:17
         compiled from edit_food.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'edit_food.tpl', 11, false),)), $this); ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Edit Foods</h3>
			<div style='float: left; padding-right: 2ex; width: 20%;'>
				<div><span style='text-decoration: underline;'><strong>Saved foods</strong></span></div>
<?php if ($this->_tpl_vars['savedFoods']): ?>
	<?php $_from = $this->_tpl_vars['savedFoods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['savedFood']):
?>
				<div name='savedFoods' id='savedFood-<?php echo $this->_tpl_vars['savedFood']['id']; ?>
'><a href='<?php echo $_SERVER['REQUEST_URI']; ?>
' title='<?php echo $this->_tpl_vars['savedFood']['description']; ?>
' onclick='loadFoodToEdit("<?php echo $this->_tpl_vars['savedFood']['id']; ?>
"); return false;'><?php echo ((is_array($_tmp=$this->_tpl_vars['savedFood']['description'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 25, " ...") : smarty_modifier_truncate($_tmp, 25, " ...")); ?>
</a></div>
	<?php endforeach; endif; unset($_from);  else: ?>
				No saved foods.
<?php endif; ?>
			</div>
			<div id='editFood' style='float: left; padding-left: 2ex; border-left: 1px solid black; width: 75%;'>
<?php if ($this->_tpl_vars['editFood']): ?>
				<script type='text/javascript'>xajax_loadFoodToEdit("<?php echo $this->_tpl_vars['editFood']; ?>
");</script>
<?php else: ?>
				&lt;= Select a food to edit.
<?php endif; ?>
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
