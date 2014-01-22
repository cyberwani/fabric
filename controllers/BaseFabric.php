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

	public function page_type(  )
	{
		if( is_category() )
			return 'category';

		if( is_archive() )
			return 'archive';

		if( is_404() )
			return '404';

		$post_type = get_post_type();
		
		if( !empty( $post_type ) )
			return $post_type;
	}

	public function the_head( $name = null )
	{
		return $this->get_template( 'head', $name );
	}

	public function the_header( $name = null )
	{
		return $this->get_template( 'header', $name );
	}

	public function the_sidebar( $name = null )
	{
		return $this->get_template( 'sidebar', $name );
	}

	public function the_footer( $name = null )
	{
		return $this->get_template( 'footer', $name );
	}

	private function get_template( $type, $name )
	{
		do_action( "get_{$type}", $name );

		$templates = array();
		$name = (string) $name;
		if ( '' !== $name )
			$templates[] = "{$type}-{$name}.php";

		$templates[] = "{$type}.php";

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