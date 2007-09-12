<?php

# aqui estan establecidos varias variables globales
# y estan incluidos varias classes y librerias
require ("../config.php");

# a user is attempting to login
if ( isset($_POST) ) {
	if ( isset($_POST['doLogin']) ) {
		if ( validateUser($_POST['user'], $_POST['pass']) ) {
			header ("Location: $config->_adminURL"); # redirect usuario
			exit;
		} else {
			$errMsg = "Usuario / Contrase&ntilde;a no v&aacute;lido(a).";
		}
	}
}

# the user wants to logout ... kill the session
if ( isset($_REQUEST) && isset($_REQUEST['logout']) ) {
		session_unset(); # mata las variables de la sesion
		session_destroy(); # mata la sesion
}

?>
