<?php

function fabric_setup_plugin_notifications() {

    $selected_plugins = get_option('fabric-packages');

    if( empty( $selected_plugins ) )
        return;

    $selected_plugins_array = explode(',', $selected_plugins);

    $all_packages = fabric_get_packages();

    $plugins_to_install = array();

    $x=0;
    foreach( $all_packages as $package )
    {
        $current_package = fabric_read_package( $package );

        foreach( $current_package as $package_group => $group_info )
        {
            foreach( $group_info['plugins'] as $plugin => $plugin_info )
            {
                $slug = basename($plugin_info['url']);
                if( in_array( $slug, $selected_plugins_array ) ) {
                    $plugins_to_install[$x]['name']     = $plugin;
                    $plugins_to_install[$x]['slug']     = $slug;
                    $plugins_to_install[$x]['required'] = true;
                    $plugins_to_install[$x]['force_activation'] = false;
                }
                $x++;
            }
        }
    }

    $config = array(
        'domain'            => 'fabric',                    // Text domain - likely want to be the same as your theme.
        'default_path'      => '',                          // Default absolute path to pre-packaged plugins
        'parent_menu_slug'  => 'themes.php',                // Default parent menu slug
        'parent_url_slug'   => 'themes.php',                // Default parent URL slug
        'menu'              => 'install-required-plugins',  // Menu slug
        'has_notices'       => true,                        // Show admin notices or not
        'is_automatic'      => false,                       // Automatically activate plugins after installation or not
        'message'           => '',                          // Message to output right before the plugins table
        'strings'           => array(
            'page_title'                                => __( 'Install Required Plugins', 'fabric' ),
            'menu_title'                                => __( 'Install Plugins', 'fabric' ),
            'installing'                                => __( 'Installing Plugin: %s', 'fabric' ), // %1$s = plugin name
            'oops'                                      => __( 'Something went wrong with the plugin API.', 'fabric' ),
            'notice_can_install_required'               => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
            'notice_can_install_recommended'            => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_install'                     => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
            'notice_can_activate_required'              => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
            'notice_can_activate_recommended'           => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_activate'                    => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
            'notice_ask_to_update'                      => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
            'notice_cannot_update'                      => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
            'install_link'                              => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                             => _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
            'return'                                    => __( 'Return to Required Plugins Installer', 'fabric' ),
            'plugin_activated'                          => __( 'Plugin activated successfully.', 'fabric' ),
            'complete'                                  => __( 'All plugins installed and activated successfully. %s', 'fabric' ), // %1$s = dashboard link
            'nag_type'                                  => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
        )
    );

    tgmpa( $plugins_to_install, $config );
}
add_action( 'tgmpa_register', 'fabric_setup_plugin_notifications' );

