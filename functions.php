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

// Auto-Include any php files from the root of our /functions/ directory
if ($functions_handle = opendir(FABRIC_THEME_DIR . 'functions/')) {

    while (false !== ($entry = readdir($functions_handle))) {
		$ext = pathinfo($entry, PATHINFO_EXTENSION);
		if($ext == 'php') {
			include ( FABRIC_THEME_DIR . 'functions/' . $entry );
		}
    }
    closedir($functions_handle);
}

/*
 *	WAIT! Your custom code doesn't belong here!
 *	Custom functionality should go into the /functions/ folder. For small changes add your code to /functions/custom.php. If your code is more complicated, create a new file for it in the /functions/ directory, or consider creating a plugin. Don't forget to ask yourself if your code would be better suited to go into a controller as well.
 */