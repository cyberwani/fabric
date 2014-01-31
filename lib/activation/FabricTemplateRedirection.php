<?php
/**
 * =======================================
 * Fabric Template Redirection
 * =======================================
 *
 * 
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 */

if ( !defined('FABRIC_TEMPLATE_REDIRECTION_ACTIVE') ){
	define('FABRIC_TEMPLATE_REDIRECTION_ACTIVE', true);
}

class FabricTemplateRedirection
{

	public function init()
	{
		add_filter( 'template_directory', array( $this, 'redirect_template_directory' ), 10, 3 );
		add_filter( 'validate_current_theme', array( $this, 'disable_theme_validation' ) );
	}

	public function redirect_template_directory($template_dir, $template, $theme_root)
	{
	    $new_template_dir = $template_dir . '/views';

	    if( !is_dir( $new_template_dir ) || !file_exists( $new_template_dir . '/index.php' ) )
	    	return $template_dir;

	    return $new_template_dir;
	}

	public function disable_theme_validation()
	{
	    $templates_dir = basename( get_template_directory() );

	    if( "views" != $templates_dir )
	    	return true;

		return false;
	}

}

add_action( 'muplugins_loaded', array( new FabricTemplateRedirection, 'init' ) );
