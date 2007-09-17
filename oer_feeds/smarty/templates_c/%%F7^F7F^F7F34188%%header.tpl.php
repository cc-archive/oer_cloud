<?php /* Smarty version 2.6.14, created on 2007-09-17 11:57:08
         compiled from header.tpl */ ?>
<?php echo '<?xml'; ?>
 version='1.0' <?php echo '?>'; ?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
	
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>

<head><?php echo $this->_tpl_vars['myHeaders']; ?>
</head>

<body>

<div id='header'>
	<div id='headerLogo'>
		<a href='<?php echo $this->_tpl_vars['config']->_rootUri; ?>
/'><img style='vertical-align: middle;' src='<?php echo $this->_tpl_vars['config']->_imgUri; ?>
/cc-learn.png' alt='ccLearn'/></a>
	</div>
	<div id='headerRight'>
		<a href='login.php?logout'>[log out]</a>
	</div>
	<div id='headerInfoBar'>
		<div id='headerLinks'>
			<strong>OER Feeds</strong>
		</div>
		<div id='systemMsgs'><?php echo $this->_tpl_vars['systemMsg']; ?>
</div>
	</div>
</div>