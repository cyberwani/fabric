<?php
/**
 * Theme activation
 */
if (is_admin() && isset($_GET['activated']) && 'themes.php' == $GLOBALS['pagenow']) {
    wp_redirect(admin_url('themes.php?page=theme_activation_options'));
    exit;
}

function fabric_theme_activation_options_init() {
    register_setting(
        'fabric_activation_options',
        'fabric_theme_activation_options'
    );
}
add_action('admin_init', 'fabric_theme_activation_options_init');

function fabric_activation_options_page_capability($capability) {
    return 'edit_theme_options';
}
add_filter('option_page_capability_fabric_activation_options', 'fabric_activation_options_page_capability');

function fabric_theme_activation_options_add_page() {
    $fabric_activation_options = fabric_get_theme_activation_options();

    if (!$fabric_activation_options) {
        $theme_page = add_theme_page(
            __('Theme Activation', 'fabric'),
            __('Theme Activation', 'fabric'),
            'edit_theme_options',
            'theme_activation_options',
            'fabric_theme_activation_options_render_page'
        );
    } else {
        if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'theme_activation_options') {
            flush_rewrite_rules();
            wp_redirect(admin_url('themes.php'));
            exit;
        }
    }
}
add_action('admin_menu', 'fabric_theme_activation_options_add_page', 50);

function fabric_get_theme_activation_options() {
    return get_option('fabric_theme_activation_options');
}

