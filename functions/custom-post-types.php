<?php

if ( !defined('FABRIC_CPT_DIR') ){
    define('FABRIC_CPT_DIR', dirname(__FILE__) . '/custom-post-types/');
}

if( !is_dir( FABRIC_CPT_DIR ) && is_writable( dirname(__FILE__) ) ) {
	mkdir( FABRIC_CPT_DIR, 0644 );
}

// Include all Custom Post Types
if ( is_dir( FABRIC_CPT_DIR ) && $cpt_handle = opendir( FABRIC_CPT_DIR ) ) {
    while (false !== ($cpt = readdir($cpt_handle))) {
		$ext = pathinfo($cpt, PATHINFO_EXTENSION);
		if($ext == 'php') {
			include ( FABRIC_CPT_DIR . $cpt );
		}
    }
    closedir($cpt_handle);
}