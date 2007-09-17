<?php

require ("config.php");

if ( isset($_POST['doLogin']) ) {
	if ( validateUser($_POST['login'], $_POST['password']) ) {
		header ("Location: {$config->_rootUri}/index.php");
		exit;
	} else {
		$errMsg = "<span style='color: red;'>login failed</span>";
	}
}

if ( isset($_REQUEST['logout']) ) {
	session_unset();
	session_destroy();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
        
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en'>

<head>
	<title>LOGIN - OER Feeds</title>
	<link rel='stylesheet' type='text/css' href='<?php echo $config->_cssUri; ?>/site.css' />
	<script type='text/javascript' src='<?php echo $config->_jsUri; ?>/site.js'></script>
</head>

<body onload='focusFormField("login");'>

<form name='frmLogin' method='post' action='<?php echo $_SERVER['PHP_SELF']; ?>'>
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
				<input type='text' class='loginInputField' name='login' id='login' />
			</div>
			<div class='loginBoxField'>
				<input type='password' class='loginInputField' name='password' id='password' />
			</div>
		</div>
		<div class='loginBoxFoot'>
		
<?php

	if ( ! empty($errMsg) ) {
 		echo "			<span class='msgError'>$errMsg</span>";
	}
	
?>

			<input type='submit' value='Enter' class='loginButton' name='doLogin' />
		</div>
	</div>
</form>
		
</body>

</html>
