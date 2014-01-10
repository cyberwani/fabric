<?php

// Calculate and include the right controller
function fabric_controller() {

	$post_type 		= ucfirst( get_post_type() );
	$post_slug 		= fabric_format_slug();
	$category_info 	= ( is_category() ) ? get_category( get_query_var( 'cat' ) ) : false;
	$category 		= ( !empty( $category_info ) ) ? fabric_format_slug( $category_info->slug ) : '';

	$controllers = array();
	$controllers['post_slug'] = array(
		'file' 		=> $post_type . 'Fabric' . $post_slug,
		'namespace' => '\Fabric\Controllers\\' . $post_type . 'Fabric' . $post_slug
	);
	$controllers['post_type'] = array(
		'file' 		=> $post_type . 'Fabric',
		'namespace' => '\Fabric\Controllers\\' . $post_type . 'Fabric'
	);
	$controllers['cat_slug']  = array(
		'file' 		=> 'CategoryFabric' . $category,
		'namespace' => '\Fabric\Controllers\CategoryFabric' . $category
	);
	$controllers['category']  = array(
		'file' 		=> 'CategoryFabric',
		'namespace' => '\Fabric\Controllers\CategoryFabric'
	);
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

	$slug_parts = explode('-', $slug);
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