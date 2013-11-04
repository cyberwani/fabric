<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php printf(__('%s Sitemap', 'fabric'), wp_get_theme()); ?></h2>
	<?php settings_errors(); ?>

	<ul id="sitemap-source" style="display:none;">
		<li>
			Uptrending
			<ul>
				<?php wp_list_pages('title_li='); ?>
			</ul>
		</li>
	</ul>
	<ul id="sitemap">

	</ul>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
	    jQuery("#sitemap-source").jOrgChart({chartElement:"#sitemap"});
	});
</script>