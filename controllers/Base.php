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

class Base extends FabricController
{

	public $show_sidebar = true;

	public $google_analytics_id = '';


	public function __construct()
	{
		$this->show_sidebar = $this->show_sidebar();
	}

	private function sidebar_blacklist()
	{
		return array(
			// is_page('slug'),
			// 'book' == get_post_type(),
			// is_category()
		);
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

	private function show_sidebar()
	{
		if( !$this->show_sidebar )
			return false;

		if( in_array( true, $this->sidebar_blacklist() ) )
			return false;

		return true;
	}

}