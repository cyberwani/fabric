<?php

function fabric_get_front_page_template() {
	$templates = array('views/front-page.php');

	$template = locate_template( $templates );
	return $template;
}
add_filter( 'front_page_template', 'fabric_get_front_page_template', 10 );

function fabric_get_home_template() {
	$templates = array( 'views/home.php', 'views/index.php' );

	$template = locate_template( $templates );
	return $template;
}
add_filter( 'home_template', 'fabric_get_home_template', 10 );

function fabric_get_taxonomy_template() {
	$term = get_queried_object();

	$templates = array();

	if ( ! empty( $term->slug ) ) {
		$taxonomy = $term->taxonomy;
		$templates[] = "views/taxonomy-$taxonomy-{$term->slug}.php";
		$templates[] = "views/taxonomy-$taxonomy.php";
	}
	$templates[] = 'views/taxonomy.php';

	$template = locate_template( $templates );
	return $template;
}
add_filter( 'taxonomy_template', 'fabric_get_taxonomy_template', 10 );

function fabric_get_single_template() {
	$object = get_queried_object();

	$templates = array();

	if ( ! empty( $object->post_type ) )
		$templates[] = "views/single-{$object->post_type}.php";
	$templates[] = "views/single.php";

	$template = locate_template( $templates );
	return $template;
}
add_filter( 'single_template', 'fabric_get_single_template', 10 );

function fabric_get_page_template() {
	$id = get_queried_object_id();

	$template = get_page_template_slug();
	$pagename = get_query_var('pagename');

	if ( ! $pagename && $id ) {
		// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
		$post = get_queried_object();
		if ( $post )
			$pagename = $post->post_name;
	}
	$templates = array();
	if ( $template && 0 === validate_file( $template ) )
	       $templates[] = $template;
	if ( $pagename )
	       $templates[] = "views/page-$pagename.php";
	if ( $id )
	       $templates[] = "views/page-$id.php";
	$templates[] = 'views/page.php';

	$template = locate_template($templates);

	return $template;
}
add_filter( 'page_template', 'fabric_get_page_template', 10 );

function fabric_get_category_template() {
	$category = get_queried_object();

	$templates = array();

	if ( ! empty( $category->slug ) ) {
		$templates[] = "views/category-{$category->slug}.php";
		$templates[] = "views/category-{$category->term_id}.php";
	}
	$templates[] = 'views/category.php';

	$template = locate_template( $templates );
	return $template;
}
add_filter( 'category_template', 'fabric_get_category_template', 10 );

function fabric_get_tag_template() {
	$tag = get_queried_object();

	$templates = array();

	if ( ! empty( $tag->slug ) ) {
		$templates[] = "views/tag-{$tag->slug}.php";
		$templates[] = "views/tag-{$tag->term_id}.php";
	}
	$templates[] = 'views/tag.php';

	$template = locate_template( $templates );
	return $template;
}
add_filter( 'tag_template', 'fabric_get_tag_template', 10 );

function fabric_get_author_template() {
	$author = get_queried_object();

	$templates = array();

	if ( is_a( $author, 'WP_User' ) ) {
		$templates[] = "views/author-{$author->user_nicename}.php";
		$templates[] = "views/author-{$author->ID}.php";
	}
	$templates[] = 'views/author.php';

	$template = locate_template( $templates );
	return $template;
}
add_filter( 'author_template', 'fabric_get_author_template', 10 );

function fabric_get_archive_template() {
	$post_types = array_filter( (array) get_query_var( 'post_type' ) );

	$templates = array();

	if ( count( $post_types ) == 1 ) {
		$post_type = reset( $post_types );
		$templates[] = "views/archive-{$post_type}.php";
	}
	$templates[] = 'views/archive.php';

	$template = locate_template( $templates );
	return $template;
}
add_filter( 'archive_template', 'fabric_get_archive_template', 10 );

function fabric_get_attachment_template() {
	global $posts;

	if ( ! empty( $posts ) && isset( $posts[0]->post_mime_type ) ) {
		$type = explode( '/', $posts[0]->post_mime_type );

		if ( ! empty( $type ) ) {
			if ( $template = fabric_get_type_template( $type[0] ) )
				return $template;
			elseif ( ! empty( $type[1] ) ) {
				if ( $template = fabric_get_type_template( $type[1] ) )
					return $template;
				elseif ( $template = fabric_get_type_template( "$type[0]_$type[1]" ) )
					return $template;
			}
		}
	}

	return fabric_get_type_template( 'attachment' );
}
add_filter( 'attachment_template', 'fabric_get_attachment_template', 10 );

function fabric_get_comments_popup_template() {
	$template = locate_template( array( 'views/comments-popup.php' ) );

	// Backward compat code will be removed in a future release
	if ('' == $template)
		$template = ABSPATH . WPINC . '/theme-compat/comments-popup.php';

	return $template;
}
add_filter( 'comments_popup_template', 'fabric_get_comments_popup_template', 10 );

function fabric_get_404_template() {
	return fabric_get_type_template('404');
}
add_filter( '404_template', 'fabric_get_404_template', 10 );

function fabric_get_search_template() {
	return fabric_get_type_template('search');
}
add_filter( 'search_template', 'fabric_get_search_template', 10 );

function fabric_get_date_template() {
	return fabric_get_type_template('date');
}
add_filter( 'date_template', 'fabric_get_date_template', 10 );

function fabric_get_paged_template() {
	return fabric_get_type_template('paged');
}
add_filter( 'paged_template', 'fabric_get_paged_template', 10 );

function fabric_get_index_template() {
	return fabric_get_type_template('index');
}
add_filter( 'index_template', 'fabric_get_index_template', 10 );

function fabric_get_type_template($type) {
	$templates = array("views/{$type}.php");

	$template = locate_template( $templates );
	return $template;
}