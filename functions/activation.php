<?php
/**
 * Theme activation
 */

require_once FABRIC_THEME_DIR . 'functions/models/activation.php';

function fabric_theme_activation_options_render_page() {

    $packages = fabric_get_packages();
    ?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php printf(__('%s Theme Activation', 'fabric'), wp_get_theme()); ?></h2>
        <?php settings_errors(); ?>

        <form method="post" action="options.php">

            <?php
                settings_fields('fabric_activation_options');
            ?>
            <style type="text/css">
                table#packages {
                    border-collapse: collapse;
                }
                table#packages tr.sub {
                    display: none;
                    background: #ddd;
                }
                table#packages tr.sub:nth-child(even) {
                    background: #eee;
                }
                table#packages tr td {
                    border-left: 1px solid #ccc;
                }
            </style>
            <script type="text/javascript">
                packages = '';
                (function($) {
                    $(document).ready( function() {
                        $(document).on( 'click', 'input.package', function() {
                            if($(this).prop('checked')) {
                                var package_slug = $(this).attr('data-package');
                                $('tr.' + package_slug).css('display', 'table-row');
                                $('tr.' + package_slug).find('input[type="checkbox"]').prop('checked', true);
                            } else {
                                var package_slug = $(this).attr('data-package');
                                $('tr.' + package_slug).css('display', 'none');
                                $('tr.' + package_slug).find('input[type="checkbox"]').prop('checked', false);
                            }
                        });

                        $('#choose_a_package').on('change', function() {
                            console.log($(this).val());
                            $.post(
                                ajaxurl,
                                {
                                    // wp ajax action
                                    action : 'fabric_read_package_ajax',

                                    // send the nonce along with the request
                                    fabric_package : $(this).val()
                                },
                                function( response ) {
                                    packages = buildConfigurator(response);
                                    $('table#packages').html(packages);
                                }
                            );
                        });
                    });

                    function buildConfigurator(packages) {
                        packages = JSON.parse(packages);
                        var html = '<tbody>';

                        $.each(packages, function(package_name, package_details) {
                            var package_slug = convertToSlug(package_name);
                            html += '<tr>';
                                html += '<td>';
                                    html += '<input type="checkbox" class="package" data-package="' + package_slug + '" name="fabric_theme_activation_options[' + package_name + ']" id="' + package_name + '" value="true" />';
                                html += '</td>';
                                html += '<td>';
                                    html += '<label for="' + package_name + '"> ' + package_name + '</label>';
                                html += '</td>';
                                html += '<td>';
                                    html += '<span>' + this.description + '</span>';
                                html += '</td>';
                            html += '</tr>';
                            
                            $.each(this.plugins, function(plugin_name, plugin_details) {
                                html += '<tr class="sub ' + package_slug + '">';
                                    html += '<td>';
                                        html += '<input type="checkbox" class="plugin" name="fabric_theme_activation_options[' + plugin_name + ']" id="' + plugin_name + '" value="true" />';
                                    html += '</td>';
                                    html += '<td>';
                                        html += '<span><a href="' + this.url + '" target="_blank">' + plugin_name + '</a></span>';
                                    html += '</td>';
                                    html += '<td>';
                                        html += '<span>' + this.description + '</span>';
                                    html += '</td>';
                                html += '</tr>';
                            });
                        });

                        html += '</tbody>';

                        return html;

                    }
                    function convertToSlug(Text)
                    {
                        return Text
                            .toLowerCase()
                            .replace(/ /g,'-')
                            .replace(/[^\w-]+/g,'')
                            ;
                    }

                })(jQuery);
            </script>
            <table class="form-table">

                <tr valign="top"><th scope="row"><?php _e('Choose a Package', 'fabric'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php _e('Choose a Package', 'fabric'); ?></span></legend>
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
                            <br>
                            <small class="description"><?php printf(__('Choose a preconfigured installation package', 'fabric')); ?></small>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top"><th scope="row"><?php _e('Choose Packages', 'fabric'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php _e('Choose Packages', 'fabric'); ?></span></legend>
                            <table id="packages">
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

<?php 
}
