<?php $view->get_template_part('head'); ?>

<body <?php body_class(); ?>>

	<?php $view->get_header(); ?>

	<!--[if lt IE 9]>
		<div class="alert alert-warning"><?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'fabric'); ?></div>
	<![endif]-->

	<?php if( $view->show_title ) { ?>
		<div class="page-title">
			<h1><?php $view->the_title(); ?></h1>
		</div>
	<?php } ?>

	<?php include fabric_template_path(); ?>

	<?php if( $view->show_sidebar ) {
		$view->get_sidebar();
	} ?>

	<?php $view->get_footer(); ?>

</body>
</html>