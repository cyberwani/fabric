<?php
/**
 * =======================================
 * Fabric Template Wrapper
 * =======================================
 *
 * 
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 * Based on http://scribu.net/wordpress/theme-wrappers.html by Scribu
 */

function fabric_template_path() {
	return FabricTemplateWrapper::$main_template;
}
 
class FabricTemplateWrapper {
 
	/**
	 * Stores the full path to the main template file
	 */
	static $main_template;
 
	/**
	 * Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
	 */
	static $base;
 
	static function wrap( $template ) {

		self::$main_template = $template;

		self::$base = substr( basename( self::$main_template ), 0, -4 );

		if ( 'index' == self::$base )
			self::$base = false;
 
		$templates = array( 'views/wrapper.php' );
 
		if ( self::$base )
			array_unshift( $templates, sprintf( 'views/wrapper-%s.php', self::$base ) );

		return locate_template( $templates );
	}
}
add_filter( 'template_include', array( 'FabricTemplateWrapper', 'wrap' ), 99 );