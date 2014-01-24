<?php

# Based on http://wpengineer.com/2513/filename-cache-busting-wordpress/ by DOMINIK

function fabric_rewrite_asset_query_var($content) {
	
	global $wp_rewrite;
	
	$fabric_new_non_wp_rules = array(
		'(.+)\.(.+)\.(js|css)$' => '$1.$3 [L]'
	);
	
	$wp_rewrite->non_wp_rules = array_merge($wp_rewrite->non_wp_rules, $fabric_new_non_wp_rules);
	
	return $content;
}
add_action('generate_rewrite_rules', 'fabric_rewrite_asset_query_var');

function fabric_move_asset_version( $src ) {
	// Don't touch admin scripts
	if ( is_admin() )
		return $src;
	
	return preg_replace(
		'/\.(js|css)\?ver=(.+)$/',
		'.$2.$1',
		$src
	);
}
add_filter( 'script_loader_src', 'fabric_move_asset_version' );
add_filter( 'style_loader_src', 'fabric_move_asset_version' );