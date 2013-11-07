<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php printf(__('%s Content Creation', 'fabric'), wp_get_theme()); ?></h2>
	<?php settings_errors(); ?>

	<?php if($message): ?>
		<div class="updated">
			<p><?php _e( 'Pages have been created!', 'fabric' ); ?></p>
		</div>
	<?php endif; ?>

	<form method="post" action="admin.php?page=fabric-content-creator">
		<?php
			wp_editor(
						null,
						"fabric-content-paste",
						array(
							"media_buttons" => false,
							"tinymce" => array(
								'theme_advanced_buttons1' => "undo,redo,bullist,indent,outdent",
								'theme_advanced_buttons2' => '',
						        'theme_advanced_buttons3' => '',
								'theme_advanced_buttons4' => ''
							)
						)
					);
		?>
		<input type="hidden" name="fabric-action" value="textarea-content-paste">
		<?php submit_button("Create Pages"); ?>
	</form>
</div>