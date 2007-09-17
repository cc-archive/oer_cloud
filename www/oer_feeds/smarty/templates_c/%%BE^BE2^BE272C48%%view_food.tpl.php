<?php /* Smarty version 2.6.11, created on 2007-05-08 09:26:59
         compiled from view_food.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'cycle', 'view_food.tpl', 37, false),array('modifier', 'escape', 'view_food.tpl', 64, false),)), $this); ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>
	<div id='middleColumn'>
		<div id='middleData'>
			<div style='margin-bottom: 1em;'>
<?php if ($this->_tpl_vars['foodDesc']): ?>
				<strong>Food</strong>: <?php echo $this->_tpl_vars['foodDesc']; ?>
 (<?php echo $this->_tpl_vars['foodData'][0]['foodDesc']; ?>
)<br />
<?php else: ?>
				<strong>Food</strong>: <?php echo $this->_tpl_vars['foodData'][0]['foodDesc']; ?>
<br />
<?php endif;  if (! empty ( $this->_tpl_vars['foodData'][0]['sciname'] )): ?>
				<strong>Scientific name</strong>: <?php echo $this->_tpl_vars['foodData'][0]['sciname']; ?>
<br />
<?php endif; ?>
				<strong>Quantity</strong>: <?php echo $this->_tpl_vars['quantity']; ?>
 <?php echo $this->_tpl_vars['foodData'][0]['msre_desc']; ?>
 (about <?php echo $this->_tpl_vars['gramWeight']; ?>
 grams)
			</div>
			<div style='margin-bottom: 1em;'>
<?php if ($this->_tpl_vars['showAllNutrients']): ?>
				<a href='<?php echo $this->_tpl_vars['config']->_rootUri; ?>
/<?php echo $this->_tpl_vars['config']->_thisScript; ?>
?food=<?php echo $this->_tpl_vars['food']; ?>
&weight=<?php echo $this->_tpl_vars['weight']; ?>
&quantity=<?php echo $this->_tpl_vars['quantity']; ?>
'>
				    Hide unwanted nutrients</a>
<?php else: ?>
				<a href='<?php echo $this->_tpl_vars['config']->_rootUri; ?>
/<?php echo $this->_tpl_vars['config']->_thisScript; ?>
?food=<?php echo $this->_tpl_vars['food']; ?>
&weight=<?php echo $this->_tpl_vars['weight']; ?>
&quantity=<?php echo $this->_tpl_vars['quantity']; ?>
&showall'>
					Show all nutrients</a>
<?php endif;  if ($this->_tpl_vars['isLoggedIn']): ?>
				| <a href='nutrient_chooser.php'>Manage nutrient list</a>
<?php endif; ?>
			</div>
			<div style='width: 100%;'>
				<table class='standardTable'>
					<tr class='tableTitleRow'>
						<th>Nutrient</th>
						<th>Quantity</th>
						<th>&#37;DRI</th>
					</tr>

<?php $_from = $this->_tpl_vars['foodData']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['nutrient']):
?>
					<tr class='<?php echo smarty_function_cycle(array('values' => "bgDark,bgLight"), $this);?>
'>
						<td><?php echo $this->_tpl_vars['nutrient']['nutrdesc']; ?>
</td>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['nutrient']['nutrientQuantity'];  echo $this->_tpl_vars['nutrient']['units']; ?>
</td>
	<?php if ($this->_tpl_vars['nutrient']['percentDri'] != "--"): ?>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['nutrient']['percentDri']; ?>
&#37;</td>
	<?php else: ?>
						<td style='text-align: center;'>--</td>
	<?php endif; ?>
					</tr>
<?php endforeach; endif; unset($_from); ?>
				</table>
			</div>
			<div style='margin-top: 1em;'>
				<form action='add_food.php' method='post' id="formAddFood" onsubmit='return validateAddFood("formAddFood","foodDesc");'>
<?php if ($this->_tpl_vars['isLoggedIn']): ?> 
					<a name='save'></a>
					<div style='margin-bottom: .5em; text-align: justify;'>
						If you would like to save this item for later reference, or add it
						to a meal or diary, enter a short descriptive entry in the
						text box below and then select the appropriate button.  A default
						description may have been added for you.  However, this description may
						not be very helpful and could possibly be quite long.  You are
						encouraged to change this to something more meaningful, and possibly
						shorter.  Are you still <a href='help.php#saving'>confused</a>?
					</div>
					<div style='margin-bottom: .5em;'>
	<?php if ($this->_tpl_vars['foodDesc']): ?>
						<input type='text' name='description' id='foodDesc' size='25' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['foodDesc'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' />
	<?php else: ?>
						<input type='text' name='description' id='foodDesc' size='25' value='<?php echo $this->_tpl_vars['quantity']; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['foodData'][0]['msre_desc'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['foodData'][0]['foodDesc'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' />
	<?php endif; ?>
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='submit' name='saveFood' id='saveFood' value='Save food' style='width: 20ex;' onclick='getElement("formAddFood").action.value = "saveFood";' />
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='submit' name='addFoodToMeal' id='addFoodToMeal' value='Add to meal =&gt;' style='width: 20ex;' onclick='getElement("formAddFood").action.value = "addFoodToMeal";' />
						<select name='meal'>
							<option selected='selected' value='0'>New meal</option>
	<?php $_from = $this->_tpl_vars['myMeals']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['myMeal']):
?>
							<option value='<?php echo $this->_tpl_vars['myMeal']['id']; ?>
'><?php echo $this->_tpl_vars['myMeal']['description']; ?>
</option>
	<?php endforeach; endif; unset($_from); ?>
						</select>
					</div>
	<?php if ($this->_tpl_vars['userDiaries']): ?>
					<div style='margin-right: 1ex; margin-bottom: .5em; float: left;'>
						<input type='submit' name='addFoodToDiary' id='addFoodToDiary' value='Add to diary =&gt;' style='width: 20ex;' onclick='getElement("formAddFood").action.value = "addFoodToDiary";' />
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
						with
						<a href='help.php#timestamp' title='What is a diary timestamp?'>timestamp</a>
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
	<?php endif;  else: ?>
					<a name='save'></a>
					<div style='margin-bottom: .5em; text-align: justify;'>
						Would you like to add this food to a meal?  Enter a short
						descriptive entry in the text box below and then click the
						"Add to meal" button.  A default description may have been
						added for you.  However, this description may not be very
						helpful and could possibly be quite long.  You are encouraged
						to change this to something more meaningful, and possibly
						shorter.  Are you still <a href='help.php#saving'>confused</a>?
					</div>
					<div style='margin-bottom: .5em;'>
	<?php if ($this->_tpl_vars['foodDesc']): ?>
						<input type='text' name='description' size='25' value='<?php echo ((is_array($_tmp=$this->_tpl_vars['foodDesc'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' />
	<?php else: ?>
						<input type='text' name='description' size='25' value='<?php echo $this->_tpl_vars['quantity']; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['foodData'][0]['msre_desc'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['foodData'][0]['foodDesc'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
' />
	<?php endif; ?>
					</div>
					<div style='margin-bottom: .5em;'>
						<input type='submit' name='addFoodToMeal' value='Add to meal' style='width: 20ex;' />
					</div>
					<input type='hidden' name='meal' value='0' />
<?php endif; ?>
					<input type='hidden' name='food' value='<?php echo $this->_tpl_vars['food']; ?>
' />
					<input type='hidden' name='weight' value='<?php echo $this->_tpl_vars['weight']; ?>
' />
					<input type='hidden' name='quantity' value='<?php echo $this->_tpl_vars['quantity']; ?>
' />
					<input type='hidden' name='action' value='' />
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
