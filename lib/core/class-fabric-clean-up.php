<?php
/**
 * =======================================
 * Fabric Clean Up
 * =======================================
 *
 * 
 * @author Matt Keys <matt@uptrending.com>
 * @version 1.0
 * Based on https://raw.github.com/roots/roots/master/lib/cleanup.php by Roots Theme
 */

class Fabric_Clean_Up
{

	public function init()
	{
		add_action('init', array( $this, 'head_cleanup' ) );

		// Remove the WordPress version from RSS feeds
		add_filter( 'the_generator', '__return_false');

		add_filter( 'language_attributes', array( $this, 'language_attributes' ) );

		add_filter( 'wp_title', array( $this, 'wp_title' ), 10 );

		add_filter( 'style_loader_tag', array( $this, 'clean_style_tag' ) );

		add_filter( 'body_class', array( $this, 'body_class' ) );

		add_filter( 'embed_oembed_html', array( $this, 'embed_wrap' ), 10, 4 );

		add_action( 'admin_init', array( $this, 'remove_dashboard_widgets' ) );

		add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
	
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );

		add_filter( 'get_avatar', array( $this, 'remove_self_closing_tags' ) ); // <img />
		add_filter( 'comment_id_fields', array( $this, 'remove_self_closing_tags' ) ); // <input />
		add_filter( 'post_thumbnail_html', array( $this, 'remove_self_closing_tags' ) ); // <img />

		add_filter( 'get_bloginfo_rss', array( $this, 'remove_default_description' ) );

		if ( get_option( 'fabric-nice-search' ) ) {
		    add_action( 'template_redirect', array( $this, 'nice_search_redirect' ) );
		}

		add_filter( 'request', array( $this, 'request_filter' ) );
	}

	/**
	 * Clean up wp_head()
	 *
	 * Remove unnecessary <link>'s
	 * Remove inline CSS used by Recent Comments widget
	 * Remove inline CSS used by posts with galleries
	 * Remove self-closing tag and change ''s to "'s on rel_canonical()
	 */
	public function head_cleanup() {
	    // Originally from http://wpengineer.com/1438/wordpress-header/
	    remove_action('wp_head', 'feed_links', 2);
	    remove_action('wp_head', 'feed_links_extra', 3);
	    remove_action('wp_head', 'rsd_link');
	    remove_action('wp_head', 'wlwmanifest_link');
	    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
	    remove_action('wp_head', 'wp_generator');
	    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

	    global $wp_widget_factory;
	    remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));

	    if (!class_exists('WPSEO_Frontend')) {
	        remove_action('wp_head', 'rel_canonical');
	        add_action('wp_head', array( $this, 'rel_canonical' ) );
	    }
	}

	public function rel_canonical() {
	    global $wp_the_query;

	    if (!is_singular()) {
	        return;
	    }

	    if (!$id = $wp_the_query->get_queried_object_id()) {
	        return;
	    }

	    $link = get_permalink($id);
	    echo "\t<link rel=\"canonical\" href=\"$link\">\n";
	}

	/**
	 * Clean up language_attributes() used in <html> tag
	 *
	 * Remove dir="ltr"
	 */
	public function language_attributes() {
	    $attributes = array();
	    $output = '';

	    if (is_rtl()) {
	        $attributes[] = 'dir="rtl"';
	    }

	    $lang = get_bloginfo('language');

	    if ($lang) {
	        $attributes[] = "lang=\"$lang\"";
	    }

	    $output = implode(' ', $attributes);
	    $output = apply_filters('fabric_language_attributes', $output);

	    return $output;
	}

	/**
	 * Manage output of wp_title()
	 */
	public function wp_title($title) {
	    if (is_feed()) {
	        return $title;
	    }

	    $title .= get_bloginfo('name');

	    return $title;
	}

	/**
	 * Clean up output of stylesheet <link> tags
	 */
	public function clean_style_tag($input) {
	    preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
	    // Only display media if it is meaningful
	    $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
	    return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
	}

	/**
	 * Add and remove body_class() classes
	 */
	public function body_class($classes) {
	    // Add post/page slug
	    if (is_single() || is_page() && !is_front_page()) {
	        $classes[] = basename(get_permalink());
	    }

	    // Current controller
	    $classes[] = 'controller-' . FABRIC_CONTROLLER;

	    // Remove unnecessary classes
	    $home_id_class = 'page-id-' . get_option('page_on_front');
	    $remove_classes = array(
	        'page-template-default',
	        $home_id_class
	    );
	    $classes = array_diff($classes, $remove_classes);

	    return $classes;
	}

	/**
	 * Wrap embedded media as suggested by Readability
	 *
	 * @link https://gist.github.com/965956
	 * @link http://www.readability.com/publishers/guidelines#publisher
	 */
	public function embed_wrap($cache, $url, $attr = '', $post_ID = '') {
	    return '<div class="entry-content-asset">' . $cache . '</div>';
	}

	/**
	 * Remove unnecessary dashboard widgets
	 *
	 * @link http://www.deluxeblogtips.com/2011/01/remove-dashboard-widgets-in-wordpress.html
	 */
	public function remove_dashboard_widgets() {
	    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
	    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
	    remove_meta_box('dashboard_primary', 'dashboard', 'normal');
	    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
	}

	/**
	 * Clean up the_excerpt()
	 */
	public function excerpt_length($length) {
	    return POST_EXCERPT_LENGTH;
	}

	public function excerpt_more($more) {
	    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'fabric') . '</a>';
	}

	/**
	 * Remove unnecessary self-closing tags
	 */
	function remove_self_closing_tags($input) {
	    return str_replace(' />', '>', $input);
	}

	/**
	 * Don't return the default description in the RSS feed if it hasn't been changed
	 */
	function remove_default_description($bloginfo) {
	    $default_tagline = 'Just another WordPress site';
	    return ($bloginfo === $default_tagline) ? '' : $bloginfo;
	}

	/**
	 * Redirects search results from /?s=query to /search/query/, converts %20 to +
	 *
	 * @link http://txfx.net/wordpress-plugins/nice-search/
	 */
	function nice_search_redirect() {
	    global $wp_rewrite;
	    if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks()) {
	        return;
	    }

	    $search_base = $wp_rewrite->search_base;
	    if (is_search() && !is_admin() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false) {
	        wp_redirect( home_url( "/{$search_base}/" . urlencode( get_query_var('s') ) ) );
	        exit();
	    }
	}

	/**
	 * Fix for empty search queries redirecting to home page
	 *
	 * @link http://wordpress.org/support/topic/blank-search-sends-you-to-the-homepage#post-1772565
	 * @link http://core.trac.wordpress.org/ticket/11330
	 */
	function request_filter($query_vars) {
	    if (isset($_GET['s']) && empty($_GET['s'])) {
	        $query_vars['s'] = ' ';
	    }

	    return $query_vars;
	}

}

add_action( 'fabric_loaded', array( new Fabric_Clean_Up, 'init' ) );
