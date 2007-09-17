<?php /* Smarty version 2.6.11, created on 2007-05-07 20:38:28
         compiled from nutrient_search.tpl */ ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<div>
				<strong>Nutrient</strong>: <?php echo $this->_tpl_vars['nutrientName']; ?>
<br />
			</div>
<?php if (isset ( $this->_tpl_vars['searchResults'] )): ?>
			<div style='margin-top: 2ex;'>
				The following (<?php echo $_GET['count']; ?>
) foods have the highest "<?php echo $this->_tpl_vars['nutrientName']; ?>
" content in the database.<br />
				The results are in decending order (highest quantity first).<br />
				In parenthesis is the quantity of "<?php echo $this->_tpl_vars['nutrientName']; ?>
" per 100g of the displayed item.<br />
				Please select one.
			</div>
			<div style='margin-top: 2ex;'>
				<ol>
	<?php $_from = $this->_tpl_vars['searchResults']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['searchResult']):
?>
					<li><a href='food_quantity.php?food=<?php echo $this->_tpl_vars['searchResult']['food']; ?>
'>(<?php echo $this->_tpl_vars['searchResult']['nutr_val'];  echo $this->_tpl_vars['searchResult']['units']; ?>
) <?php echo $this->_tpl_vars['searchResult']['foodDesc']; ?>
</a><br /></li>
	<?php endforeach; endif; unset($_from); ?>
				</ol>
			</div>
			<div class='pageNav'>	
				<?php echo $this->_tpl_vars['pageNav']; ?>

			</div>
<?php else: ?>
			<div style='margin-top: 2ex;'>
				<span class='msgError'>No items matched your search.</span><br />
			</div>
			<div>
				Would you like to <a href='index.php?<?php echo $_SERVER['QUERY_STRING']; ?>
'>refine your search</a>?
			</div>
			<div>
				Don't understand the search options?  See the <a href='help.php#searching'>help</a> on searching.
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
