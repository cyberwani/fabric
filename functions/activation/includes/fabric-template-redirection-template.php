<?php
/**
 * @package Fabric Template Redirection
 * @version 1.0
 */
/*
Plugin Name: Fabric Template Redirection
Plugin URI: http://UpTrending.com
Description: Handles redirection of theme templates to the /views folder in the Fabric Theme
Author: Matt Keys
Version: 1.0
Author URI: http://UpTrending.com
*/

if ( !defined('FABRIC_TEMPLATE_REDIRECTION_ACTIVE') ){
	define('FABRIC_TEMPLATE_REDIRECTION_ACTIVE', true);
}

// Append our views folder to the template directory path
function fabric_redirect_template_directory($template_dir, $template, $theme_root) {
    $new_template_dir = $template_dir . '/views';

    if(!file_exists($new_template_dir . '/index.php'))
    	return $template_dir;

    return $new_template_dir;
}
add_filter( 'template_directory', 'fabric_redirect_template_directory', 10, 3 );

// Avoid issues with the theme deactivating itself when it cannot find the style.css file in our /views folder. An alternative option would be to include a duplicate style.css in /views
function fabric_validate_theme() {
	return false;
}
add_filter( 'validate_current_theme', 'fabric_validate_theme' );