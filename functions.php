<?php

if ( !defined('FABRIC_THEME_DIR') ){
	define('FABRIC_THEME_DIR', dirname(__FILE__) . '/');
}
if ( !defined('FABRIC_INCLUDES_DIR') ){
	define('FABRIC_INCLUDES_DIR', dirname(__FILE__) . '/functions/includes/');
}

require_once locate_template('/functions/activation.php');
require_once locate_template('/functions/content-creation.php');