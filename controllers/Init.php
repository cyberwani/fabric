<?php
/**
 * =======================================
 * Init Controller
 * =======================================
 *
 * This controller fires very early so you can use the init action hook
 *
 * To use, add a line to the construct function like:
 * add_action( 'init', array( new MyFabricClass, 'my_function' ) );
 *
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 */

namespace Fabric\Controllers;

class Init
{

	public function __construct()
	{
		add_action( 'wp_head', array( new Base, 'google_analytics_tracking' ), 99 );
	}

}