<form role="search" method="get" class="search-form form-inline" action="<?php echo home_url('/'); ?>">
    <input type="search" value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" class="search-field form-control" placeholder="<?php _e( 'Search', 'fabric' ); ?> <?php bloginfo('name'); ?>">
    <button type="submit" class="search-submit btn btn-default"><?php _e('Search', 'fabric'); ?></button>
</form>
