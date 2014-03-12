<?php if ( ! have_posts() ) : ?>
  <div class="alert alert-warning">
    <?php _e( 'Sorry, no results were found.', 'fabric' ); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php while ( have_posts() ) : the_post(); ?>

  <article <?php post_class(); ?>>
    <header>
      <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
      <?php $view->get_template_part('entry-meta'); ?>
    </header>
    <div class="entry-summary">
      <?php the_excerpt(); ?>
    </div>
  </article>

<?php endwhile; ?>

<?php if ($wp_query->max_num_pages > 1) : ?>
  <nav class="post-nav">
    <ul class="pager">
      <li class="previous"><?php next_posts_link( __( '&larr; Older posts', 'fabric' ) ); ?></li>
      <li class="next"><?php previous_posts_link( __( 'Newer posts &rarr;', 'fabric' ) ); ?></li>
    </ul>
  </nav>
<?php endif; ?>