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

class Base
{

	public $show_sidebar = true;

	public $page_type;

	public $google_analytics_id = '';


	public function __construct()
	{
		$this->page_type = $this->page_type();

		$this->show_sidebar = $this->show_sidebar();
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

	private function sidebar_blacklist()
	{
		return array(
			// is_page('ham'),
			// 'book' == get_post_type(),
			// is_category()
		);
	}

	private function show_sidebar()
	{
		if( !$this->show_sidebar )
			return false;

		if( in_array( true, $this->sidebar_blacklist() ) )
			return false;

		return true;
	}

	private function page_type()
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

}