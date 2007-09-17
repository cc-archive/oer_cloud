<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
        
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>

<head>
	<title>LOGIN</title>
	<link rel='stylesheet' type='text/css' href='<?php echo $config->_cssDir; ?>/site.css' />
</head>

<body onload='focusFormField("_user");'>

<form name='frmLogin' method='post' action='<?php echo $PHP_SELF; ?>' onsubmit='return validateLoginFields();'>
	<div class='loginBox'>
		<div class='loginBoxHead'>
			<span>OER Feeds</span> 
		</div>
		<div class='loginBoxLabels'>
			<div class='loginBoxLabel'>Login</div>
			<div class='loginBoxLabel'>Password</div>
		</div>
		<div class='loginBoxFields'>
			<div class='loginBoxField'>
				<input type='text' class='loginInputField' name='login' id='_user' />
			</div>
			<div class='loginBoxField'>
				<input type='password' class='loginInputField' name='password' id='_pass' />
			</div>
		</div>
		<div class='loginBoxFoot'>
{if $errMsg}
			<span class='msgError'>{$errMsg}</span>";
{/if}
			<input type='submit' value='Enter' class='loginButton' name='doLogin' />
		</div>
	</div>
</form>
		
</body>

</html>
