<?php

function fabric_activation( $reinstall = false ) {

	$error_string = __( "Fabric was unable to automatically place the 'template redirection' plugin into the mu-plugins folder. This is required for Fabric to function. You must manually place /fabric/lib/activation/class-fabric-template-redirection.php into the /wp-content/mu-plugins/ directory. If the mu-plugins folder does not exist, you should create it.", 'fabric' );

	$plugin_src = file_get_contents( FABRIC_ACTIVATION_DIR . 'class-fabric-template-redirection.php' );

	if ( ! is_dir( WP_CONTENT_DIR . '/mu-plugins' ) && is_writable( WP_CONTENT_DIR ) )
		mkdir( WP_CONTENT_DIR . '/mu-plugins', 0755 );

	if ( ! is_dir( WP_CONTENT_DIR . '/mu-plugins' ) || ! is_writable( WP_CONTENT_DIR . '/mu-plugins' ) ) {
		$error = new WP_Error( 'redirection', $error_string );
		die( $error->get_error_message() );
	}

	$write_result = file_put_contents( WP_CONTENT_DIR . '/mu-plugins/class-fabric-template-redirection.php', $plugin_src );

	if ( $reinstall ) {
		die( __( 'Fabric template redirection was missing, and has been reinstalled. Please refresh to continue. If problems persist please consult the documentation.', 'fabric' ) );
	}
}

function fabric_deactivation() {

	// Remove Template Redirection
	if ( file_exists( WP_CONTENT_DIR . '/mu-plugins/class-fabric-template-redirection.php' ) ) {
		unlink( WP_CONTENT_DIR . '/mu-plugins/class-fabric-template-redirection.php' );
	}
}
add_action( 'switch_theme', 'fabric_deactivation' );

function fabric_read_package_ajax() {

	$package = $_POST['fabric_package'];
	require_once FABRIC_ACTIVATION_DIR . 'spyc.php';
	$packages = Spyc::YAMLLoad( FABRIC_PACKAGES_DIR . $package );

	echo json_encode( $packages );
	exit;
}
add_action( 'wp_ajax_fabric_read_package_ajax', 'fabric_read_package_ajax' );

function fabric_read_package( $package ) {
	require_once FABRIC_ACTIVATION_DIR . 'spyc.php';
	$packages = Spyc::YAMLLoad( FABRIC_PACKAGES_DIR . $package );

	return $packages;
}

function fabric_get_packages() {
	$packages = array();

	if ( $handle = opendir( FABRIC_PACKAGES_DIR ) ) {
		while ( false !== ( $entry = readdir( $handle ) ) ) {
			$ext = pathinfo( $entry, PATHINFO_EXTENSION );
			if ( 'yml' == $ext ) {
				$packages[] = $entry;
			}
		}
		closedir( $handle );
	}

	return $packages;
}

function fabric_check_if_plugin_installed( $slug ) {

	$keys = array_keys( get_plugins() );

	foreach ( $keys as $key ) {
		if ( preg_match( '|^' . $slug .'/|', $key ) ) {
			return $key;
		}
	}

	return false;
}

function fabric_starts_with( $haystack, $needle ) {
	return $needle === "" || strpos( $haystack, $needle ) === 0;
}

function fabric_ends_with( $haystack, $needle ) {
	return $needle === "" || substr( $haystack, -strlen( $needle ) ) === $needle;
}

function fabric_install_packages( $customizer_options ) {

	$plugins_to_install = explode( ',', $customizer_options->{'fabric-packages'} );
	$plugins_to_install = array_filter( $plugins_to_install );

	if ( empty( $plugins_to_install ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api

	$plugin_sources = array();
	$x = 0;

	foreach( $plugins_to_install as $plugin )
	{
		// Handle local zipped plugins
		if ( fabric_starts_with( $plugin, 'local_' ) && fabric_ends_with( $plugin, '.zip' ) ) {

			$slug = substr( $plugin, 6 );
			$slug = substr( $slug, 0, -4 );

			$plugin_exist = fabric_check_if_plugin_installed( $slug );

			if ( $plugin_exist ) {
				continue;
			}

			$plugin_src = FABRIC_PACKAGES_DIR . 'local_plugins/' . $slug . '.zip';

			$plugin_sources[ $x ] = $plugin_src;
			$x++;

			continue;
		}

		$plugin_exist = fabric_check_if_plugin_installed( $plugin );

		if ( $plugin_exist ) {
			continue;
		}

		$result = plugins_api( 'plugin_information', array(
			'slug' => $plugin,
			'fields' => array(
					'sections' => false
				)
		) );

		if ( ! $result || ! isset( $result->download_link ) ) {
			continue;
		}

		$plugin_sources[ $x ] = $result->download_link;
		$x++;
	}

	if ( empty( $plugin_sources ) ) {
		return;
	}

	require_once FABRIC_ACTIVATION_DIR . '/class-tgm-plugin-activation.php';

	$TGM_Bulk_Installer = new TGM_Bulk_Installer;
	$TGM_Bulk_Installer->is_automatic = true;

	$TGM_Bulk_Installer->bulk_install( $plugin_sources, true );
}

// Fabric Theme Customizer Classes
if ( class_exists('WP_Customize_Control') ) {
	class Fabric_Plugin_Packages extends WP_Customize_Control {
		public $type = 'fabric-packages';
	
		public function render_content()
		{
			$packages = fabric_get_packages();
			?>
			<script src="<?php echo get_template_directory_uri(); ?>/lib/activation/theme-customizer.js"></script>
			<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/lib/activation/theme-customizer.css" type="text/css">

			<small class="description"><?php printf(__('Choose a preconfigured installation package', 'fabric')); ?></small>
			<select name="fabric_theme_activation_options[fabric_package]" id="choose_a_package">
				<option>Select Package</option>
				<?php
				foreach( $packages as $package )
				{ 
					?>
					<option value="<?php echo $package; ?>"><?php echo $package; ?></option>
					<?php
				}
				?>
			</select>
			<br />
			<br />
			<table id="packages"></table>
			<input type="hidden" id="<?php echo $this->id; ?>" class="cstmzr-hidden-packages" <?php $this->link(); ?> value="<?php echo sanitize_text_field( $this->value() ); ?>">
			<?php
		}

	}
	class WP_Customize_Control_With_Description extends WP_Customize_Control {
		public $description;
	
		public function render_content()
		{
			parent::render_content();
			echo '<p class="description">'.$this->description.'</p>';
		}

	}
}