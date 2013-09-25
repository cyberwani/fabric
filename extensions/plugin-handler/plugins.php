<?php

function fabric_install_activate_plugins( $plugins = array() ) {

	if ( ! current_user_can('install_plugins') )
		wp_die( __( 'You do not have sufficient permissions to install plugins on this site.' ) );

	if( empty($plugins) )
		return __( 'You must specify an array of plugins to install and activate.' );

	require_once ( ABSPATH . 'wp-admin/admin.php');
	require_once ( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
	require_once ( get_template_directory() . '/extensions/plugin-handler/class-fabric-upgrader.php' );
	require_once ( ABSPATH . 'wp-admin/includes/plugin-install.php' );

	foreach($plugins as $plugin) {

		//check_admin_referer('install-plugin_' . $plugin);
		$api = plugins_api('plugin_information', array('slug' => $plugin, 'fields' => array('sections' => false) ) ); //Save on a bit of bandwidth.

		if ( is_wp_error($api) )
	 		wp_die($api);

		$title = sprintf( __('Installing Plugin: %s'), $api->name . ' ' . $api->version );
		$nonce = 'install-plugin_' . $plugin;
		$url = 'update.php?action=install-plugin&plugin=' . urlencode( $plugin );

		$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin_Fabric( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
		$installed = $upgrader->install($api->download_link);

		if($installed) {
			
			//get the main php file for the plugin, format it, and activate it
			$plugin_files = get_plugins( '/' . $plugin );

			$plugin_key = key($plugin_files);
			$plugin_dir = $plugin;

			$plugin_to_activate = $plugin_dir . '/' . $plugin_key;

			$result = activate_plugin($plugin_to_activate, '', is_network_admin() );

		}

	}

}

//$array = array('hello-dolly', 'akismet');
//fabric_install_activate_plugins( $array );