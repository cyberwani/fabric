<?php

//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log.txt', print_r('', true));
//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log1.txt', print_r('', true));


function fabric_template_path() {
	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log.txt', print_r(Fabric_Wrapping::$main_template."\r\n", true), FILE_APPEND);
	return Fabric_Wrapping::$main_template;
}
 
class Fabric_Wrapping {
 
	/**
	 * Stores the full path to the main template file
	 */
	static $main_template;
 
	/**
	 * Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
	 */
	static $base;
 
	static function wrap( $template ) {
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/log1.txt', print_r($template."\r\n", true), FILE_APPEND);
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
 
add_filter( 'template_include', array( 'Fabric_Wrapping', 'wrap' ), 99 );