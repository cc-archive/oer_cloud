<?php /* Smarty version 2.6.11, created on 2007-04-24 20:57:25
         compiled from food_search.tpl */ ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<div>
				<strong>Search text</strong>: '<?php echo $this->_tpl_vars['searchString']; ?>
'<br />
				<strong>Search type</strong>: <?php echo $this->_tpl_vars['searchType']; ?>
/<?php echo $this->_tpl_vars['wordType']; ?>
<br />
				<strong>Category</strong>: <?php echo $this->_tpl_vars['foodCatName']; ?>
<br />
				<strong>Sort by</strong>: <?php echo $this->_tpl_vars['sortType']; ?>

			</div>
<?php if (isset ( $this->_tpl_vars['searchResults'] )): ?>
			<div style='margin-top: 2ex;'>
				The following items matched your search.
				Select one, or <a href='index.php?<?php echo $_SERVER['QUERY_STRING']; ?>
'>refine your search</a>.
			</div>
			<div style='margin-top: 2ex;'>
	<?php if ($this->_tpl_vars['sortType'] == 'Category'): ?>
		<?php $_from = $this->_tpl_vars['searchResults']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['foodCat']):
?>
			<div style='text-align: center; background-color: #e0e0e0;'><?php echo $this->_tpl_vars['foodCat']['foodCatName']; ?>
</div>
			<?php $_from = $this->_tpl_vars['foodCat']['searchResults']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['searchResult']):
?>
				<a href='food_quantity.php?food=<?php echo $this->_tpl_vars['searchResult']['food']; ?>
'><?php echo $this->_tpl_vars['searchResult']['foodDesc']; ?>
</a><br />
			<?php endforeach; endif; unset($_from); ?>
		<?php endforeach; endif; unset($_from); ?>
	<?php else: ?>
		<?php $_from = $this->_tpl_vars['searchResults']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['searchResult']):
?>
			<a href='food_quantity.php?food=<?php echo $this->_tpl_vars['searchResult']['food']; ?>
'><?php echo $this->_tpl_vars['searchResult']['foodDesc']; ?>
</a><br />
		<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
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
