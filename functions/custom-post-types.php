<?php

if ( !defined('FABRIC_CPT_DIR') ){
    define('FABRIC_CPT_DIR', dirname(__FILE__) . '/custom-post-types/');
}

// Include all Custom Post Types
if ($cpt_handle = opendir(FABRIC_CPT_DIR)) {
    while (false !== ($cpt = readdir($cpt_handle))) {
		$ext = pathinfo($cpt, PATHINFO_EXTENSION);
		if($ext == 'php') {
			require_once ( FABRIC_CPT_DIR . $cpt );
		}
    }
    closedir($cpt_handle);
}