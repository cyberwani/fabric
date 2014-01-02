<?php
/**
 * =======================================
 * Base Controller
 * =======================================
 *
 * 
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 */

namespace Fabric\Controllers;

class BaseFabric
{

	public function __construct()
	{

	}

	public function the_head( $name = null )
	{
		do_action( 'get_head', $name );

		$templates = array();
		$name = (string) $name;
		if ( '' !== $name )
			$templates[] = "head-{$name}.php";

		$templates[] = 'head.php';

		return locate_template($templates, false);
	}

	public function the_header( $name = null )
	{
		do_action( 'get_header', $name );

		$templates = array();
		$name = (string) $name;
		if ( '' !== $name )
			$templates[] = "header-{$name}.php";

		$templates[] = 'header.php';

		return locate_template($templates, false);
	}

	public function the_sidebar( $name = null )
	{
		do_action( 'get_sidebar', $name );

		$templates = array();
		$name = (string) $name;
		if ( '' !== $name )
			$templates[] = "sidebar-{$name}.php";

		$templates[] = 'sidebar.php';

		return locate_template($templates, false);
	}

	public function the_footer( $name = null )
	{
		do_action( 'get_footer', $name );

		$templates = array();
		$name = (string) $name;
		if ( '' !== $name )
			$templates[] = "footer-{$name}.php";

		$templates[] = 'footer.php';

		return locate_template($templates, false);
	}

	public function loop( $post_type, $additional_args = array() )
	{
		$post_type = explode(',', $post_type);

		$args = array( 'post_type' => $post_type );
		$merged_args = array_merge($args, $additional_args);

		$loop = new \WP_Query( $merged_args );
		if( !empty($loop->posts) ) {
			return new FabricLoopIterator( $loop );
		} else {
			return array();
		}
	}
}