function fabric_theme_activation_options_render_page() {

    require_once FABRIC_THEME_DIR . '/functions/includes/spyc.php';
    $packages = Spyc::YAMLLoad(FABRIC_THEME_DIR . '/functions/includes/packages.yml');
    ?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php printf(__('%s Theme Activation', 'fabric'), wp_get_theme()); ?></h2>
        <?php settings_errors(); ?>

        <form method="post" action="options.php">

            <?php
                settings_fields('fabric_activation_options');
            ?>

            <table class="form-table">

                <tr valign="top"><th scope="row"><?php _e('Choose Packages', 'fabric'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php _e('Choose Packages', 'fabric'); ?></span></legend>
                            <table>
                                <tbody>
                                    <?php
                                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log.txt', print_r($packages, true));
                                    foreach( $packages as $package => $contents)
                                    { ?>
                                    <tr>
                                        <td><input type="checkbox" name="fabric_theme_activation_options[<?php echo $package; ?>]" id="<?php echo $package; ?>" value="true" /></td>
                                        <td><label for="<?php echo $package; ?>"> <?php echo $package; ?></label></td>
                                        <td><span><?php echo $contents['description']; ?></span></td>
                                    </tr>
                                    <?php
                                //echo '<p class="checkbx">';
                                //echo '<input type="checkbox" name="fabric_theme_activation_options['.$package.']" id="'.$package.'" value="true" />';
                                //echo '<label for="'.$package.'"> '.$package.'</label>';
                                //echo '</p>';
                            }
                            ?>
                                </tbody>
                            </table>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top"><th scope="row"><?php _e('Create static front page?', 'fabric'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php _e('Create static front page?', 'fabric'); ?></span></legend>
                            <select name="fabric_theme_activation_options[create_front_page]" id="create_front_page">
                                <option value="true"><?php echo _e('Yes', 'fabric'); ?></option>
                                <option selected="selected" value="false"><?php echo _e('No', 'fabric'); ?></option>
                            </select>
                            <br>
                            <small class="description"><?php printf(__('Create a page called Home and set it to be the static front page', 'fabric')); ?></small>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top"><th scope="row"><?php _e('Change permalink structure?', 'fabric'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php _e('Update permalink structure?', 'fabric'); ?></span></legend>
                            <select name="fabric_theme_activation_options[change_permalink_structure]" id="change_permalink_structure">
                                <option value="true"><?php echo _e('Yes', 'fabric'); ?></option>
                                <option selected="selected" value="false"><?php echo _e('No', 'fabric'); ?></option>
                            </select>
                            <br>
                            <small class="description"><?php printf(__('Change permalink structure to /&#37;postname&#37;/', 'fabric')); ?></small>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top"><th scope="row"><?php _e('Create navigation menu?', 'fabric'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php _e('Create navigation menu?', 'fabric'); ?></span></legend>
                            <select name="fabric_theme_activation_options[create_navigation_menus]" id="create_navigation_menus">
                                <option value="true"><?php echo _e('Yes', 'fabric'); ?></option>
                                <option selected="selected" value="false"><?php echo _e('No', 'fabric'); ?></option>
                            </select>
                            <br>
                            <small class="description"><?php printf(__('Create the Primary Navigation menu and set the location', 'fabric')); ?></small>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top"><th scope="row"><?php _e('Add pages to menu?', 'fabric'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php _e('Add pages to menu?', 'fabric'); ?></span></legend>
                            <select name="fabric_theme_activation_options[add_pages_to_primary_navigation]" id="add_pages_to_primary_navigation">
                                <option value="true"><?php echo _e('Yes', 'fabric'); ?></option>
                                <option selected="selected" value="false"><?php echo _e('No', 'fabric'); ?></option>
                            </select>
                            <br>
                            <small class="description"><?php printf(__('Add all current published pages to the Primary Navigation', 'fabric')); ?></small>
                        </fieldset>
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>
        </form>
    </div>

<?php }

function fabric_theme_activation_action() {

    if (!($fabric_theme_activation_options = fabric_get_theme_activation_options())) {
        return;
    }

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log.txt', print_r($fabric_theme_activation_options, true));

    if (strpos(wp_get_referer(), 'page=theme_activation_options') === false) {
        return;
    }

    require_once FABRIC_THEME_DIR . '/functions/includes/spyc.php';
    $packages = Spyc::YAMLLoad(FABRIC_THEME_DIR . '/functions/includes/packages.yml');

    $plugins_to_install = array();
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log1.txt', print_r($packages, true));
    foreach( $packages as $package => $contents)
    {
        if ($fabric_theme_activation_options[$package] === 'true') {
            $fabric_theme_activation_options[$package] = false;

            foreach( $contents as $plugin => $plugin_info)
            {
                $plugins_to_install[] = basename($plugin_info['url']);
            }
        }
    }

    if( !empty( $plugins_to_install ) ) {
        require_once FABRIC_THEME_DIR . '/functions/includes/plugin-handler.php';
        fabric_install_activate_plugins($plugins_to_install);
    }

    if ($fabric_theme_activation_options['create_front_page'] === 'true') {
        $fabric_theme_activation_options['create_front_page'] = false;

        $default_pages = array('Home');
        $existing_pages = get_pages();
        $temp = array();

        foreach ($existing_pages as $page) {
            $temp[] = $page->post_title;
        }

        $pages_to_create = array_diff($default_pages, $temp);

        foreach ($pages_to_create as $new_page_title) {
            $add_default_pages = array(
                'post_title' => $new_page_title,
                'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum consequat, orci ac laoreet cursus, dolor sem luctus lorem, eget consequat magna felis a magna. Aliquam scelerisque condimentum ante, eget facilisis tortor lobortis in. In interdum venenatis justo eget consequat. Morbi commodo rhoncus mi nec pharetra. Aliquam erat volutpat. Mauris non lorem eu dolor hendrerit dapibus. Mauris mollis nisl quis sapien posuere consectetur. Nullam in sapien at nisi ornare bibendum at ut lectus. Pellentesque ut magna mauris. Nam viverra suscipit ligula, sed accumsan enim placerat nec. Cras vitae metus vel dolor ultrices sagittis. Duis venenatis augue sed risus laoreet congue ac ac leo. Donec fermentum accumsan libero sit amet iaculis. Duis tristique dictum enim, ac fringilla risus bibendum in. Nunc ornare, quam sit amet ultricies gravida, tortor mi malesuada urna, quis commodo dui nibh in lacus. Nunc vel tortor mi. Pellentesque vel urna a arcu adipiscing imperdiet vitae sit amet neque. Integer eu lectus et nunc dictum sagittis. Curabitur commodo vulputate fringilla. Sed eleifend, arcu convallis adipiscing congue, dui turpis commodo magna, et vehicula sapien turpis sit amet nisi.',
                'post_status' => 'publish',
                'post_type' => 'page'
            );

            $result = wp_insert_post($add_default_pages);
        }

        $home = get_page_by_title('Home');
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home->ID);

        $home_menu_order = array(
            'ID' => $home->ID,
            'menu_order' => -1
        );
        wp_update_post($home_menu_order);
    }

    if ($fabric_theme_activation_options['change_permalink_structure'] === 'true') {
        $fabric_theme_activation_options['change_permalink_structure'] = false;

        if (get_option('permalink_structure') !== '/%postname%/') {
            global $wp_rewrite;
            $wp_rewrite->set_permalink_structure('/%postname%/');
            flush_rewrite_rules();
        }
    }

    if ($fabric_theme_activation_options['create_navigation_menus'] === 'true') {
        $fabric_theme_activation_options['create_navigation_menus'] = false;

        $fabric_nav_theme_mod = false;

        $primary_nav = wp_get_nav_menu_object('Primary Navigation');

        if (!$primary_nav) {
            $primary_nav_id = wp_create_nav_menu('Primary Navigation', array('slug' => 'primary_navigation'));
            $fabric_nav_theme_mod['primary_navigation'] = $primary_nav_id;
        } else {
            $fabric_nav_theme_mod['primary_navigation'] = $primary_nav->term_id;
        }

        if ($fabric_nav_theme_mod) {
            set_theme_mod('nav_menu_locations', $fabric_nav_theme_mod);
        }
    }

    if ($fabric_theme_activation_options['add_pages_to_primary_navigation'] === 'true') {
        $fabric_theme_activation_options['add_pages_to_primary_navigation'] = false;

        $primary_nav = wp_get_nav_menu_object('Primary Navigation');
        $primary_nav_term_id = (int) $primary_nav->term_id;
        $menu_items= wp_get_nav_menu_items($primary_nav_term_id);

        if (!$menu_items || empty($menu_items)) {
            $pages = get_pages();
            foreach($pages as $page) {
                $item = array(
                    'menu-item-object-id' => $page->ID,
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish'
                );
                wp_update_nav_menu_item($primary_nav_term_id, 0, $item);
            }
        }
    }

    update_option('fabric_theme_activation_options', $fabric_theme_activation_options);
}
add_action('admin_init','fabric_theme_activation_action');

function fabric_deactivation() {
    delete_option('fabric_theme_activation_options');
}
add_action('switch_theme', 'fabric_deactivation');



