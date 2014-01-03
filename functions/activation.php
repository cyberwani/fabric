<?php
/**
 * Theme activation
 */

if ( !defined('FABRIC_ACTIVATION_DIR') ){
    define('FABRIC_ACTIVATION_DIR', dirname(__FILE__) . '/activation/');
}

if (is_admin() && isset($_GET['activated']) && 'themes.php' == $GLOBALS['pagenow']) {

    fabric_activation();

    wp_redirect(admin_url('customize.php'));
    exit;
}

function fabric_activation() {

    $plugin_src = file_get_contents(FABRIC_ACTIVATION_DIR . 'includes/fabric-template-redirection-template.php');

    if( !is_dir(WP_CONTENT_DIR . '/mu-plugins') )
        mkdir(WP_CONTENT_DIR . '/mu-plugins', 0755);

    $write_result = file_put_contents(WP_CONTENT_DIR . '/mu-plugins/fabric-template-redirection.php', $plugin_src);

    if( false == $write_result )
        update_option( 'fabric_template_redirection_installed', 0 );

}

function fabric_deactivation() {

    // Remove Template Redirection
    if( file_exists( WP_CONTENT_DIR . '/mu-plugins/fabric-template-redirection.php' ) )
        unlink(WP_CONTENT_DIR . '/mu-plugins/fabric-template-redirection.php');

}
add_action('switch_theme', 'fabric_deactivation');

function fabric_customize($wp_customize) {

    /*
        Plugin Packages
    */
    $wp_customize->add_section( 'fabric_plugin_packages' , array(
        'title'      => __('Plugin Packages', 'fabric'),
        'priority'   => 30,
    ) );
    $wp_customize->add_setting( 'fabric-packages', array( 'default' => '', 'type' => 'option', 'transport' => 'refresh', 'capability' => 'edit_theme_options' ));
    $wp_customize->add_control(
        new Fabric_Plugin_Packages(
            $wp_customize,
            'fabric-packages',
            array(
                'label'          => __( 'Package Name', 'fabric' ),
                'section'        => 'fabric_plugin_packages',
                'settings'       => 'fabric-packages'
            )
        ) 
    );

}
add_action( 'customize_register', 'fabric_customize' );

if (class_exists('WP_Customize_Control')) {
    class Fabric_Plugin_Packages extends WP_Customize_Control {
        public $type = 'fabric-packages';
    
        public function render_content()
        {
            $packages = fabric_get_packages();
            ?>
            <script src="<?php echo get_template_directory_uri(); ?>/functions/activation/includes/theme-customizer.js"></script>
            <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/functions/activation/includes/theme-customizer.css" type="text/css">

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
}

function fabric_read_package_ajax() {

    $package = $_POST['fabric_package'];
    require_once FABRIC_ACTIVATION_DIR . 'includes/spyc.php';
    $packages = Spyc::YAMLLoad(FABRIC_ACTIVATION_DIR . 'packages/' . $package);

    echo json_encode($packages);
    exit;
}
add_action('wp_ajax_fabric_read_package_ajax', 'fabric_read_package_ajax');

function fabric_read_package($package) {
    require_once FABRIC_ACTIVATION_DIR . 'includes/spyc.php';
    $packages = Spyc::YAMLLoad(FABRIC_ACTIVATION_DIR . 'packages/' . $package);

    return $packages;
}

function fabric_get_packages() {
    $packages = array();

    if ($handle = opendir(FABRIC_ACTIVATION_DIR . 'packages/')) {
        while (false !== ($entry = readdir($handle))) {
            $ext = pathinfo($entry, PATHINFO_EXTENSION);
            if($ext == 'yml') {
                $packages[] = "$entry";
            }
        }
        closedir($handle);
    }

    return $packages;
}

require_once FABRIC_ACTIVATION_DIR . '/includes/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'fabric_after_save_customizer' );

function fabric_after_save_customizer() {

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

