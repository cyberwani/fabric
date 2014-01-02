<?php $view = fabric_controller(); ?>

<?php include $view->the_head(); ?>

<body <?php body_class(); ?>>

	<?php include $view->the_header(); ?>

	<!--[if lt IE 8]>
		<div class="alert alert-warning"><?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'fabric'); ?></div>
	<![endif]-->

	<?php include fabric_template_path(); ?>

	<?php include $view->the_sidebar(); ?>

	<?php include $view->the_footer(); ?>

</body>
</html>