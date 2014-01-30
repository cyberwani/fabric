<?php
/**
 * =======================================
 * Fabric Auto Enqueue
 * =======================================
 *
 * 
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 */

if ( !defined('FABRIC_JS_ASSETS_DIR') ){
    define('FABRIC_JS_ASSETS_DIR', FABRIC_THEME_DIR . 'assets/js/');
}
if ( !defined('FABRIC_JS_ASSETS_PUBLIC_DIR') ){
    define('FABRIC_JS_ASSETS_PUBLIC_DIR', get_bloginfo('template_directory') . '/assets/js/');
}
if ( !defined('FABRIC_CSS_ASSETS_DIR') ){
    define('FABRIC_CSS_ASSETS_DIR', FABRIC_THEME_DIR . 'assets/css/');
}
if ( !defined('FABRIC_CSS_ASSETS_PUBLIC_DIR') ){
    define('FABRIC_CSS_ASSETS_PUBLIC_DIR', get_bloginfo('template_directory') . '/assets/css/');
}

class FabricAutoEnqueue
{

	public function init()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );
		add_action( 'wp_footer', array( $this, 'enqueue_css' ) );

		if (is_single() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}

	public function enqueue_js() {

		$scripts = $this->find_assets( 'js', FABRIC_JS_ASSETS_PUBLIC_DIR, FABRIC_JS_ASSETS_DIR );

		if( !$scripts )
			return;

		foreach( $scripts as $script )
		{
			wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
		}
	}

	public function enqueue_css() {

		$styles = $this->find_assets( 'css', FABRIC_CSS_ASSETS_PUBLIC_DIR, FABRIC_CSS_ASSETS_DIR );

		if( !$styles )
			return;

		foreach( $styles as $style )
		{
			if( $style['in_footer'] && !did_action( 'wp_print_styles' ) )
				continue;
			wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver'] );
		}
	}

	private function find_assets( $ext, $public_path, $local_path ) {

		$assets_to_enqueue = array();

		if ($assets_handle = opendir($local_path)) {
		    while (false !== ($file = readdir($assets_handle))) {
				$fileinfo = pathinfo($file);
				if(isset($fileinfo['extension']) && $fileinfo['extension'] == $ext) {
					$asset = $this->maybe_enqueue_asset( $file, $fileinfo, $public_path, $local_path );
					
					if( $asset )
						$assets_to_enqueue[] = $asset;
				}
		    }
		    closedir($assets_handle);
		}

		if( !empty( $assets_to_enqueue ) )
			return $assets_to_enqueue;

		return false;
	}

	private function maybe_enqueue_asset( $asset, $fileinfo, $public_path, $local_path ) {

		$header_match =  preg_match( '/^header_/', $asset );
		$footer_match =  preg_match( '/^footer_/', $asset );

		if( !$header_match && !$footer_match )
			return false;

		$dependencies = explode( '+', $fileinfo['filename'] );
		$handle = array_shift( $dependencies );

		$handle = preg_replace( '/^header_|^footer_/', '', $handle );

		return array(
			'handle' 	=> $handle,
			'src' 		=> $public_path . $asset,
			'deps'		=> $dependencies,
			'ver'		=> filemtime( $local_path . $asset ),
			'in_footer'	=> $footer_match
		);
	}

}

add_action( 'fabric_loaded', array( new FabricAutoEnqueue, 'init' ) );
