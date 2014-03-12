<?php
/**
 * =======================================
 * Fabric Include Post Types
 * =======================================
 *
 * 
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 */

if ( ! defined('FABRIC_CPT_DIR') ) {
	define( 'FABRIC_CPT_DIR', FABRIC_THEME_DIR . 'lib/post-types/' );
}

class Fabric_Include_Post_Types
{

	public function init()
	{
		$this->check_create_dir();
		$this->include_all_post_types();
	}

	private function check_create_dir()
	{
		if ( ! is_dir( FABRIC_CPT_DIR ) && is_writable( dirname( __FILE__ ) ) ) {
			mkdir( FABRIC_CPT_DIR, 0755 );
		}
	}

	private function include_all_post_types()
	{
		if ( is_dir( FABRIC_CPT_DIR ) && $cpt_handle = opendir( FABRIC_CPT_DIR ) ) {
			while ( false !== ( $cpt = readdir( $cpt_handle ) ) ) {
				$ext = pathinfo( $cpt, PATHINFO_EXTENSION );
				if ( 'php' == $ext ) {
					include ( FABRIC_CPT_DIR . $cpt );
				}
			}
			closedir( $cpt_handle );
		}
	}

}

add_action( 'fabric_loaded', array( new Fabric_Include_Post_Types, 'init' ) );
