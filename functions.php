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
include 'lib/core/class-fabric-activation.php';

// Verify template redirection is active, attempt to install if not
if( !defined('FABRIC_TEMPLATE_REDIRECTION_ACTIVE') && !is_admin() ) {
	fabric_activation( true );
}

// Include all other Fabric lib classes
include 'lib/core/class-fabric-auto-enqueue.php';
include 'lib/core/class-fabric-cache-busting.php';
include 'lib/core/class-fabric-clean-up.php';
include 'lib/core/class-fabric-controller.php';
include 'lib/core/class-fabric-include-post-types.php';
include 'lib/core/class-fabric-include-taxonomies.php';
include 'lib/core/class-fabric-loop-iterator.php';
include 'lib/core/class-fabric-template-wrapper.php';

// Trigger our fabric_loaded actions. This action happens before Init is fired
do_action( 'fabric_loaded' );





/*
 *	WAIT! Your custom code doesn't belong here!
 *	Custom functionality should go into one of your controllers
 */