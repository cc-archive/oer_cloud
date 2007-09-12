<?php /* Smarty version 2.6.11, created on 2007-04-25 17:59:48
         compiled from list_diaries.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'list_diaries.tpl', 11, false),array('function', 'math', 'list_diaries.tpl', 17, false),)), $this); ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>
			<h3 style='text-align: center;'>Diaries</h3>
<?php if ($this->_tpl_vars['userDiaries']): ?>
	<?php if ($this->_tpl_vars['diaryCount'] < 50): ?>
			<div>
		<?php $_from = $this->_tpl_vars['userDiaries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['userDiaries'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['userDiaries']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['userDiary']):
        $this->_foreach['userDiaries']['iteration']++;
?>
				<div><?php echo $this->_foreach['userDiaries']['iteration']; ?>
) <a href='view_diary.php?diary=<?php echo $this->_tpl_vars['userDiary']['id']; ?>
&action=viewDiary'><?php echo ((is_array($_tmp=$this->_tpl_vars['userDiary']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a></div>
		<?php endforeach; endif; unset($_from); ?>
			</div>
	<?php else: ?>
			<div style='float: left; margin-right: 5ex;'>
		<?php $_from = $this->_tpl_vars['userDiaries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['userDiaries'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['userDiaries']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['userDiary']):
        $this->_foreach['userDiaries']['iteration']++;
?>
			<?php echo smarty_function_math(array('equation' => 'ceil(x/2) + 1','x' => $this->_tpl_vars['diaryCount'],'assign' => 'medianDiary'), $this);?>

			<?php if ($this->_foreach['userDiaries']['iteration'] == $this->_tpl_vars['medianDiary']): ?>
			</div>
			<div style='float: left; width: 49%;'>
			<?php endif; ?>
				<div><?php echo $this->_foreach['userDiaries']['iteration']; ?>
) <a href='view_diary.php?diary=<?php echo $this->_tpl_vars['userDiary']['id']; ?>
&action=viewDiary'><?php echo ((is_array($_tmp=$this->_tpl_vars['userDiary']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a></div>
		<?php endforeach; endif; unset($_from); ?>
			</div>
	<?php endif;  else: ?>
			<div>* No saved diaries.</div>
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
