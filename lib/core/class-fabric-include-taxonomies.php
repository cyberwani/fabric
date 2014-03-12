<?php
/**
 * =======================================
 * Fabric Include Taxonomies
 * =======================================
 *
 * 
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 */

if ( ! defined('FABRIC_TAX_DIR') ){
	define( 'FABRIC_TAX_DIR', FABRIC_THEME_DIR . 'lib/taxonomies/' );
}

class Fabric_Include_Taxonomies
{

	public function init()
	{
		$this->check_create_dir();
		$this->include_all_taxonomies();
	}

	private function check_create_dir()
	{
		if ( ! is_dir( FABRIC_TAX_DIR ) && is_writable( dirname( __FILE__ ) ) ) {
			mkdir( FABRIC_TAX_DIR, 0755 );
		}
	}

	private function include_all_taxonomies()
	{
		if ( is_dir( FABRIC_TAX_DIR ) && $tax_handle = opendir( FABRIC_TAX_DIR ) ) {
			while ( false !== ( $tax = readdir( $tax_handle ) ) ) {
				$ext = pathinfo( $tax, PATHINFO_EXTENSION );
				if ( 'php' == $ext ) {
					include ( FABRIC_TAX_DIR . $tax );
				}
			}
			closedir( $tax_handle );
		}
	}

}

add_action( 'fabric_loaded', array( new Fabric_Include_Taxonomies, 'init' ) );
