<?php
/**
 * Sitemap
 */

// Add Admin Menu Page
add_action('admin_menu', 'fabric_sitemap_menu');
function fabric_sitemap_menu() {
	add_menu_page('Sitemap', 'Sitemap', 'publish_posts', 'fabric-sitemap', 'fabric_sitemap_page');
}

function fabric_sitemap_page() {
	wp_enqueue_script("jOrgChart", get_template_directory_uri()."/functions/includes/assets/javascripts/jquery.jOrgChart.js",array("jquery","jquery-ui-core"));
	wp_enqueue_style("jOrgChart", get_template_directory_uri()."/functions/includes/assets/css/jOrgChart.css");

	include "includes/sitemap/main.php";
}