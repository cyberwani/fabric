<?php
	
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			echo get_post_type();
			//
			// Post Content here
			the_content();
			//
		} // end while
	} // end if
?>