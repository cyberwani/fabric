<?php

if( !defined('FABRIC_THEME_DIR') ) {
	define('FABRIC_THEME_DIR', dirname(__FILE__) . '/');
}
if( !defined('FABRIC_CONTROLLERS') ) {
	define('FABRIC_CONTROLLERS', dirname(__FILE__) . '/controllers/');
}
if( !defined('FABRIC_VIEWS') ) {
	define('FABRIC_VIEWS', dirname(__FILE__) . '/views/');
}

// Include fabric activation lib class
include 'lib/FabricActivation.php';

// Verify template redirection is active, attempt to install if not
if( !defined('FABRIC_TEMPLATE_REDIRECTION_ACTIVE') && !is_admin() ) {
	fabric_activation( true );
}

// Include all other Fabric lib classes
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