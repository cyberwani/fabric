<?php
/**
 * =======================================
 * Fabric Controller Autoloader & Wrapper Functions
 * =======================================
 *
 * 
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 */

namespace Fabric\Controllers;

class FabricController
{
	public function __construct()
	{
		spl_autoload_register( array( $this, 'fabric_autoloader' ) );
	}

	public function load_controller()
	{
		global $view;

		$controller = 'Fabric\Controllers\\' . $this->controller_hierarchy();

		$view = new $controller;
	}

	public function init_controller()
	{
		$init_controller = new Init;
	}

	public function get_header( $name = null )
	{
		return $this->get_template( 'header', $name, $this );
	}

	public function get_sidebar( $name = null )
	{
		return $this->get_template( 'sidebar', $name, $this );
	}

	public function get_footer( $name = null )
	{
		return $this->get_template( 'footer', $name, $this );
	}

	public function get_template_part( $slug, $name = null )
	{
		return $this->get_template( $slug, $name, $this, true );
	}

	private function get_template( $type, $name, &$view, $template_part = false )
	{
		if( $template_part ) {
			do_action( "get_template_part_{$type}", $type, $name );
		} else {
			do_action( "get_{$type}", $name );
		}

		$templates = array();
		$name = (string) $name;
		if ( '' !== $name )
			$templates[] = "{$type}-{$name}.php";

		$templates[] = "{$type}.php";

		include locate_template($templates, false);
	}

	private function controller_hierarchy()
	{
		$controller = false;
		if     ( is_404()				&& $controller = $this->get_404_controller()				) :
		elseif ( is_search()			&& $controller = $this->get_search_controller()				) :
		elseif ( is_front_page()		&& $controller = $this->get_home_controller()				) :
		elseif ( is_home()				&& $controller = $this->get_home_controller()				) :
		elseif ( is_post_type_archive()	&& $controller = $this->get_post_type_archive_controller()	) :
		elseif ( is_tax()				&& $controller = $this->get_taxonomy_controller()			) :
		elseif ( is_attachment()		&& $controller = $this->get_attachment_controller()			) :
		elseif ( is_single()			&& $controller = $this->get_single_controller()				) :
		elseif ( is_page()				&& $controller = $this->get_page_controller()				) :
		elseif ( is_category()			&& $controller = $this->get_category_controller()			) :
		elseif ( is_tag()				&& $controller = $this->get_tag_controller()				) :
		elseif ( is_author()			&& $controller = $this->get_author_controller()				) :
		elseif ( is_date()				&& $controller = $this->get_date_controller()				) :
		elseif ( is_archive()			&& $controller = $this->get_archive_controller()			) :
		elseif ( is_comments_popup()	&& $controller = $this->get_comments_popup_controller()		) :
		elseif ( is_paged()				&& $controller = $this->get_paged_controller()				) :
		else :
			$controller = $this->get_base_controller();
		endif;

		if ( !defined('FABRIC_CONTROLLER') ){
			define( 'FABRIC_CONTROLLER', $controller );
		}

		return $controller;
	}

	private function get_404_controller()
	{
		return $this->get_query_controller('Error');
	}

	private function get_search_controller()
	{
		return $this->get_query_controller('Search');
	}

	private function get_home_controller()
	{
		return $this->get_query_controller('Home');
	}

