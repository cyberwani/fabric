<?php

// Supports

add_theme_support('menus');
add_theme_support('post-thumbnails');

register_nav_menus(array(
	'primary_navigation' => __('Primary Navigation', 'fabric'),
));

define('POST_EXCERPT_LENGTH', 40); // Length in words for excerpt_length filter (http://codex.wordpress.org/Plugin_API/Filter_Reference/excerpt_length)