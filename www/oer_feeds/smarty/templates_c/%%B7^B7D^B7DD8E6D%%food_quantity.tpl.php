<?php /* Smarty version 2.6.11, created on 2007-04-25 11:27:06
         compiled from food_quantity.tpl */ ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<div>
				<strong>You selected</strong>: <?php echo $this->_tpl_vars['foodQuantities'][0]['foodDesc']; ?>
<br />
<?php if (! empty ( $this->_tpl_vars['foodQuantities'][0]['sciname'] )): ?>
				<strong>Scientific name</strong>: <span style='text-decoration: italic;'><?php echo $this->_tpl_vars['foodQuantities'][0]['sciname']; ?>
</span>
<?php endif; ?>
			</div>

			<div style='margin-top: 2ex;'>
				You must now choose a quantity for the selected food.  You may choose between
				various predefined quantities, or you may enter your own quantity.  If you
				enter your own quantity, any decimal number is allowable, including fractionals. 
			</div>

			<form action='view_food.php' method='post' name='formFoodQuantity' style='margin-top: 2ex;'>
				<div>
					<input type='radio' name='quantitySource' value='predefined' checked='checked' />
					Select a predefined quantity/weight:
				</div>
				<div style='margin-top: 2ex; margin-left: 5em;'>
<?php $_from = $this->_tpl_vars['foodQuantities']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foodQuantity'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foodQuantity']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['foodQuantity']):
        $this->_foreach['foodQuantity']['iteration']++;
?>
	<?php if (($this->_foreach['foodQuantity']['iteration']-1) == 0): ?>
					<input type='radio' name='predefinedWeight' value='<?php echo $this->_tpl_vars['foodQuantity']['seq']; ?>
' checked='checked' onfocus='return changeQuantitySource("0");' />
	<?php else: ?>
					<input type='radio' name='predefinedWeight' value='<?php echo $this->_tpl_vars['foodQuantity']['seq']; ?>
' onfocus='return changeQuantitySource("0");' />
	<?php endif; ?>
					<?php echo $this->_tpl_vars['foodQuantity']['amount']; ?>
 <?php echo $this->_tpl_vars['foodQuantity']['msre_desc']; ?>
 (<?php echo $this->_tpl_vars['foodQuantity']['gm_wgt']; ?>
 grams)<br />
<?php endforeach; endif; unset($_from); ?>
				</div>
				<div style='margin-top: 2ex;'>
					<input type='radio' name='quantitySource' value='userdefined' />
					Enter your own quantity/weight:
				</div>
				<div style='margin-top: 2ex; margin-left: 5em;'>
					<input type='text' name='quantity' size='5' onfocus='return changeQuantitySource("1");'/>
					<select name='userdefinedWeight'>
<?php $_from = $this->_tpl_vars['foodQuantities']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['foodQuantity']):
?>
						<option value='<?php echo $this->_tpl_vars['foodQuantity']['seq']; ?>
'><?php echo $this->_tpl_vars['foodQuantity']['msre_desc']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
					</select>
				</div>
				<div style='margin-top: 2ex;'>
					<input type='hidden' name='food' value='<?php echo $this->_tpl_vars['food']; ?>
' />
					<input type='hidden' name='action' value='getFood' />
					<input type='submit' name='doGetFood' value='Proceed' />
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
