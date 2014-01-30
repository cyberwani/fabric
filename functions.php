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

// Include all Fabric lib classes
include 'lib/FabricActivation.php';
include 'lib/FabricAutoEnqueue.php';
include 'lib/FabricCacheBusting.php';
include 'lib/FabricCleanUp.php';
include 'lib/FabricController.php';
include 'lib/FabricIncludePostTypes.php';
include 'lib/FabricIncludeTaxonomies.php';
include 'lib/FabricLoopIterator.php';
include 'lib/FabricTemplateWrapper.php';

// Trigger our fabric_loaded actions. This action happens before Init is fired
do_action( 'fabric_loaded' );


/*
 *	WAIT! Your custom code doesn't belong here!
 *	Custom functionality should go into one of your controllers
 */