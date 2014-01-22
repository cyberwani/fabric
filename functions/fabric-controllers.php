<?php

function fabric_init() {
	
	$fabric_init = new Fabric\Controllers\InitFabric;
}
add_action( 'fabric_loaded', 'fabric_init' );

function fabric_initiate_controller( $wp_obj ) {

	global $view;
	
	$view = fabric_controller( $wp_obj );
}
add_action( 'parse_query', 'fabric_initiate_controller' );

// Calculate and include the right controller
function fabric_controller( $wp_obj ) {

	$post_type    = isset( $wp_obj->query['post_type'] )	? fabric_format_slug( $wp_obj->query['post_type'] ) 	: false;
	$page_slug 	  = isset( $wp_obj->query['pagename'] ) 	? fabric_format_slug( $wp_obj->query['pagename'] ) 	  	: false;
	$post_slug 	  = isset( $wp_obj->query['name'] ) 		? fabric_format_slug( $wp_obj->query['name'] ) 		  	: false;
	$category	  = ( $wp_obj->is_category ) 				? fabric_format_slug( $wp_obj->query['category_name'] ) : false;

	if( !$post_slug && $page_slug) $post_slug = $page_slug;

	$controllers = array();
	if( $post_type && $post_slug ) {
		$controllers['post_slug'] = array(
			'file' 		=> $post_type . 'Fabric' . $post_slug,
			'namespace' => '\Fabric\Controllers\\' . $post_type . 'Fabric' . $post_slug
		);
	}
	if( $post_type ) {
		$controllers['post_type'] = array(
			'file' 		=> $post_type . 'Fabric',
			'namespace' => '\Fabric\Controllers\\' . $post_type . 'Fabric'
		);
	}
	if( $category ) {
		$controllers['cat_slug']  = array(
			'file' 		=> 'CategoryFabric' . $category,
			'namespace' => '\Fabric\Controllers\CategoryFabric' . $category
		);
		$controllers['category']  = array(
			'file' 		=> 'CategoryFabric',
			'namespace' => '\Fabric\Controllers\CategoryFabric'
		);
	}
	$controllers['base'] 	  = array(
		'file' 		=> 'BaseFabric',
		'namespace' => '\Fabric\Controllers\BaseFabric'
	);

	$controllers = apply_filters( 'fabric_controllers', $controllers, $post_type, $post_slug, $category );

	$located_controller = fabric_locate_controller( $controllers );

	if( isset( $located_controller['namespace'] ) ) {
		
		if ( !defined('FABRIC_CONTROLLER') ){
			define( 'FABRIC_CONTROLLER', $located_controller['file'] );
		}
		return new $located_controller['namespace'];
	}
}

function fabric_locate_controller( $controller_names ) {

	$located = '';
	$category_controller_types = array( 'cat_slug' => 1, 'category' => 1 );

	foreach ( (array) $controller_names as $controller_type => $controller_name )
	{
		if ( !isset( $controller_name['file'] ) )
			continue;
		if ( !is_category() && isset( $category_controller_types[ $controller_type ] ) )
			continue;
		if ( file_exists(FABRIC_CONTROLLERS . $controller_name['file'] . '.php')) {
			$located = $controller_name;
			break;
		}
	}

	return $located;	
}

function fabric_format_slug( $slug = false ) {
 
	if( empty( $slug ) )
		$slug = basename( get_permalink() );
 
	$delimeter = array( '-', '_' );
	$slug_parts = explode( $delimeter[0], str_replace($delimeter, $delimeter[0], $slug ) );
	foreach( $slug_parts as $key => $part )
	{
		$slug_parts[$key] = ucfirst($part);
	}
 
	$formatted_slug = implode('', $slug_parts);
 
	if( empty( $formatted_slug ) ) {
		if( is_home() || is_front_page() ) {
			$formatted_slug = 'Home';
		}
	}
 
	return $formatted_slug;
}

// Register PHP autoloader to include classes for us when called
function fabric_autoloader($className)
{
    $classNameParts = explode('\\', trim($className, '\\'));

    if($classNameParts[0] != 'Fabric')
        return;

    $fileName = array_pop($classNameParts);

    include_once FABRIC_CONTROLLERS . $fileName . '.php';
}
spl_autoload_register('fabric_autoloader');