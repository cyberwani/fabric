<?php

if ( !defined('FABRIC_ACTIVE') ){
	define('FABRIC_ACTIVE', true);
}

if ( !defined('FABRIC_THEME_DIR') ){
	define('FABRIC_THEME_DIR', dirname(__FILE__) . '/');
}

if ( !defined('FABRIC_CONTROLLERS') ){
	define('FABRIC_CONTROLLERS', dirname(__FILE__) . '/Controllers/');
}

if ( !defined('FABRIC_VIEWS') ){
	define('FABRIC_VIEWS', dirname(__FILE__) . '/Views/');
}

// Supports

add_theme_support('menus');
add_theme_support('post-thumbnails');

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

// Include controller for current page/post/CPT
function fabric_include_controller() {
	$post_type = get_post_type();

	$default_controller = '\Fabric\Controllers\FabricBaseController';
	$controller = '\Fabric\Controllers\\' . 'Fabric' . $post_type . 'Controller';

	if( file_exists( FABRIC_CONTROLLERS . 'Fabric' . $post_type . 'Controller.php' ) ) {
		return new $controller;
	} else {
		return new $default_controller;
	}
}

