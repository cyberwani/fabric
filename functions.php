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
if ( !defined('FABRIC_LIB') ){
	define('FABRIC_LIB', dirname(__FILE__) . '/lib/');
}

// Auto-Include any php files from the root of our /lib/ directory
if ( is_dir( FABRIC_LIB ) && $functions_handle = opendir( FABRIC_LIB ) ) {

    while (false !== ($entry = readdir($functions_handle))) {
		$ext = pathinfo($entry, PATHINFO_EXTENSION);
		if($ext == 'php') {
			include ( FABRIC_LIB . $entry );
		}
    }
    closedir($functions_handle);
}

// Trigger our fabric_loaded actions
do_action( 'fabric_loaded' );


/*
 *	WAIT! Your custom code doesn't belong here!
 *	Custom functionality should go into one of your controllers
 */
