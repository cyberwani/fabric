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

class Base extends Fabric_Controller
{

	public $show_sidebar = true;

	public $show_title = true;

	private $google_analytics_id = '';

	public function __construct()
	{
		$this->show_sidebar = $this->show_sidebar();
		$this->show_title = $this->show_title();
	}

	public function config()
	{
		add_theme_support('menus');
		add_theme_support('post-thumbnails');

		register_nav_menus(array(
			'primary_navigation' => __('Primary Navigation', 'fabric'),
		));

		// length in words for excerpt_length filter
		define('POST_EXCERPT_LENGTH', 40); 
	}

	private function sidebar_blacklist()
	{
		return array();
	}

	private function title_blacklist()
	{
		return array(
			is_front_page()
		);
	}

	public function paged_loop ( $post_type, $additional_args = array() ) {
		return $this->loop( $post_type, $additional_args, true );
	}

	public function loop( $post_type, $additional_args = array(), $paginate = false )
	{
		$post_type = explode(',', $post_type);
		$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

		$args = array(
			'paged'		=> $paged,
			'post_type' => $post_type
		);
		$merged_args = array_merge($args, $additional_args);

		$loop = new \WP_Query( $merged_args );
		if( !empty($loop->posts) ) {
			return new \Fabric_Loop_Iterator( $loop, $paginate );
		} else {
			return array();
		}
	}

	public function google_analytics_tracking()
	{
		if( !$this->google_analytics_id )
			return;
		?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '<?php echo $this->google_analytics_id; ?>']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
		<?php
	}

	public function the_title()
	{
		if (is_home()) {
			if (get_option('page_for_posts', true)) {
				echo get_the_title(get_option('page_for_posts', true));
			} else {
				_e('Latest Posts', 'fabric');
			}
		} elseif (is_archive()) {
			$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
			if ($term) {
				echo $term->name;
			} elseif (is_post_type_archive()) {
				echo get_queried_object()->labels->name;
			} elseif (is_day()) {
				printf(__('Daily Archives: %s', 'fabric'), get_the_date());
			} elseif (is_month()) {
				printf(__('Monthly Archives: %s', 'fabric'), get_the_date('F Y'));
			} elseif (is_year()) {
				printf(__('Yearly Archives: %s', 'fabric'), get_the_date('Y'));
			} elseif (is_author()) {
				$author = get_queried_object();
				printf(__('Author Archives: %s', 'fabric'), $author->display_name);
			} else {
				single_cat_title();
			}
		} elseif (is_search()) {
			printf(__('Search Results for %s', 'fabric'), get_search_query());
		} elseif (is_404()) {
			_e('Not Found', 'fabric');
		} else {
			the_title();
		}
	}

	private function show_sidebar()
	{
		if( !$this->show_sidebar )
			return false;

		if( in_array( true, $this->sidebar_blacklist() ) )
			return false;

		return true;
	}

	private function show_title()
	{
		if( !$this->show_title )
			return false;

		if( in_array( true, $this->title_blacklist() ) )
			return false;

		return true;
	}

}