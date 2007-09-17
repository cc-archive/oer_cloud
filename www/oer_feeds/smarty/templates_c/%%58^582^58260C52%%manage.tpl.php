<?php /* Smarty version 2.6.11, created on 2007-04-27 09:55:22
         compiled from manage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'manage.tpl', 16, false),array('modifier', 'truncate', 'manage.tpl', 16, false),)), $this); ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Manage Account</h3>
			<div><strong>Quick edit favorites</strong>:</div>
			<div style='border: 1px solid black;'>
<?php if ($this->_tpl_vars['favFoods']): ?>
			<div style='padding: 1ex;'>
				<form action='edit_food.php' method='post' name='formQuickEditFood' id='formQuickEditFood' onsubmit='return validateEditFood("formQuickEditFood");'>
					<div style='float: left; margin-right: .5ex; overflow: auto;'>
						<strong>Foods:</strong><br />
						<select name='food' id='foodId'>
	<?php $_from = $this->_tpl_vars['favFoods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['favFood']):
?>
							<option value='<?php echo $this->_tpl_vars['favFood']['id']; ?>
'><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['favFood']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('truncate', true, $_tmp, 50, " ...") : smarty_modifier_truncate($_tmp, 50, " ...")); ?>
</option>
	<?php endforeach; endif; unset($_from); ?>
						</select>
					</div>
					<div style='float: left; margin-right: .5ex;'>
						<strong>Action:</strong><br />
						<select name='action' id='foodAction' onchange='return toggleShowRenameField("foodAction","renameFood");'>
							<option value='Edit'>Edit</option>
							<option value='Delete'>Delete</option>
							<option value='Rename'>Rename</option>
						</select>
					</div>
					<div id='renameFood' style='display: none; float: left; margin-right: .5ex;'>
						<strong>New name:</strong><br />
						<input type='text' name='newFoodName' id='newFoodName' size='20' />
					</div>
					<div style='float: left;'>
						&nbsp;<br />
						<input type='submit' name='doModifyFood' value='Go' />
					</div>
				</form>
			</div>
			<div style='clear: both;'></div>
<?php else: ?>
			<div style='margin: 1ex;'>* No saved foods to manage.</div>
<?php endif;  if ($this->_tpl_vars['favMeals']): ?>
			<div style='padding: 1ex;'>
				<form action='edit_meal.php' method='post' name='formQuickEditMeal' id='formQuickEditMeal' onsubmit='return validateEditMeal("formQuickEditMeal");'>
					<div style='float: left; margin-right: .5ex; overflow: auto;'>
						<strong>Meals:</strong><br />
						<select name='meal' id='meal'>
	<?php $_from = $this->_tpl_vars['favMeals']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['favMeal']):
?>
							<option value='<?php echo $this->_tpl_vars['favMeal']['id']; ?>
'><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['favMeal']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('truncate', true, $_tmp, 50, " ...") : smarty_modifier_truncate($_tmp, 50, " ...")); ?>
</option>
	<?php endforeach; endif; unset($_from); ?>
						</select>
					</div>
					<div style='float: left; margin-right: .5ex;'>
						<strong>Action:</strong><br />
						<select name='action' id='mealAction' onchange='return toggleShowRenameField("mealAction","renameMeal");'>
							<option value='Edit'>Edit</option>
							<option value='Rename'>Rename</option>
							<option value='Delete'>Delete</option>
						</select>
					</div>
					<div id='renameMeal' style='display: none; float: left; margin-right: .5ex;'>
						<strong>New name:</strong><br />
						<input type='text' name='newMealName' size='20' />
					</div>
					<div style='float: left;'>
						&nbsp;<br />
						<input type='submit' name='doModifyMeal' value='Go' />
					</div>
				</form>
			</div>
			<div style='clear: both;'></div>
<?php else: ?>
			<div style='margin: 1ex;'>* No saved meals to manage.</div>
<?php endif;  if ($this->_tpl_vars['userDiaries']): ?>
			<div style='margin: 1ex;'>
				<form action='edit_diary.php' method='post' name='formQuickEditDiary' id='formQuickEditDiary' onsubmit='return validateEditDiary("formQuickEditDiary");'>
					<div style='float: left; margin-right: .5ex; overflow: auto;'>
						<strong>Diaries:</strong><br />
						<select name='diary' id='diaryId'>
	<?php $_from = $this->_tpl_vars['userDiaries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['userDiary']):
?>
							<option value='<?php echo $this->_tpl_vars['userDiary']['id']; ?>
'><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['userDiary']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('truncate', true, $_tmp, 50, " ...") : smarty_modifier_truncate($_tmp, 50, " ...")); ?>
</option>
	<?php endforeach; endif; unset($_from); ?>
						</select>
					</div>
					<div style='float: left; margin-right: .5ex;'>
						<strong>Action:</strong><br />
						<select name='action' id='diaryAction' onchange='return toggleShowRenameField("diaryAction","renameDiary");'>
							<option value='Delete'>Delete</option>
							<option value='Rename'>Rename</option>
						</select>
					</div>
					<div id='renameDiary' style='display: none; float: left; margin-right: .5ex;'>
						<strong>New name:</strong><br />
						<input type='text' name='newDiaryName' id='newDiaryName' size='20' />
					</div>
					<div style='float: left;'>
						&nbsp;<br />
						<input type='submit' name='doModifyDiary' value='Go' />
					</div>
				</form>
			</div>
			<div style='clear: both;'>&nbsp;</div>
<?php else: ?>
			<div style='margin: 1ex;'>* No saved diaries to manage.</div>
<?php endif; ?>
			</div>

			<div style='margin-top: 2ex; margin-bottom: 2ex;'>
				<a href='edit_food.php?action=showFoods'>Edit foods</a> - use this section to edit any/all foods,
				not just favorites.
			</div>

			<div style='margin-top: 2ex; margin-bottom: 2ex;'>
				<a href='edit_meal.php?action=showMeals'>Edit meals</a> - use this section to edit any/all meals,
				not just favorites.
			</div>

			<div style='margin-top: 2ex; margin-bottom: 2ex;'>
				<form action='add_diary.php' method='post' onsubmit='return validateCreateDiary("newDiaryName");'>
					<strong>Create</strong> a new diary named
					<input type='text' name='newDiaryName' id='newDiaryName' />
					<input type='submit' name='doCreateDiary' value='Go' />
				</form>
			</div>
			<div style='margin-top: 2ex; margin-bottom: 2ex; text-align: justify;'>
				<a href='nutrient_chooser.php'>Edit your list of standard nutrients.</a>  There is
				a large number of nutrients available for most foods.  Usually you will not be
				concerned with the majority of them, but rather only a small percentage of the
				available nutrients.  Use this section to filter out the nutrients you do not want
				to see, though you will always have the option to view all nutrient data while
				viewing any food or meal.
			</div>

			<div style='margin-top: 2ex; margin-bottom: 2ex; text-align: justify;'>
				<a href='edit_account.php'>Edit your profile.</a>  Here you can change
				things like your password, username, age, gender, or even delete your
				whole account and all the data associated with it.
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