	private function get_post_type_archive_controller()
	{
		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) )
			$post_type = reset( $post_type );

		$obj = get_post_type_object( $post_type );
		if ( ! $obj->has_archive )
			return false;

		return $this->get_archive_controller();
	}

	private function get_taxonomy_controller()
	{
		$term = get_queried_object();

		$controllers = array();

		if ( ! empty( $term->slug ) ) {
			$taxonomy	= $this->format_slug( $term->taxonomy );
			$slug		= $this->format_slug( $term->slug );
			$controllers[] = "Taxonomy$taxonomy$slug";
			$controllers[] = "Taxonomy$taxonomy";
		}
		$controllers[] = 'Taxonomy';

		return $this->get_query_controller( 'taxonomy', $controllers );
	}

	private function get_attachment_controller()
	{
		global $posts;

		if ( ! empty( $posts ) && isset( $posts[0]->post_mime_type ) ) {
			$type = explode( '/', $posts[0]->post_mime_type );

			if ( ! empty( $type ) ) {

				$type[0] = $this->format_slug( $type[0] );
				$type[1] = $this->format_slug( $type[1] );

				if ( $template = $this->get_query_controller( $type[0] ) )
					return $template;
				elseif ( ! empty( $type[1] ) ) {
					if ( $template = $this->get_query_controller( $type[1] ) )
						return $template;
					elseif ( $template = $this->get_query_controller( "$type[0]_$type[1]" ) )
						return $template;
				}
			}
		}

		return $this->get_query_controller( 'Attachment' );
	}

	private function get_single_controller()
	{
		$object = get_queried_object();

		$controllers = array();

		if ( ! empty( $object->post_type ) ) {
			$post_type	= $this->format_slug( $object->post_type );
			$controllers[] = "Single{$post_type}";
		}
		$controllers[] = "Single";

		return $this->get_query_controller( 'Single', $controllers );
	}

	private function get_page_controller()
	{
		$id = get_queried_object_id();
		$template = get_page_template_slug();
		$pagename = get_query_var('pagename');

		if ( ! $pagename && $id ) {
			// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
			$post = get_queried_object();
			if ( $post )
				$pagename = $post->post_name;
		}

		$controllers = array();
		if ( $template && 0 === validate_file( $template ) ) {
			$template	= $this->format_slug( basename( $template, '.php' ) );
			$controllers[] = $template;
		}
		if ( $pagename ) {
			$pagename = $this->format_slug( $pagename );
			$controllers[] = 'Page' . $pagename;
		}
		if ( $id )
			$controllers[] = 'Page' . $id;
		$controllers[] = 'Page';

		return $this->get_query_controller( 'Page', $controllers );
	}

	private function get_category_controller()
	{
		$category = get_queried_object();

		$controllers = array();

		if ( ! empty( $category->slug ) ) {
			$slug		= $this->format_slug( $category->slug );
			$term_id	= $category->term_id;
			$controllers[] = 'Category' . $slug;
			$controllers[] = 'Category' . $term_id;
		}
		$controllers[] = 'Category';

		return $this->get_query_controller( 'Category', $controllers );
	}

	private function get_tag_controller()
	{
		$tag = get_queried_object();

		$controllers = array();

		if ( ! empty( $tag->slug ) ) {
			$slug		= $this->format_slug( $tag->slug );
			$term_id	= $tag->term_id;
			$controllers[] = 'Tag' . $slug;
			$controllers[] = 'Tag' . $term_id;
		}
		$controllers[] = 'Tag';

		return $this->get_query_controller( 'Tag', $controllers );
	}

	private function get_author_controller()
	{
		$author = get_queried_object();

		$controllers = array();

		if ( is_a( $author, 'WP_User' ) ) {
			$user_nicename	= $this->format_slug( $author->user_nicename );
			$ID				= $author->ID;
			$controllers[] = 'Author' . $user_nicename;
			$controllers[] = 'Author' . $ID;
		}
		$controllers[] = 'Author';

		return $this->get_query_controller( 'Author', $controllers );
	}

	private function get_date_controller()
	{
		return $this->get_query_controller('Date');
	}

	private function get_archive_controller()
	{
		$post_types = array_filter( (array) get_query_var( 'post_type' ) );

		$controllers = array();

		if ( count( $post_types ) == 1 ) {
			$post_type = reset( $post_types );
			$post_type	= $this->format_slug( $post_type );
			$controllers[] = 'Archive' . $post_type;
		}
		$controllers[] = 'Archive';

		return $this->get_query_controller( 'Archive', $controllers );
	}

	private function get_comments_popup_controller()
	{
		return $this->get_query_controller('CommentsPopup');
	}

	private function get_paged_controller()
	{
		return $this->get_query_controller('Paged');
	}

	private function get_base_controller()
	{
		return $this->get_query_controller('Base');
	}

	private function get_query_controller( $type, $controllers = array() )
	{
		if ( empty( $controllers ) )
			$controllers = array( $type );

		$controller = $this->locate_controller( $controllers );

		return apply_filters( "{$type}_controller", $controller );
	}

	private function locate_controller( $controller_names )
	{
		$located = false;
		foreach ( (array) $controller_names as $controller_name ) {
			if ( !$controller_name )
				continue;
			if ( file_exists( FABRIC_CONTROLLERS . $controller_name . '.php' ) ) {
				$located = $controller_name;
				break;
			}
		}

		return $located;
	}

	private function format_slug( $slug = false )
	{
		if( empty( $slug ) )
			$slug = basename( get_permalink() );

		$delimeter = array( '-', '_', ' ' );
		$slug_parts = explode( $delimeter[0], str_replace($delimeter, $delimeter[0], $slug ) );
		foreach( $slug_parts as $key => $part )
		{
			$slug_parts[$key] = ucfirst($part);
		}

		$formatted_slug = implode('', $slug_parts);
	 
		if( empty( $formatted_slug ) ) {
			if( is_home() || is_front_page() ) {
				$formatted_slug = 'Home';
			}
		}

		return $formatted_slug;
	}

	private function fabric_autoloader($className)
	{
	    $classNameParts = explode('\\', trim($className, '\\'));

	    if($classNameParts[0] != 'Fabric')
	        return;

	    $fileName = array_pop($classNameParts);

	    include_once FABRIC_CONTROLLERS . $fileName . '.php';
	}

}

add_action( 'fabric_loaded', array( new FabricController, 'init_controller' ) );
add_action( 'wp', array( new FabricController, 'load_controller' ) );
