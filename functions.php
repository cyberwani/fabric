<?php

if ( !defined('FABRIC_THEME_DIR') ){
	define('FABRIC_THEME_DIR', dirname(__FILE__) . '/');
}

if ( !defined('FABRIC_CONTROLLERS') ){
	define('FABRIC_CONTROLLERS', dirname(__FILE__) . '/controllers/');
}

if ( !defined('FABRIC_VIEWS') ){
	define('FABRIC_VIEWS', dirname(__FILE__) . '/views/');
}

// Include all functions
if ($functions_handle = opendir(FABRIC_THEME_DIR . 'functions/')) {
    while (false !== ($entry = readdir($functions_handle))) {
		$ext = pathinfo($entry, PATHINFO_EXTENSION);
		if($ext == 'php') {
			require_once ( FABRIC_THEME_DIR . 'functions/' . $entry );
		}
    }
    closedir($functions_handle);
}