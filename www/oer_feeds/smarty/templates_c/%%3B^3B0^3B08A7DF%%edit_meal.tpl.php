<?php /* Smarty version 2.6.11, created on 2007-04-25 18:53:37
         compiled from edit_meal.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'edit_meal.tpl', 13, false),)), $this); ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Edit Meals</h3>
			<div style='float: left; padding-right: 2ex; width: 25%;'>
				<div>
					<span style='text-decoration: underline;'><strong>Saved meals</strong></span>
				</div>
<?php if ($this->_tpl_vars['savedMeals']): ?>
	<?php $_from = $this->_tpl_vars['savedMeals']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['savedMeal']):
?>
				<div name='savedMeals' id='savedMeal-<?php echo $this->_tpl_vars['savedMeal']['id']; ?>
'><a href='<?php echo $_SERVER['REQUEST_URI']; ?>
' title='<?php echo $this->_tpl_vars['savedMeal']['description']; ?>
' onclick='loadMealToEdit(<?php echo $this->_tpl_vars['savedMeal']['id']; ?>
); return false;'><?php echo ((is_array($_tmp=$this->_tpl_vars['savedMeal']['description'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 25, " ...") : smarty_modifier_truncate($_tmp, 25, " ...")); ?>
</a></div>
	<?php endforeach; endif; unset($_from);  else: ?>
				No saved meals.
<?php endif; ?>
			</div>
			<form action='edit_meal.php' method='post' name='formEditMeal' id='formEditMeal' style='width: 75%; overflow: auto;' onsubmit='return validateEditMeal("formEditMeal");'>
				<div id='editMeal' style='float: left; padding-left: 2ex; border-left: 1px solid black;'>
<?php if ($this->_tpl_vars['editMeal']): ?>
					<script type='text/javascript'>xajax_loadMealToEdit("<?php echo $this->_tpl_vars['editMeal']; ?>
");</script>
<?php else: ?>
					&lt;= Select a meal to edit.
<?php endif; ?>
				</div>
			</form>
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
