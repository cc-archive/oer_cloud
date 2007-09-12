<?php /* Smarty version 2.6.11, created on 2007-05-08 16:30:26
         compiled from view_meal.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'view_meal.tpl', 35, false),)), $this); ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>
	<div id='middleColumn'>
		<div id='middleData'>
<?php if ($this->_tpl_vars['isLoggedIn']): ?>
			<div style='margin-bottom: 1em;'>
				<strong>Meal</strong>: <?php echo $this->_tpl_vars['mealDesc']; ?>

	<?php if ($this->_tpl_vars['meal'] != '0'): ?>
				[<a href='edit_meal.php?meal=<?php echo $this->_tpl_vars['meal']; ?>
&action=showMeal'>Edit</a>]
	<?php endif; ?>
			</div>
			<div style='margin-bottom: 1em;'>
	<?php if ($this->_tpl_vars['showAllNutrients']): ?>
				<a href='<?php echo $this->_tpl_vars['config']->_rootUri; ?>
/<?php echo $this->_tpl_vars['config']->_thisScript; ?>
?meal=<?php echo $this->_tpl_vars['meal']; ?>
&action=<?php echo $_GET['action']; ?>
'>
				    Hide unwanted nutrients</a> |
	<?php else: ?>
				<a href='<?php echo $this->_tpl_vars['config']->_rootUri; ?>
/<?php echo $this->_tpl_vars['config']->_thisScript; ?>
?meal=<?php echo $this->_tpl_vars['meal']; ?>
&action=<?php echo $_GET['action']; ?>
&showall'>
					Show all nutrients</a> |
	<?php endif; ?>
				<a href='nutrient_chooser.php'>Manage nutrient list</a>
			</div>
<?php else: ?>
			<div style='margin-bottom: 1em;'>
				<strong>Meal</strong>: <?php echo $this->_tpl_vars['mealDesc']; ?>

			</div>
<?php endif; ?>
			<div style='width: 100%;'>
				<table class='standardTable'>
					<tr class='tableTitleRow'>
<?php $_from = $this->_tpl_vars['mealData']['columnTitles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['columnTitle']):
?>
						<th><?php echo $this->_tpl_vars['columnTitle']; ?>
</th>
<?php endforeach; endif; unset($_from); ?>
					</tr>
<?php $_from = $this->_tpl_vars['mealData']['nutrients']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['nutrient']):
?>
					<tr class='<?php echo smarty_function_cycle(array('values' => "bgDark,bgLight"), $this);?>
'>
						<td><?php echo $this->_tpl_vars['nutrient']['nutrientName']; ?>
</td>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['nutrient']['total'];  echo $this->_tpl_vars['nutrient']['units']; ?>
</td>
	<?php if ($this->_tpl_vars['nutrient']['percentDri'] != "--"): ?>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['nutrient']['percentDri']; ?>
&#37;</td>
	<?php else: ?>
						<td style='text-align: center;'>--</td>
	<?php endif; ?>
	<?php $_from = $this->_tpl_vars['nutrient']['quantities']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['quantity']):
?>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['quantity']; ?>
</td>
	<?php endforeach; endif; unset($_from); ?>
					</tr>
<?php endforeach; endif; unset($_from); ?>
				</table>
			</div>
<?php if ($this->_tpl_vars['isLoggedIn']): ?> 
			<div style='margin-top: 1em;'>
				<form action='add_meal.php' method='post' name='formAddMeal' id='formAddMeal' onsubmit='return validateAddMeal("formAddMeal","mealDesc");'>
					<a name='save'></a>
					<div style='margin-bottom: .5em; text-align: justify;'>
						If you would like to save this meal for later reference, or add it
						to a diary, enter a short descriptive entry in the text box below
						and then select the appropriate button.  If you are unsure how all
						of this works, then take a look at the help on
						<a href='help.php#meals'>creating meals</a>.
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='text' name='description' id='mealDesc' style='width: 100%' value='<?php echo $this->_tpl_vars['mealDesc']; ?>
' />
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='submit' name='saveMeal' value='Save meal' style='width: 20ex;' onclick='document.formAddMeal.action.value = "saveMeal";'  />
					</div>

	<?php if ($this->_tpl_vars['userDiaries']): ?>
					<div style='margin-right: 1ex; margin-bottom: .5em; float: left;'>
						<input type='submit' name='addMealToDiary' id='addMealToDiary' value='Add to diary =&gt;' style='width: 20ex;' onclick='document.formAddMeal.action.value = "addMealToDiary";' />
						<select name='diary'>
		<?php $_from = $this->_tpl_vars['userDiaries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['userDiary']):
?>
							<option value='<?php echo $this->_tpl_vars['userDiary']['id']; ?>
'><?php echo $this->_tpl_vars['userDiary']['description']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>
						</select>
					</div>
					<div style='margin-bottom: .5em;'>
						with <a href='help.php#timestamp' title='What is a diary timestamp?'>timestamp</a>
						<input type='text' name='diaryTimestamp' id='diaryTimestamp' readonly='readonly' value='' />
						<script type="text/javascript">
							Calendar.setup(
								<?php echo '{'; ?>

									inputField	: "diaryTimestamp", // ID of the input field
									ifFormat	: "%Y-%m-%d %I:%M%p", // the date format
									button		: "diaryTimestamp", // ID of the button
									weekNumbers	: false,
									showsTime	: true,
									firstDay	: 0
								<?php echo '}'; ?>

							);
						</script>
					</div>
	<?php endif; ?>
					
					<input type='hidden' name='meal' value='<?php echo $this->_tpl_vars['meal']; ?>
' />
					<input type='hidden' name='action' value='' />
				</form>
			</div>
<?php endif; ?>
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
