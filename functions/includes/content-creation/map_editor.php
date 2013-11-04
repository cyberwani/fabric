<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php printf(__('%s Content Map Editor', 'fabric'), wp_get_theme()); ?></h2>
	<?php settings_errors(); ?>
	<div id="content_map">
		<div id="node_list">
			<?php output_map(json_decode($existing_content_map)) ?>
		</div>
		<div id="node_inspector">
			Inspector
		</div>
	</div>
</div>