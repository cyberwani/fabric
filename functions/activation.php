<?php
/**
 * Theme activation
 */

if ( !defined('FABRIC_ACTIVATION_DIR') ){
    define('FABRIC_ACTIVATION_DIR', dirname(__FILE__) . '/activation/');
}
if ( !defined('FABRIC_PACKAGES_DIR') ){
    define('FABRIC_PACKAGES_DIR', dirname(__FILE__) . '/activation/packages/');
}

require_once FABRIC_ACTIVATION_DIR . '/includes/helper.php';

if( is_admin() && get_option( 'fabric-plugin-notifications' ) ) {
    require_once FABRIC_ACTIVATION_DIR . '/includes/tgmpa-plugin-notifications.php';
    require_once FABRIC_ACTIVATION_DIR . '/includes/class-tgm-plugin-activation.php';
}

if (is_admin() && isset($_GET['activated']) && 'themes.php' == $GLOBALS['pagenow']) {

    fabric_activation();

    wp_redirect( admin_url( 'customize.php' ) );
    exit;
}

function fabric_customize($wp_customize) {

    $wp_customize->remove_section('nav');
    $wp_customize->remove_section('title_tagline');

    $wp_customize->add_section( 'fabric_plugin_packages' , array(
        'title'         => __('Plugin Packages', 'fabric'),
        'description'   => __('Packages will be installed after clicking "Save" above. Please allow time for plugins to be downloaded and installed.', 'fabric'),
        'priority'      => 20
    ) );
    $wp_customize->add_setting( 'fabric-packages', array( 'default' => '', 'type' => 'option', 'transport' => 'postMessage', 'capability' => 'edit_theme_options' ));
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
    $wp_customize->add_setting( 'fabric-plugin-notifications', array( 'default' => true, 'type' => 'option', 'transport' => 'postMessage', 'capability' => 'edit_theme_options' ));
    $wp_customize->add_control(
        new WP_Customize_Control_With_Description(
            $wp_customize,
            'fabric-plugin-notifications',
            array(
                'label'          => __( 'Notify admin about plugins?', 'painter-theme' ),
                'section'        => 'fabric_plugin_packages',
                'settings'       => 'fabric-plugin-notifications',
                'type'           => 'checkbox',
                'description'    => 'Show alert in admin area when required plugins are uninstalled or deactivated.',
                'priority'       => 100
            )
        ) 
    );

    $wp_customize->get_section('static_front_page')->title = __( 'Permalinks & Static Home Page' );
    $wp_customize->get_section('static_front_page')->description = '';
    $wp_customize->get_section('static_front_page')->priority = 30;
    $wp_customize->add_setting( 'fabric-permalinks', array( 'default' => true, 'type' => 'option', 'transport' => 'postMessage', 'capability' => 'edit_theme_options' ));
    $wp_customize->add_control(
        new WP_Customize_Control_With_Description(
            $wp_customize,
            'fabric-permalinks',
            array(
                'label'          => __( 'Change permalink structure?', 'painter-theme' ),
                'section'        => 'static_front_page',
                'settings'       => 'fabric-permalinks',
                'type'           => 'checkbox',
                'description'    => 'Change permalinks to: /%postname%/',
                'priority'       => 0
            )
        ) 
    );

    $wp_customize->add_section( 'fabric_nav_menu' , array(
        'title'         => __('Create and Set Navigation Menu', 'fabric'),
        'priority'      => 40
    ) );
    $wp_customize->add_setting( 'fabric-nav-menu', array( 'default' => true, 'type' => 'option', 'transport' => 'postMessage', 'capability' => 'edit_theme_options' ));
    $wp_customize->add_control(
        new WP_Customize_Control_With_Description(
            $wp_customize,
            'fabric-nav-menu',
            array(
                'label'          => __( 'Create "Primary Navigation" menu', 'painter-theme' ),
                'section'        => 'fabric_nav_menu',
                'settings'       => 'fabric-nav-menu',
                'type'           => 'checkbox',
                'description'    => 'Create the Primary Navigation menu and set the location'
            )
        ) 
    );
}
add_action( 'customize_register', 'fabric_customize' );

function fabric_after_save_customizer() {

    // Get options from customizer
    $customizer_options = json_decode( wp_unslash( $_POST['customized'] ) );

    // Install any choosen packages
    fabric_install_packages( $customizer_options );

    // Set Permalinks
    if( $customizer_options->{'fabric-permalinks'} ) {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
        flush_rewrite_rules();
        update_option( 'fabric-permalinks', false );
    }

    // Create and Set Navigation Menu
    if( $customizer_options->{'fabric-nav-menu'} ) {
        
        $fabric_nav_theme_mod = false;
        $primary_nav = wp_get_nav_menu_object( 'Primary Navigation' );

        if (!$primary_nav) {
            $primary_nav_id = wp_create_nav_menu('Primary Navigation', array('slug' => 'primary_navigation'));
            $fabric_nav_theme_mod['primary_navigation'] = $primary_nav_id;
        } else {
            $fabric_nav_theme_mod['primary_navigation'] = $primary_nav->term_id;
        }

        if ($fabric_nav_theme_mod) {
            set_theme_mod('nav_menu_locations', $fabric_nav_theme_mod);
        }
        update_option( 'fabric-nav-menu', false );
    }
}
add_action( 'customize_save_after', 'fabric_after_save_customizer' );

