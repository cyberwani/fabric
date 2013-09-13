<?php

require ( 'phpsvnclient.php' );

$plugins_to_pull	= array(
	'bbpress-new-topic-emailer',
	'advanced-custom-fields'
);

function fabric_checkout_plugin( $plugins ) {

	$wordpress_svn_url 	= 'http://plugins.svn.wordpress.org';
	$plugins_dir		= '/Users/mattkeys/Desktop/W/Fabric/wp-content/plugins/';

	$svn_handler = new phpsvnclient( $wordpress_svn_url );

	if(is_array( $plugins )) {

		foreach( $plugins as $plugin )
		{
			$svn_handler->checkOut( $plugin . '/trunk', $plugins_dir . $plugin . '/' , true );
		}

	} else {

		$svn_handler->checkOut( $plugins .'/trunk', $plugins_dir . $plugins . '/' , true );

	}

}

fabric_checkout_plugin( $plugins_to_pull );