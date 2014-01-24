<?php

function register_cpt_product() {

	$product_args = array(
		'label' 				=> __('Products', 'fabric'),
		'labels'				=> array(
				'singular_name' => __('Product', 'fabric')
			),
		'public'				=> true,
		'exclude_from_search'	=> false,
		'show_ui'				=> true,
		'show_in_nav_menus'		=> true,
		'has_archive'			=> true,
		'supports'				=> array('title', 'editor'),
		'taxonomies'			=> array(),
		'rewrite'				=> array(
				'slug'	=> __('products', 'fabric')
			),
		'query_var'				=> 'cpt_product'
	);

	register_post_type( 'product', $product_args );
}
add_action('init', 'register_cpt_product');