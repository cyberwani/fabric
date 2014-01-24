<?php

if ( !defined('FABRIC_TAX_DIR') ){
    define('FABRIC_TAX_DIR', dirname(__FILE__) . '/custom-taxonomies/');
}

if( !is_dir( FABRIC_TAX_DIR ) && is_writable( dirname(__FILE__) ) ) {
	mkdir( FABRIC_TAX_DIR, 0755 );
}

// Include all Custom Taxonomies
if ( is_dir( FABRIC_TAX_DIR ) && $tax_handle = opendir( FABRIC_TAX_DIR ) ) {
    while (false !== ($tax = readdir($tax_handle))) {
		$ext = pathinfo($tax, PATHINFO_EXTENSION);
		if($ext == 'php') {
			include ( FABRIC_TAX_DIR . $tax );
		}
    }
    closedir($tax_handle);
}