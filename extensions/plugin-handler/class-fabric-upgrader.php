<?php

/**
 * Plugin Installer Skin for WordPress Plugin Installer.
 *
 * @TODO More Detailed docs, for methods as well.
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 2.8.0
 */
class Plugin_Installer_Skin_Fabric extends WP_Upgrader_Skin {
	var $api;
	var $type;

	function __construct($args = array()) {
		$defaults = array( 'type' => 'web', 'url' => '', 'plugin' => '', 'nonce' => '', 'title' => '' );
		$args = wp_parse_args($args, $defaults);

		$this->type = $args['type'];
		$this->api = isset($args['api']) ? $args['api'] : array();

		parent::__construct($args);
	}

	function before() {
		if ( !empty($this->api) )
			$this->upgrader->strings['process_success'] = sprintf( __('Successfully installed the plugin <strong>%s %s</strong>.'), $this->api->name, $this->api->version);
	}

	function after() {

		$plugin_file = $this->upgrader->plugin_info();

		if ( $this->result && !is_wp_error($this->result) ) {

			

		}

	}
}